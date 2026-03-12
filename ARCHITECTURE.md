# ClinicDesq — Architecture Document

> **Framework:** Laravel 12 (PHP)
> **Frontend:** Blade + Tailwind CSS + Vite
> **Auth:** Laravel Breeze (staff) + custom `auth:vet` guard
> **AI:** OpenAI GPT-4.1 via HTTP
> **Last updated:** 2026-03-12

---

## Table of Contents

1. [System Overview](#1-system-overview)
2. [User Roles & Access Model](#2-user-roles--access-model)
3. [Authentication System](#3-authentication-system)
4. [Route Architecture](#4-route-architecture)
5. [Controller Layer](#5-controller-layer)
6. [Data Model & Relationships](#6-data-model--relationships)
7. [Database Schema](#7-database-schema)
8. [RBAC & Permission System](#8-rbac--permission-system)
9. [Multi-Tenancy Model](#9-multi-tenancy-model)
10. [AI Integration](#10-ai-integration)
11. [Pet Parent Access & Privacy Model](#11-pet-parent-access--privacy-model)
12. [Inventory & Pricing System](#12-inventory--pricing-system)
13. [Appointment & Clinical Workflow](#13-appointment--clinical-workflow)
14. [Middleware Stack](#14-middleware-stack)
15. [Frontend Architecture](#15-frontend-architecture)
16. [Known Architectural Issues](#16-known-architectural-issues)

---

## 1. System Overview

ClinicDesq is a multi-tenant veterinary clinic management platform. It supports multiple organisations, each operating one or more clinics, with vets working across clinics and clinic staff managing operations.

```
Organisation
  └── Clinics (many)
        ├── Staff Users (many-to-many via clinic_user_assignments)
        └── Vets (many-to-many via clinic_vet)
              └── Appointments
                    ├── CaseSheet (1:1)
                    ├── Prescription → PrescriptionItems (1:many)
                    ├── DiagnosticReports → DiagnosticFiles (1:many)
                    ├── AppointmentTreatments (1:many)
                    └── Bill → BillItems (1:1 / 1:many)

PetParent → Pets → Appointments
DrugGeneric → DrugBrand → DrugDosage
Organisation → InventoryItems → InventoryBatches
Organisation → PriceList → PriceListItems
```

---

## 2. User Roles & Access Model

There are **two distinct user types** with separate database tables and auth guards.

### Staff Users (`users` table — `auth` guard)

| Role | Level | Description |
|---|---|---|
| `superadmin` | Platform | Full system access, manages all organisations |
| `organisation_owner` | Org | Full org access, bypasses permission checks |
| `regional_manager` | Org | Org-level staff with assigned permissions |
| `area_manager` | Org | Org-level staff with assigned permissions |
| `clinic_manager` | Clinic | Manages a specific clinic |
| `receptionist` | Clinic | Front-desk, appointment booking |
| `sales` | Clinic | Billing-focused role |

Staff users are assigned to organisations via `organisation_id` on the `users` table.
Clinic-level staff are assigned to specific clinics via the `clinic_user_assignments` pivot table.

### Vets (`vets` table — `auth:vet` guard)

Vets are a completely separate authenticatable entity. They:
- Have their own login at `/vet/login`
- Maintain their own session (`auth:vet`)
- Are associated with one or more clinics via the `clinic_vet` pivot table
- Select an "active clinic" per session (`session('active_clinic_id')`)
- Cannot see pet parent PII (enforced at model level)

---

## 3. Authentication System

Two independent auth systems run in parallel.

### Staff Auth (Breeze-based)
- **Login:** `GET/POST /staff/login` → `RoleLoginController@staffLogin`
- **Guard:** `web` (default Laravel session guard)
- **Model:** `App\Models\User` (extends `Authenticatable`)
- **Table:** `users`
- **Post-login redirect:** determined by `role` field on User

### Vet Auth (Custom)
- **Login:** `GET /vet/login` → `VetLoginController@showLoginForm`, `POST` → `@login`
- **Guard:** `vet` (custom guard configured in `config/auth.php`)
- **Model:** `App\Models\Vet` (extends `Authenticatable`)
- **Table:** `vets`
- **Logout:** `POST /vet/logout`
- **Session key:** `active_clinic_id` stores selected working clinic

### Route `guest` Guards
- Staff guest routes use: `middleware('guest')`
- Vet guest routes use: `middleware('guest:vet')`

These are distinct — a logged-in vet is not considered a "guest" for staff routes and vice versa.

---

## 4. Route Architecture

Routes are defined in `routes/web.php` and organized into four prefix-based groups.

### `/admin/*` — Superadmin Portal
- **Middleware:** `auth`, `role:superadmin`
- **Manages:** Organisations, Clinics, Vets (global), Drug catalog

### `/organisation/*` — Organisation Portal
- **Middleware:** `auth` + `permission:<slug>` per sub-group
- **Manages:** Clinics, Users, Roles & Permissions, Price Lists, Inventory, Vets

Sub-group permission requirements:

| Routes | Permission Required |
|---|---|
| Dashboard, clinics list, users list | `dashboard.view` |
| Create/edit roles | `roles.manage` |
| Create clinics | `clinics.manage` |
| Create/edit users | `users.manage` |
| Price lists | `pricing.manage` |
| Inventory | `inventory.manage` |
| Assign vets to clinics | `vets.assign` |

### `/clinic/*` — Clinic Portal
- **Middleware:** `auth`, `permission:appointments.view`
- **Manages:** Appointments (view, create, status update, reschedule), Billing

### `/vet/*` — Vet Portal
- **Middleware:** `auth:vet`
- **Manages:** Dashboard, Profile, Clinic selection, full appointment clinical workflow

#### Vet AI Routes (no prefix group, appended at end of file)
- `POST /vet/ai/refine`
- `POST /vet/ai/clinical-insights`
- `POST /vet/ai/senior-support/{appointment}`
- `POST /vet/ai/prescription-support/{appointment}`
- `POST /appointments/{appointment}/ai/prescription`
- `PUT /diagnostics/files/{file}/ai-summary`
- `POST /diagnostics/files/{file}/verify`

---

## 5. Controller Layer

### Admin Controllers (`app/Http/Controllers/Admin/`)

| Controller | Responsibility |
|---|---|
| `AdminDashboardController` | Admin home |
| `OrganisationController` | Create/list organisations |
| `ClinicController` | Create/edit/list clinics (admin view) |
| `VetController` | Create/list vets (global) |
| `DrugController` | Global drug catalog: generics, brands, dosages |

### Organisation Controllers (`app/Http/Controllers/Organisation/`)

| Controller | Responsibility |
|---|---|
| `OrganisationDashboardController` | Org overview: clinics, vet counts |
| `OrganisationClinicController` | Create/list clinics within org |
| `OrganisationUserController` | Create/edit/assign users within org |
| `OrganisationRoleController` | Create/edit roles with permission sets |
| `OrganisationVetController` | View/assign/offboard vets to clinics |
| `PriceListController` | Create/edit price lists and items |
| `InventoryController` | Manage inventory items, batches, stock |

### Clinic Controllers (`app/Http/Controllers/Clinic/`)

| Controller | Responsibility |
|---|---|
| `ClinicDashboardController` | Clinic home (currently unused/commented) |
| `ClinicAppointmentController` | Appointment list, create, status, reschedule |
| `BillingController` | Billing form for appointment |

### Vet Controllers (`app/Http/Controllers/Vet/`)

| Controller | Responsibility |
|---|---|
| `Auth/VetLoginController` | Vet login/logout |
| `VetDashboardController` | Vet home, clinic selection |
| `VetClinicController` | Vet's view of their active clinic's appointments |
| `VetProfileController` | (formerly `VetLoginController`) Profile show/edit |
| `AppointmentController` | Full appointment workflow: create, case, treatment, prescription, case sheet |
| `PetParentController` | Create pet parent records |
| `PetParentProfileController` | View pet parent profile |
| `PetController` | Create pet records |
| `PetProfileController` | View pet profile |
| `VetAppointmentHistoryController` | Vet's own appointment history |
| `VetPetHistoryController` | Cross-clinic pet history search (OTP-gated) |
| `DiagnosticController` | Upload, extract, edit, view, download diagnostic files |
| `DiagnosticReportController` | Report-level management |
| `VetAiController` | All AI-powered clinical assistance endpoints |

---

## 6. Data Model & Relationships

### Core Domain Model

```
Organisation (1)
  └── Clinic (many)           [FK: organisation_id]
        ├── ClinicUserAssignment (many) [pivot: user_id, clinic_id]
        └── clinic_vet (pivot)          [vet_id, clinic_id, role]

User (many)                    [FK: organisation_id, clinic_id]
  └── OrganisationUserRole (many) [FK: user_id, role_id, clinic_id, organisation_id]

OrganisationRole (many)        [FK: organisation_id]
  └── role_permissions (pivot) [role_id, permission_id]
        └── Permission

Vet (many)
  └── clinic_vet (pivot) → Clinic

PetParent (1)
  └── Pet (many)               [FK: pet_parent_id]
        └── Appointment (many) [FK: pet_id, pet_parent_id, clinic_id, vet_id]
              ├── CaseSheet (1:1)           [FK: appointment_id]
              ├── Prescription (1:1)        [FK: appointment_id]
              │     └── PrescriptionItem (many) [FK: prescription_id]
              ├── DiagnosticReport (many)   [FK: appointment_id, pet_id, clinic_id, vet_id]
              │     └── DiagnosticFile (many)   [FK: diagnostic_report_id]
              ├── AppointmentTreatment (many)   [FK: appointment_id, price_list_item_id]
              └── Bill (1:1)                [FK: appointment_id, clinic_id]
                    └── BillItem (many)     [FK: bill_id, price_list_item_id]

Organisation → InventoryItem (many) [FK: organisation_id]
  └── InventoryBatch (many)   [FK: inventory_item_id, clinic_id]

Organisation → PriceList (many) [FK: organisation_id]
  └── PriceListItem (many)    [FK: price_list_id, drug_brand_id?, inventory_item_id?]

DrugGeneric (1)
  ├── DrugDosage (many)       [FK: generic_id, species-specific]
  └── DrugBrand (many)        [FK: generic_id]
```

### Model Summary Table

| Model | Table | Key Relationships |
|---|---|---|
| `Organisation` | `organisations` | has many Clinics |
| `Clinic` | `clinics` | belongsTo Organisation, belongsToMany Vets, belongsToMany Users |
| `User` | `users` | belongsTo Organisation, belongsTo Clinic, belongsToMany Clinics (assignments) |
| `Vet` | `vets` | belongsToMany Clinics |
| `PetParent` | `pet_parents` | has many Pets |
| `Pet` | `pets` | belongsTo PetParent, has many Appointments |
| `Appointment` | `appointments` | belongsTo Pet, Clinic, Vet; has one CaseSheet, Prescription, Bill; has many Treatments, DiagnosticReports |
| `CaseSheet` | `case_sheets` | belongsTo Appointment |
| `Prescription` | `prescriptions` | belongsTo Appointment; has many PrescriptionItems |
| `PrescriptionItem` | `prescription_items` | belongsTo Prescription |
| `DiagnosticReport` | `diagnostic_reports` | belongsTo Appointment; has many DiagnosticFiles |
| `DiagnosticFile` | `diagnostic_files` | belongsTo DiagnosticReport; has static helpers for summary queries |
| `AppointmentTreatment` | `appointment_treatments` | belongsTo Appointment, PriceListItem, DrugGeneric, DrugBrand |
| `Bill` | `bills` | belongsTo Appointment; has many BillItems |
| `BillItem` | `bill_items` | belongsTo Bill, PriceListItem |
| `InventoryItem` | `inventory_items` | belongsTo Organisation, DrugBrand; has many Batches |
| `InventoryBatch` | `inventory_batches` | belongsTo InventoryItem, Clinic |
| `PriceList` | `price_lists` | belongsTo Organisation; has many PriceListItems |
| `PriceListItem` | `price_list_items` | belongsTo PriceList, DrugBrand?, InventoryItem? |
| `DrugGeneric` | `drug_generics` | has many DrugDosages, DrugBrands |
| `DrugBrand` | `drug_brands` | belongsTo DrugGeneric |
| `DrugDosage` | `drug_dosages` | belongsTo DrugGeneric; species-specific dose ranges |
| `OrganisationRole` | `organisation_roles` | belongsTo Organisation; belongsToMany Permissions |
| `OrganisationUserRole` | `organisation_user_roles` | belongsTo User, OrganisationRole, Clinic |
| `Permission` | `permissions` | belongsToMany OrganisationRoles |
| `ClinicUserAssignment` | `clinic_user_assignments` | pivot: User ↔ Clinic |
| `PetParentClinicAccess` | `pet_parent_clinic_access` | tracks which clinics have access to a pet parent |
| `PetParentAccessOtp` | `pet_parent_access_otps` | OTP records for cross-clinic pet parent access |

---

## 7. Database Schema

### Key migration files and what they create

| Migration | Creates |
|---|---|
| `0001_01_01_000000` | `users`, `password_reset_tokens`, `sessions` |
| `0001_01_01_000001` | `cache`, `cache_locks` |
| `0001_01_01_000002` | `jobs`, `job_batches`, `failed_jobs` |
| `2026_02_16_*_create_clinics_table` | `clinics` |
| `2026_02_16_*_create_vets_table` | `vets` |
| `2026_02_16_*_create_clinic_vet_table` | `clinic_vet` (pivot) |
| `2026_02_17_170848` | Adds profile fields to `vets` (degree, skills, certifications, experience) |
| `2026_02_17_173225` | `appointments` |
| `2026_02_17_174820` | `pet_parents` |
| `2026_02_17_175321` | `pets` |
| `2026_02_17_183825` | `pet_parent_clinic_access` |
| `2026_02_17_183908` | `pet_parent_access_otps` |
| `2026_02_17_220605` | `prescriptions` |
| `2026_02_17_220726` | `prescription_items` |
| `2026_02_17_225219` | Updates `case_sheets` table |
| `2026_02_19_164749` | `clinic_user_assignments` (with cascadeOnDelete on user_id and clinic_id) |

Additional tables referenced in code but not in visible migrations (likely created in separate migrations):
- `organisations`
- `organisation_roles`
- `organisation_user_roles`
- `role_permissions`
- `permissions`
- `drug_generics`, `drug_brands`, `drug_dosages`
- `inventory_items`, `inventory_batches`
- `price_lists`, `price_list_items`
- `case_sheets`
- `bills`, `bill_items`
- `appointment_treatments`
- `diagnostic_reports`, `diagnostic_files`

### Notable Schema Decisions

- `pet_age_at_visit` on `appointments`: stored as float (years). Calculated dynamically from `Pet.age` + `Pet.age_recorded_at` on display via `Appointment::getCalculatedAgeAtVisitAttribute()`.
- `Pet.age` + `Pet.age_months` + `Pet.age_recorded_at`: age is recorded at a point in time and calculated forward from that date.
- `DiagnosticFile.status`: workflow states — `pending` → `extracted` → `human_verified`. AI summaries are only used in clinical AI prompts once status is `human_verified`.
- `PetParentClinicAccess.revoked_at`: nullable timestamp; null means access is currently granted.
- `InventoryBatch.clinic_id`: nullable. Null = organisation-level (central) stock.

---

## 8. RBAC & Permission System

### Two-layer access control

**Layer 1 — Role gate** (coarse, for admin routes)
- `RoleMiddleware` checks the `role` column on the `User` model
- Used only for the `superadmin` guard on `/admin/*` routes
- `role:superadmin` in route definition

**Layer 2 — Permission gate** (fine-grained, for org/clinic routes)
- `CheckPermission` middleware reads a permission slug from the route definition
- Calls `User::hasPermission($slug)` which queries `organisation_user_roles` → `role_permissions` → `permissions`
- `organisation_owner` and `organisation_admin` bypass all permission checks

### Permission slugs in use

| Slug | Used For |
|---|---|
| `dashboard.view` | Organisation dashboard access |
| `clinics.manage` | Create clinics |
| `users.manage` | Create/edit users |
| `users.view` | View users |
| `roles.manage` | Create/edit roles |
| `pricing.manage` | Price list management |
| `inventory.manage` | Inventory management |
| `vets.assign` | Assign vets to clinics |
| `appointments.view` | Clinic appointment access |

### Permission data flow

```
User
  └── OrganisationUserRole (user_id, role_id, clinic_id, organisation_id)
        └── OrganisationRole (organisation_id, name)
              └── role_permissions (role_id, permission_id)
                    └── Permission (slug)
```

`User::hasPermission($slug)` — queries this chain using a join, returns bool.

---

## 9. Multi-Tenancy Model

ClinicDesq uses a **shared database, scoped queries** approach to multi-tenancy. There is no database-per-tenant or schema-per-tenant isolation.

### Tenant isolation is enforced by:

1. **`organisation_id` on `users`** — staff only see data for their organisation
2. **`organisation_id` on `clinics`** — clinics belong to exactly one organisation
3. **`organisation_id` on `inventory_items`, `price_lists`, `organisation_roles`** — org-scoped resources
4. **`clinic_id` on `appointments`, `bills`, `diagnostic_reports`** — clinic-scoped records
5. **`clinic_user_assignments`** — gates which staff users can access which clinic

### Vet multi-clinic model

A vet can belong to multiple clinics. They select their **active clinic** at the start of each session via `POST /vet/select-clinic/{clinic}`. This stores `active_clinic_id` in the session. All vet queries scope to this clinic.

---

## 10. AI Integration

### Service: `AiClinicalService`

Located at `app/Services/AiClinicalService.php`. All AI calls go via a single private `callOpenAi()` method using Laravel's `Http` facade against the OpenAI Chat Completions API.

**Model used:** `gpt-4.1`
**Temperature:** 0.2 default, 0.3 for text refinement

### Methods

| Method | Endpoint | Purpose |
|---|---|---|
| `refine(field, text)` | `POST /vet/ai/refine` | Rewrites clinical notes into professional language without adding information |
| `clinicalInsights(caseData)` | `POST /vet/ai/clinical-insights` | Live case review by a "senior vet" persona from unsaved case sheet fields |
| `seniorVetGuidance(context)` | `POST /vet/ai/senior-support/{appointment}` | Full case context review including past history, previous diagnostics, and current case sheet |
| `prescriptionSupport(context)` | `POST /vet/ai/prescription-support/{appointment}` | Drug selection guidance from case context + verified diagnostic summaries |
| `prescriptionAI` | `POST /appointments/{appointment}/ai/prescription` | Prescription-specific AI (via `VetAiController@prescriptionAI`) |

### Context assembly (`VetAiController`)

For `seniorVetSupport` and `prescriptionSupport`, the controller assembles a rich context object including:
- Pet species, breed, gender, age, weight
- Current live case sheet data (from request, not saved)
- Past appointment history (last N visits)
- Prior prescriptions
- **Verified diagnostic file summaries** (`DiagnosticFile::verifiedSummariesForAppointment()`) — only `human_verified` files with non-null `ai_summary` are included

### Diagnostic file pipeline

```
Upload (DiagnosticController@store)
  → File stored to disk
  → Text extracted (PDF parser / OCR)
  → status = 'extracted'
  → AI generates summary (AiClinicalService)
  → status = 'ai_summarized'

Vet reviews and verifies (DiagnosticController@verifyFile)
  → status = 'human_verified'
  → Summary now eligible for inclusion in AI clinical prompts
```

---

## 11. Pet Parent Access & Privacy Model

### Privacy design

Vets are **explicitly prevented from seeing pet parent PII**:
- `User::canViewPetPII()` returns `false` for vet role
- This is a design rule, not yet a middleware/policy gate

### Cross-clinic pet history

Pet parents may have visited multiple clinics. A vet at Clinic B wanting to see a patient's history from Clinic A must go through an **OTP verification flow**:

```
Vet searches pet by phone → POST /pet-history
  → If pet parent not associated with vet's clinic:
      → Vet requests access → PetParentAccessOtp created, OTP sent to owner
      → Vet enters OTP → GET /vet/appointments/{appointment}/request_access
      → On success → PetParentClinicAccess created (granted_at set)
      → Vet can now view cross-clinic history
```

`PetParentClinicAccess` records which clinics have been granted access, with `revoked_at` for soft revocation.

---

## 12. Inventory & Pricing System

### Inventory

- **`InventoryItem`** — master product catalog, scoped per organisation (`organisation_id`)
  - `item_type`: drug or consumable
  - Links to `DrugBrand` for drug items
  - `track_inventory`, `is_multi_use` flags for billing behavior
- **`InventoryBatch`** — stock batches per item
  - `clinic_id` nullable: null = central/org-level stock, set = clinic-specific stock
  - Tracks `expiry_date`, `quantity`, `purchase_price`

### Drug catalog (global)

The drug catalog is **global, not org-scoped**:
- `DrugGeneric` — generic drug names with drug class and default dose unit
- `DrugBrand` — branded products linked to a generic (strength, form, pack)
- `DrugDosage` — species-specific dosing ranges (min/max mg/kg, routes, frequencies) as JSON arrays

This catalog is managed by superadmin and is shared across all organisations.

### Pricing

- `PriceList` — per organisation, named list (e.g. "Standard 2026"), one active at a time
- `PriceListItem` — individual line items:
  - `item_type`: `drug`, `service`, `procedure`, `consumable`
  - `billing_type`: per unit, flat, etc.
  - Links to `DrugBrand` and/or `InventoryItem`
- Only one `PriceList` can be `is_active = true` per organisation at a time (enforced in `PriceListController@activate`)

### Treatment billing flow

```
Vet adds treatment to appointment
  → selects PriceListItem (from org's active price list)
  → AppointmentTreatment created with drug details, dose, route, billing_quantity

Clinic staff creates bill
  → BillingController@create loads appointment treatments
  → Bill + BillItems created with price snapshot from PriceListItem
```

---

## 13. Appointment & Clinical Workflow

### Full appointment lifecycle

```
1. BOOKING
   Vet or Clinic Staff creates appointment
     → Searches for PetParent by phone
     → If new: creates PetParent + Pet
     → If existing: selects pet
     → Sets scheduled_at, clinic_id, vet_id
     → status = 'scheduled'

2. CHECK-IN
   Clinic staff or vet marks appointment as active
     → status = 'in_progress' (or similar)

3. CLINICAL WORKSPACE (Vet only)
   GET /vet/appointments/{appointment}/case
     → Loads: pet, pet_parent, case_sheet, prescription, treatments, diagnostics
     → This is the main clinical interface

   3a. Case Sheet
       GET/POST /vet/appointments/{appointment}/case-sheet
         → CaseSheet: presenting_complaint, history, clinical_examination,
                       differentials, diagnosis, treatment_given, procedures_done,
                       further_plan, advice

   3b. Treatments
       POST /vet/appointments/{appointment}/treatment/add
         → AppointmentTreatment: links PriceListItem, DrugGeneric, DrugBrand,
                                  dose_mg, dose_volume_ml, route, billing_quantity

   3c. Prescription
       GET/POST /vet/appointments/{appointment}/prescription/create
         → Prescription + PrescriptionItems: medicine, dosage, frequency, duration

   3d. Diagnostics
       GET /vet/appointments/{appointment}/diagnostics/create
         → Upload files → extract text → AI summary → human verify

   3e. AI Assistance (throughout)
       → Refine text (any field)
       → Clinical insights (unsaved case data)
       → Senior vet support (full context including history)
       → Prescription support (verified diagnostics required)

4. BILLING
   GET /clinic/appointments/{appointment}/billing
     → BillingController loads treatments for bill creation
     → Bill + BillItems created

5. COMPLETE
   POST /vet/clinic/appointments/{appointment}/complete
     → status = 'completed'
```

---

## 14. Middleware Stack

| Middleware | Class | Purpose |
|---|---|---|
| `auth` | `Authenticate` | Default Laravel auth guard check |
| `auth:vet` | `Authenticate` (with vet guard) | Vet guard check |
| `guest` | `RedirectIfAuthenticated` | Redirect authenticated users away from login |
| `guest:vet` | `RedirectIfAuthenticated` | Same for vet guard |
| `role:superadmin` | `RoleMiddleware` | Checks `users.role` column value |
| `permission:<slug>` | `CheckPermission` | Calls `User::hasPermission($slug)` |
| `clinic.user` | `EnsureClinicUser` | (Defined, usage not confirmed in routes) |
| `staff.role` | `EnsureStaffRole` | (Defined, usage not confirmed in routes) |

### `CheckPermission` logic
1. Read permission slug from route middleware parameter
2. Get authenticated user
3. If `organisation_owner` or `organisation_admin` → allow
4. Query `OrganisationUserRole` → `role_permissions` → `permissions` for slug match
5. Deny with 403 if not found

---

## 15. Frontend Architecture

- **Templating:** Laravel Blade with component-based layout
- **CSS:** Tailwind CSS (JIT via Vite)
- **Build:** Vite (`vite.config.js`)
- **JS:** Vanilla JS / Alpine.js (inline in Blade files)

### Layout files

| Layout | Used By |
|---|---|
| `resources/views/layouts/app.blade.php` | General auth pages |
| `resources/views/layouts/guest.blade.php` | Login / unauthenticated pages |
| `resources/views/layouts/vet.blade.php` | Vet portal pages |
| `resources/views/admin/layout.blade.php` | Admin portal |
| `resources/views/clinic/layout.blade.php` | Clinic portal |
| `resources/views/organisation/layout.blade.php` | Organisation portal |

### View organization

```
resources/views/
  ├── admin/           organisations/, vets/
  ├── auth/            login, register, password flows
  ├── clinic/          appointments/, billing/
  ├── components/      shared UI: buttons, inputs, modal, dropdown
  ├── layouts/         app, guest, vet, navigation
  ├── organisation/    clinics/, users/, roles/, vets/, inventory/, price-lists/
  ├── profile/         user profile edit
  └── vet/
        ├── appointments/   create, case, clinical-workspace, history, OTP flow
        ├── auth/           vet login
        ├── case_sheets/    edit
        ├── clinic/         vet's clinic dashboard
        ├── diagnostics/    create, edit, embed
        ├── pet_history/    search, result
        ├── pet_parents/    create, show
        ├── pets/           create, show
        ├── prescriptions/  create
        └── profile/        show, edit
```

---

## 16. Known Architectural Issues

The following issues were identified during architectural review. See separate issues document for full detail.

### Critical

| # | Issue | Location |
|---|---|---|
| C1 | Hardcoded `?? 1` clinic fallback — unauthenticated vets default to clinic 1 | `Vet/AppointmentController` |
| C2 | `dd()` debug statement in production — crashes app and leaks data | `Organisation/OrganisationRoleController:120` |
| C3 | Prompt injection — all case sheet fields directly interpolated into OpenAI prompts | `AiClinicalService.php` |
| C4 | Wrong auth guard in vet route — `auth()->id()` used instead of `auth('vet')->id()` | `Vet/AppointmentController:118` |
| C5 | No ownership check on `addTreatment` or `BillingController@create` — any vet with an appointment ID can modify it | `Vet/AppointmentController`, `Clinic/BillingController` |

### High

| # | Issue | Location |
|---|---|---|
| H1 | Vet AI endpoints have only `auth:vet` — no RBAC or scope checks | `routes/web.php:689–719` |
| H2 | `selfAssign()` hardcodes `vet_id => 1` instead of `auth('vet')->id()` | `Vet/AppointmentController:135` |
| H3 | Diagnostic view/embed only checks `clinic_id`, not vet's assignment to appointment | `Vet/DiagnosticController` |
| H4 | `organisation_admin` role is a ghost bypass — never assigned but grants full access | `User.php:115` |
| H5 | Raw `DB::` queries bypass Eloquent model events and relationships | `OrganisationRoleController`, `OrganisationVetController`, `AppointmentController` |
| H6 | `organisation_user_roles` has no `ON DELETE CASCADE` on `user_id` — orphaned records after user deletion | Migrations |

### Medium

| # | Issue | Location |
|---|---|---|
| M1 | Password generated and flashed as plaintext in session on clinic creation | `Admin/ClinicController:104` |
| M2 | No soft deletes on any model — deletions are permanent with no audit trail | All models |
| M3 | N+1 risk: `whereHas('vets')` inside clinic loop without eager loading | `OrganisationDashboardController` |
| M4 | All validation is inline `$request->validate()` — no Form Request classes | All controllers |
| M5 | Drug search and price list item lookup are not scoped to organisation | `InventoryController`, `PriceListController` |
| M6 | Duplicate `auth:vet` middleware applied redundantly inside already-guarded groups | `routes/web.php:484–571` |
| M7 | No policies or Gates — authorization logic lives on the `User` model directly | `User::hasPermission()` |
