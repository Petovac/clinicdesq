# Lab Test Ordering System — Implementation Plan

## Overview
Build a complete lab ordering workflow: Vet orders tests → Clinic manager/receptionist routes (in-house or external) → Lab processes & uploads results → Vet reviews & approves → Results visible on pet profile.

Includes: **Lab Tests** tab in vet header nav.

---

## Architecture Decisions

### Lab Login: New `lab` auth guard (like `vet`, `pet_parent`)
- External labs are NOT clinic staff — separate portal, separate login
- In-house lab staff can also use this portal
- Clean isolation: labs see only their assigned orders, nothing else

### New model chain (NOT extending DiagnosticReport)
- Existing `DiagnosticReport` = vet manually uploads completed reports
- Lab orders = different lifecycle: order → route → process → upload → review → approve
- On vet approval, we auto-create a `DiagnosticReport` + `DiagnosticFile` so results integrate with existing pet history

---

## Database Schema (single migration)

### 1. `external_labs` — Lab organisations
```
id, organisation_id (FK), name, phone, email, address, type (in_house|external),
is_active, created_at, updated_at
```

### 2. `lab_users` — Lab staff login
```
id, external_lab_id (FK), name, email (unique), password, phone,
is_active, remember_token, created_at, updated_at
```

### 3. `lab_test_catalog` — Master test list per clinic
```
id, clinic_id (FK), name, code, category (hematology|biochemistry|urinalysis|serology|cytology|histopathology|microbiology|other),
sample_type (blood|urine|swab|tissue|fluid|other), price_list_item_id (FK nullable),
is_active, created_at, updated_at
```

### 4. `lab_orders` — One per order group
```
id, order_number (unique, e.g. LO-20260318-001),
appointment_id (FK), pet_id (FK), clinic_id (FK), vet_id (FK),
lab_id (FK nullable → external_labs), routing (pending|in_house|external),
status (ordered|routed|processing|results_uploaded|vet_review|approved|retest_requested),
priority (routine|urgent), notes,
routed_by (FK nullable → users), routed_at, completed_at,
created_at, updated_at
```

### 5. `lab_order_tests` — Individual tests in an order
```
id, lab_order_id (FK), lab_test_catalog_id (FK nullable), test_name,
status (pending|processing|completed), notes,
created_at, updated_at
```

### 6. `lab_results` — Result files/values
```
id, lab_order_id (FK), lab_order_test_id (FK nullable),
file_path, original_filename, mime_type, file_size,
extracted_text, summary, uploaded_by_lab_id (FK nullable),
vet_approved (bool default false), vet_approved_at, vet_notes,
retest_requested (bool default false), retest_reason,
visible_to_client (bool default false),
created_at, updated_at
```

---

## Status Flow

```
Vet orders tests ──→ [ordered]
                          │
     Clinic manager routes ──→ [routed] (in_house / external)
                                     │
                     Lab processes ──→ [processing]
                                          │
                   Lab uploads results ──→ [results_uploaded]
                                               │
                          Vet reviews ──→ [vet_review]
                                              │
                   ┌──────────┴──────────┐
                   │                     │
            [approved]          [retest_requested]
        (→ pet profile)          (→ back to lab)
```

---

## Build Phases (sequential)

### Phase 1: Migration + Models
- Single migration creating all 6 tables
- Models: `ExternalLab`, `LabUser`, `LabTestCatalog`, `LabOrder`, `LabOrderTest`, `LabResult`
- Add `hasMany(LabOrder)` to `Appointment`, `Pet`, `Clinic` models

### Phase 2: Lab Auth
- Add `lab` guard + provider in `config/auth.php`
- `LabAuthController` — login/logout
- `layouts/lab.blade.php` — lab portal layout
- Middleware: `auth:lab`

### Phase 3: Vet — Lab Test Ordering
- **Lab Tests** nav tab in `layouts/vet.blade.php`
- `Vet\LabOrderController` with routes:
  - `GET /vet/lab-orders` — list all orders (index page with status filters)
  - `POST /vet/appointments/{appointment}/lab-orders` — create order (AJAX)
  - `GET /vet/lab-orders/{order}` — view order detail + results
  - `POST /vet/lab-orders/{order}/approve` — approve results
  - `POST /vet/lab-orders/{order}/retest` — request retest with reason
- Add "Order Lab Tests" section on case sheet edit page
- Show lab order status on case view page (`case.blade.php`)

### Phase 4: Clinic Staff — Routing
- `Clinic\LabOrderController` with routes:
  - `GET /clinic/lab-orders` — list pending + all orders
  - `PUT /clinic/lab-orders/{order}/route` — assign to in-house or external lab
- Add "Lab Orders" nav item in clinic layout
- New permissions: `lab_orders.view`, `lab_orders.manage`

### Phase 5: Lab Portal
- `Lab\LabDashboardController` — dashboard with order counts
- `Lab\LabOrderController` with routes:
  - `GET /lab/orders` — list assigned orders
  - `GET /lab/orders/{order}` — view tests
  - `POST /lab/orders/{order}/start` — mark processing
  - `POST /lab/orders/{order}/tests/{test}/result` — upload result per test
  - `POST /lab/orders/{order}/complete` — mark all done
- Views: `resources/views/lab/` (login, layout, dashboard, orders/index, orders/show)

### Phase 6: Pet Profile Integration
- On vet approval: auto-create `DiagnosticReport` + `DiagnosticFile`
- Lab results appear in pet history and case view (via existing diagnostic system)
- `visible_to_client` flag controls pet parent access

### Phase 7: Org Admin — Manage Labs
- CRUD for external labs in org admin panel
- Create lab user accounts
- Manage lab test catalog

---

## Files to Create

**Migration:** `database/migrations/2026_03_18_100000_create_lab_system_tables.php`

**Models:**
- `app/Models/ExternalLab.php`
- `app/Models/LabUser.php`
- `app/Models/LabTestCatalog.php`
- `app/Models/LabOrder.php`
- `app/Models/LabOrderTest.php`
- `app/Models/LabResult.php`

**Controllers:**
- `app/Http/Controllers/Vet/LabOrderController.php`
- `app/Http/Controllers/Clinic/LabOrderController.php`
- `app/Http/Controllers/Lab/LabAuthController.php`
- `app/Http/Controllers/Lab/LabDashboardController.php`
- `app/Http/Controllers/Lab/LabOrderController.php`

**Views:**
- `resources/views/lab/login.blade.php`
- `resources/views/layouts/lab.blade.php`
- `resources/views/lab/dashboard.blade.php`
- `resources/views/lab/orders/index.blade.php`
- `resources/views/lab/orders/show.blade.php`
- `resources/views/vet/lab-orders/index.blade.php`
- `resources/views/vet/lab-orders/show.blade.php`
- `resources/views/clinic/lab-orders/index.blade.php`

**Config:** Update `config/auth.php`
**Routes:** Update `routes/web.php`
**Layouts:** Update `resources/views/layouts/vet.blade.php` (add Lab Tests nav)
**Layouts:** Update `resources/views/clinic/layout.blade.php` (add Lab Orders nav)
