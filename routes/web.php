<?php

use Illuminate\Support\Facades\Route;

/* ===========================
| Admin Controllers
=========================== */
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\ClinicController;
use App\Http\Controllers\Admin\VetController;
use App\Http\Controllers\Admin\OrganisationController;
use App\Http\Controllers\Admin\DrugController;

/* ===========================
| Organisation Controllers
=========================== */

use App\Http\Controllers\Organisation\OrganisationDashboardController;
use App\Http\Controllers\Organisation\OrganisationClinicController;
use App\Http\Controllers\Organisation\OrganisationUserController;
use App\Http\Controllers\Organisation\PriceListController;
use App\Http\Controllers\Organisation\OrganisationVetController;
use App\Http\Controllers\Organisation\OrganisationRoleController;
use App\Http\Controllers\Organisation\InventoryController;
use App\Http\Controllers\Organisation\FeeConfigController;
use App\Http\Controllers\Organisation\OrganisationSettingsController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\LandingController;

/* ===========================
| Clinic Controllers
=========================== */
use App\Http\Controllers\Clinic\ClinicDashboardController;
use App\Http\Controllers\Clinic\BillingController;
use App\Http\Controllers\Clinic\ClinicAppointmentController;
use App\Http\Controllers\Clinic\ClinicInventoryController;
use App\Http\Controllers\Clinic\FollowupController;
use App\Http\Controllers\Clinic\IpdController as ClinicIpdController;

/* ===========================
| Vet Controllers
=========================== */

use App\Http\Controllers\Vet\Auth\VetLoginController;
use App\Http\Controllers\Vet\Auth\VetRegisterController;
use App\Http\Controllers\Vet\VetDashboardController;
use App\Http\Controllers\Vet\VetProfileController;
use App\Http\Controllers\Vet\PetParentController;
use App\Http\Controllers\Vet\PetParentProfileController;
use App\Http\Controllers\Vet\PetController;
use App\Http\Controllers\Vet\PetProfileController;
use App\Http\Controllers\Vet\AppointmentController;
use App\Http\Controllers\Auth\RoleLoginController;
use App\Http\Controllers\Auth\OrgRegisterController;
use App\Http\Controllers\Vet\VetClinicController;
use App\Http\Controllers\Vet\VetAppointmentHistoryController;
use App\Http\Controllers\Vet\VetPetHistoryController;
use App\Http\Controllers\Vet\VetAiController;
use App\Http\Controllers\Vet\DiagnosticController;
use App\Http\Controllers\Vet\DiagnosticReportController;
use App\Http\Controllers\Vet\IpdController as VetIpdController;

/* ===========================
| Lab Controllers
=========================== */
use App\Http\Controllers\Lab\LabAuthController;
use App\Http\Controllers\Lab\LabDashboardController;
use App\Http\Controllers\Lab\LabOrderController as LabPortalOrderController;
use App\Http\Controllers\Vet\LabOrderController as VetLabOrderController;
use App\Http\Controllers\Clinic\LabOrderController as ClinicLabOrderController;
use App\Http\Controllers\Organisation\LabManagementController;

/* ===========================
| Pet Parent Controllers
=========================== */
use App\Http\Controllers\PetParent\ParentAuthController;
use App\Http\Controllers\PetParent\ParentDashboardController;

/* ===========================
| OPEN AI
=========================== */

use App\Services\AiClinicalService;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/
Route::get('/', [LandingController::class, 'index'])->name('landing');

Route::middleware('guest')->group(function () {
    Route::get('/register/organisation', [OrgRegisterController::class, 'showForm'])->name('org.register');
    Route::post('/register/organisation', [OrgRegisterController::class, 'register']);
});



/*
|--------------------------------------------------------------------------
| Login chooser
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->get('/login', function () {
    return redirect()->route('staff.login');
})->name('login');


/*
|--------------------------------------------------------------------------
| Role-specific Login Pages
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {

    // Staff login (superadmin + all clinic staff)
    Route::get('/staff/login', [RoleLoginController::class, 'staffLogin'])
        ->name('staff.login');

    // Vet login (unchanged, separate system)
    Route::get('/vet/login', [RoleLoginController::class, 'vetLogin'])
        ->name('vet.login');

});



/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:superadmin'])
    ->prefix('admin')
    ->group(function () {


    Route::get('/dashboard',
        [AdminDashboardController::class, 'index']
    )->name('admin.dashboard');

    Route::get('/organisations', [OrganisationController::class, 'index']);
    Route::get('/organisations/create', [OrganisationController::class, 'create']);
    Route::post('/organisations', [OrganisationController::class, 'store']);

    Route::get('/vets', [VetController::class, 'index']);
    Route::get('/vets/create', [VetController::class, 'create']);
    Route::post('/vets', [VetController::class, 'store']);

    /* Clinics */
    Route::get('/clinics', [ClinicController::class, 'index']);
    Route::get('/clinics/create', [ClinicController::class, 'create']);
    Route::post('/clinics', [ClinicController::class, 'store']);

    Route::get('/clinics/{id}', [ClinicController::class, 'show']);
    Route::get('/clinics/{id}/edit', [ClinicController::class, 'edit']);
    Route::post('/clinics/{id}/update', [ClinicController::class, 'update']);

    Route::get('/drugs', [DrugController::class, 'index']);

    Route::get('/drugs/create', [DrugController::class, 'create']);
    Route::post('/drugs', [DrugController::class, 'store']);
    
    Route::get('/drugs/{id}/edit', [DrugController::class, 'edit']);
    Route::post('/drugs/{id}/update', [DrugController::class, 'update']);
    
    Route::post('/drugs/{id}/dosage', [DrugController::class, 'storeDosage']);
    Route::post('/drugs/{id}/product', [DrugController::class, 'storeProduct']);
    
    Route::post('/drugs/{id}/delete', [DrugController::class, 'destroy']);
});

/*
|--------------------------------------------------------------------------
| Organisation
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])
    ->prefix('organisation')
    ->name('organisation.')
    ->group(function () {

        /* =========================
         | Organisation Dashboard
         ========================= */
         Route::middleware('permission:dashboard.view')->group(function () {

                Route::get('/dashboard', [OrganisationDashboardController::class, 'index'])
                    ->name('dashboard');

                // View clinics
                Route::get('/clinics', [OrganisationClinicController::class, 'index'])
                    ->name('clinics.index');

                // Users list & assignment
                Route::get('/users', [OrganisationUserController::class, 'index'])
                    ->name('users.index');
            });

            /* =========================
            | Manage Roles
            ========================= */

            Route::middleware('permission:roles.manage')
            ->group(function () {

                Route::get('/roles', [OrganisationRoleController::class, 'index'])
                    ->name('roles.index');

                Route::get('/roles/create', [OrganisationRoleController::class, 'create'])
                    ->name('roles.create');

                Route::post('/roles', [OrganisationRoleController::class, 'store'])
                    ->name('roles.store');

                Route::get('/roles/{role}/edit', [OrganisationRoleController::class, 'edit'])
                ->name('roles.edit');

                Route::put('/roles/{role}', [OrganisationRoleController::class, 'update'])
                ->name('roles.update');

            });

        /* =========================
         | Create Clinics
         ========================= */
         Route::middleware('permission:clinics.manage')
            ->group(function () {

                Route::get('/clinics/create', [OrganisationClinicController::class, 'create'])
                    ->name('clinics.create');

                Route::post('/clinics', [OrganisationClinicController::class, 'store'])
                    ->name('clinics.store');
            });

        /* =========================
         | Create Users
         ========================= */
         Route::middleware('permission:users.manage')
            ->group(function () {

                Route::get('/users/create', [OrganisationUserController::class, 'create'])
                    ->name('users.create');

                Route::post('/users', [OrganisationUserController::class, 'store'])
                    ->name('users.store');
            });
    });

    /* =========================
         Edit Users
         ========================= */

    Route::middleware([
        'auth',
        'permission:users.manage'
    ])
    ->prefix('organisation')
    ->name('organisation.')
    ->group(function () {
    
        Route::get('/users/{user}/edit', [OrganisationUserController::class, 'edit'])
            ->name('users.edit');
    
        Route::put('/users/{user}', [OrganisationUserController::class, 'update'])
            ->name('users.update');
    
    });

    /* =========================
         Price List
         ========================= */

    Route::middleware([
        'auth',
        'permission:pricing.manage'
    ])
    ->prefix('organisation')
    ->name('organisation.')
    ->group(function () {

        Route::get('/price-lists', [PriceListController::class, 'index'])
            ->name('price-lists.index');

        Route::get('/price-lists/create', [PriceListController::class, 'create'])
            ->name('price-lists.create');

        Route::post('/price-lists', [PriceListController::class, 'store'])
            ->name('price-lists.store');

        Route::get('/price-lists/{priceList}/edit', [PriceListController::class, 'edit'])
            ->name('price-lists.edit');

        Route::put('/price-lists/{priceList}', [PriceListController::class, 'update'])
            ->name('price-lists.update');

        Route::post('/price-lists/{priceList}/activate', [PriceListController::class, 'activate'])
            ->name('price-lists.activate');

        Route::post(
            '/price-lists/{priceList}/items',
            [PriceListController::class,'storeItem']
        )->name('price-lists.store-item');

        Route::put(
            '/price-list-items/{item}',
            [PriceListController::class,'updateItem']
        )->name('organisation.price-items.update');

        Route::delete(
            '/price-list-items/{item}',
            [PriceListController::class,'deleteItem']
        )->name('organisation.price-items.delete');
    });

    /* =========================
         Fee Configuration
         ========================= */

    Route::middleware(['auth', 'permission:pricing.manage'])
        ->prefix('organisation')
        ->name('organisation.')
        ->group(function () {

            Route::get('/fee-config', [FeeConfigController::class, 'index'])
                ->name('fee-config.index');

            Route::post('/fee-config/visit-fee', [FeeConfigController::class, 'updateVisitFee'])
                ->name('fee-config.visit-fee');

            Route::post('/fee-config/routes', [FeeConfigController::class, 'updateRouteFees'])
                ->name('fee-config.routes');

            Route::post('/fee-config/procedures', [FeeConfigController::class, 'updateProcedureFees'])
                ->name('fee-config.procedures');

            Route::get('/fee-config/procedures/{procedure}/consumables', [FeeConfigController::class, 'procedureConsumables'])
                ->name('fee-config.procedure-consumables');

            Route::post('/fee-config/procedures/{procedure}/consumables', [FeeConfigController::class, 'saveProcedureConsumables'])
                ->name('fee-config.save-procedure-consumables');
        });

    /* =========================
         Settings / Branding
         ========================= */

    Route::middleware(['auth', 'permission:settings.manage'])
        ->prefix('organisation')
        ->name('organisation.')
        ->group(function () {
            Route::get('/settings/branding', [OrganisationSettingsController::class, 'edit'])
                ->name('settings.branding');
            Route::post('/settings/branding', [OrganisationSettingsController::class, 'update'])
                ->name('settings.branding.update');
            Route::post('/settings/branding/logo', [OrganisationSettingsController::class, 'updateLogo'])
                ->name('settings.branding.logo');
            Route::post('/settings/branding/gst', [OrganisationSettingsController::class, 'updateGst'])
                ->name('settings.branding.gst');
            Route::get('/settings/branding/preview/{type}/{template}', [OrganisationSettingsController::class, 'preview'])
                ->name('settings.branding.preview');
        });


    /* =========================
         Lab Management (Org Admin)
         ========================= */
    Route::middleware(['auth'])
        ->prefix('organisation')
        ->name('organisation.')
        ->group(function () {
            // Lab Test Catalog
            Route::get('/lab-catalog', [LabManagementController::class, 'catalogIndex'])->name('lab-catalog.index');
            Route::get('/lab-catalog/create', [LabManagementController::class, 'catalogCreate'])->name('lab-catalog.create');
            Route::post('/lab-catalog', [LabManagementController::class, 'catalogStore'])->name('lab-catalog.store');
            Route::get('/lab-catalog/{test}/edit', [LabManagementController::class, 'catalogEdit'])->name('lab-catalog.edit');
            Route::put('/lab-catalog/{test}', [LabManagementController::class, 'catalogUpdate'])->name('lab-catalog.update');
            Route::delete('/lab-catalog/{test}', [LabManagementController::class, 'catalogDestroy'])->name('lab-catalog.destroy');

            // External Labs
            Route::get('/labs', [LabManagementController::class, 'labsIndex'])->name('labs.index');
            Route::get('/labs/create', [LabManagementController::class, 'labsCreate'])->name('labs.create');
            Route::post('/labs', [LabManagementController::class, 'labsStore'])->name('labs.store');
            Route::get('/labs/{lab}/edit', [LabManagementController::class, 'labsEdit'])->name('labs.edit');
            Route::put('/labs/{lab}', [LabManagementController::class, 'labsUpdate'])->name('labs.update');
            Route::post('/labs/{lab}/tests', [LabManagementController::class, 'labTestStore'])->name('labs.test.store');

            // Lab Technicians
            Route::get('/lab-techs', [LabManagementController::class, 'labTechIndex'])->name('lab-techs.index');
            Route::post('/lab-techs', [LabManagementController::class, 'labTechStore'])->name('lab-techs.store');
            Route::post('/lab-techs/{labUser}/toggle', [LabManagementController::class, 'labTechToggle'])->name('lab-techs.toggle');
        });


    /* =========================
         Inventory Management
         ========================= */


         Route::middleware([
            'auth',
            'permission:inventory.manage'
        ])
        ->prefix('organisation')
        ->name('organisation.')
        ->group(function () {
        
            Route::post('/inventory', [InventoryController::class,'store'])
                ->name('inventory.store');

            Route::post('/inventory/batch', [InventoryController::class, 'storeBatch'])
            ->name('inventory.batch.store');

            Route::get('/inventory-items', [InventoryController::class, 'items'])
                ->name('inventory.items');

            Route::get('/inventory-stock', [InventoryController::class, 'stock'])
                ->name('inventory.stock');

            Route::post('/inventory/update/{id}', [InventoryController::class,'update']);
            Route::post('/inventory/delete/{id}', [InventoryController::class,'delete']);

            Route::get('/drug-search', [InventoryController::class, 'searchDrugs']);
            Route::get('/generic-search', [InventoryController::class, 'searchGenerics']);
            Route::get('/brands-by-generic', [InventoryController::class, 'brandsByGeneric']);
            Route::get('/inventory-search', [InventoryController::class, 'searchInventoryItems']);

            // Stock Transfer
            Route::get('/inventory-transfer', [InventoryController::class, 'transferForm'])
                ->name('inventory.transfer');

            Route::post('/inventory-transfer', [InventoryController::class, 'transfer'])
                ->name('inventory.transfer.store');

            Route::get('/inventory-transfer/{item}/batches', [InventoryController::class, 'centralBatches'])
                ->name('inventory.central.batches');

            // Inventory Movement Log
            Route::get('/inventory-movements', [InventoryController::class, 'movements'])
                ->name('inventory.movements');

            // Clinic Inventory Overview
            Route::get('/clinic-inventory/{clinic}', [InventoryController::class, 'clinicOverview'])
                ->name('inventory.clinic-overview');

        });




    /* =========================
         Assign Vets
         ========================= */


         Route::middleware([
             'auth',
             'permission:vets.assign'
         ])
         ->prefix('organisation')
         ->name('organisation.')
         ->group(function () {
         
             // Vet search + list
             Route::get('/vets', [OrganisationVetController::class, 'index'])
                 ->name('vets.index');
         
             // View vet profile (read-only)
             Route::get('/vets/{vet}', [OrganisationVetController::class, 'show'])
                 ->name('vets.show');
         
             // Assign / remove clinics
             Route::post('/vets/{vet}/assign-clinics', [OrganisationVetController::class, 'assignClinics'])
                 ->name('vets.assignClinics');

             Route::post('/vets/{vet}/offboard', [OrganisationVetController::class, 'offboard']
            )->name('vets.offboard');
         });
    


/*
|--------------------------------------------------------------------------
| Clinic
|--------------------------------------------------------------------------
*/



Route::middleware(['auth'])
->prefix('clinic')
->name('clinic.')
->group(function () {

    /* =========================
     | Dashboard (any clinic staff)
     ========================= */
    Route::get('/dashboard', [ClinicDashboardController::class, 'index'])
        ->middleware('permission:dashboard.view')
        ->name('dashboard');

    /* =========================
     | Appointments (view)
     ========================= */
    Route::middleware('permission:appointments.view')->group(function () {

        Route::get(
            '/appointments',
            [ClinicAppointmentController::class,'index']
        )->name('appointments.index');

        Route::get(
            '/appointments/create',
            [ClinicAppointmentController::class,'create']
        )->name('appointments.create');

        Route::post(
            '/appointments/search',
            [ClinicAppointmentController::class,'searchPetParent']
        )->name('appointments.search');

        Route::get(
            '/appointments/create/pet/{pet}',
            [ClinicAppointmentController::class,'createForPet']
        )->name('appointments.createForPet');

        Route::post(
            '/appointments',
            [ClinicAppointmentController::class,'store']
        )->name('appointments.store');

        Route::post(
            '/appointments/{id}/status',
            [ClinicAppointmentController::class,'updateStatus']
        )->name('appointments.updateStatus');

        Route::post(
            '/appointments/{appointment}/reschedule',
            [ClinicAppointmentController::class,'reschedule']
        )->name('appointments.reschedule');
    });

    /* =========================
     | Billing
     ========================= */
    Route::get(
        '/appointments/{appointment}/billing',
        [BillingController::class, 'create']
    )->middleware('permission:billing.view')->name('billing.create');

    Route::post(
        '/bills/{bill}/confirm',
        [BillingController::class, 'confirm']
    )->middleware('permission:billing.create')->name('billing.confirm');

    Route::post(
        '/bills/{bill}/items',
        [BillingController::class, 'addItem']
    )->middleware('permission:billing.create')->name('billing.item.add');

    Route::patch(
        '/bill-items/{item}',
        [BillingController::class, 'updateItem']
    )->middleware('permission:billing.create')->name('billing.item.update');

    /* =========================
     | Inventory
     ========================= */
    Route::get('/inventory', [ClinicInventoryController::class, 'index'])
        ->middleware('permission:inventory.view')->name('inventory.index');

    Route::get('/inventory/adjust', [ClinicInventoryController::class, 'adjustForm'])
        ->middleware('permission:inventory.adjust')->name('inventory.adjust.form');

    Route::post('/inventory/adjust', [ClinicInventoryController::class, 'adjust'])
        ->middleware('permission:inventory.adjust')->name('inventory.adjust');

    Route::post('/inventory/add-stock', [ClinicInventoryController::class, 'addStock'])
        ->middleware('permission:inventory.manage')->name('inventory.addStock');

    Route::get('/inventory/movements', [ClinicInventoryController::class, 'movements'])
        ->middleware('permission:inventory.movements.view')->name('inventory.movements');

    Route::get('/inventory/{item}/batches', [ClinicInventoryController::class, 'itemBatches'])
        ->middleware('permission:inventory.view')->name('inventory.batches');

    Route::get('/inventory/{item}', [ClinicInventoryController::class, 'show'])
        ->middleware('permission:inventory.view')->name('inventory.show');

    /* =========================
     | Order Requests
     ========================= */
    Route::get('/orders', [ClinicInventoryController::class, 'orderIndex'])
        ->middleware('permission:inventory.purchase')->name('orders.index');

    Route::get('/orders/create', [ClinicInventoryController::class, 'orderCreate'])
        ->middleware('permission:inventory.purchase')->name('orders.create');

    Route::post('/orders', [ClinicInventoryController::class, 'orderStore'])
        ->middleware('permission:inventory.purchase')->name('orders.store');

    Route::get('/orders/{order}', [ClinicInventoryController::class, 'orderShow'])
        ->middleware('permission:inventory.purchase')->name('orders.show');

    Route::post('/orders/{order}/submit', [ClinicInventoryController::class, 'orderSubmit'])
        ->middleware('permission:inventory.purchase')->name('orders.submit');

    /* =========================
     | Follow-ups
     ========================= */
    Route::get('/followups', [FollowupController::class, 'index'])
        ->middleware('permission:followups.view')->name('followups.index');

    /* =========================
     | IPD (In-Patient Department)
     ========================= */
    Route::middleware('permission:ipd.view')->group(function () {
        Route::get('/ipd', [ClinicIpdController::class, 'index'])->name('ipd.index');
        Route::get('/ipd/{admission}', [ClinicIpdController::class, 'show'])->name('ipd.show');
    });

    Route::middleware('permission:ipd.manage')->group(function () {
        Route::get('/ipd-admit', [ClinicIpdController::class, 'create'])->name('ipd.create');
        Route::post('/ipd', [ClinicIpdController::class, 'store'])->name('ipd.store');
        Route::post('/ipd/{admission}/vitals', [ClinicIpdController::class, 'storeVitals'])->name('ipd.vitals.store');
        Route::post('/ipd/{admission}/notes', [ClinicIpdController::class, 'storeNote'])->name('ipd.notes.store');
        Route::post('/ipd/{admission}/discharge', [ClinicIpdController::class, 'discharge'])->name('ipd.discharge');
    });

    /* =========================
     | Documents (Print/PDF)
     ========================= */
    Route::get('/bills/{bill}/print', [DocumentController::class, 'billPrint'])->name('bill.print');
    Route::get('/bills/{bill}/pdf', [DocumentController::class, 'billPdf'])->name('bill.pdf');
    Route::get('/prescriptions/{prescription}/print', [DocumentController::class, 'prescriptionPrint'])->name('prescription.print');
    Route::get('/prescriptions/{prescription}/pdf', [DocumentController::class, 'prescriptionPdf'])->name('prescription.pdf');

});





/*
|--------------------------------------------------------------------------
| Vet
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Route::middleware('guest:vet')->group(function () {

    Route::get('/vet/login', [VetLoginController::class, 'showLoginForm'])
        ->name('vet.login');

    Route::post('/vet/login', [VetLoginController::class, 'login']);

    Route::get('/vet/register', [VetRegisterController::class, 'showRegistrationForm'])
        ->name('vet.register');
    Route::post('/vet/register', [VetRegisterController::class, 'register']);
});

Route::middleware('auth:vet')->group(function () {

    Route::post('/vet/logout', [VetLoginController::class, 'logout'])
        ->name('vet.logout');

    /*
    |--------------------------------------------------------------------------
    | Vet Dashboard & Profile
    |--------------------------------------------------------------------------
    */

    Route::get('/vet/dashboard',
        [VetDashboardController::class, 'index']
    )->name('vet.dashboard');

    Route::get('/vet/profile',
        [VetProfileController::class, 'show']
    )->name('vet.profile');

    Route::get('/vet/profile/edit',
        [VetProfileController::class, 'edit']
    )->name('vet.profile.edit');

    Route::post('/vet/profile/update',
        [VetProfileController::class, 'update']
    )->name('vet.profile.update');

    Route::post(
        '/vet/select-clinic/{clinic}',
        [VetDashboardController::class, 'selectClinic']
    )->name('vet.selectClinic');

    Route::get(
        '/vet/clinic',
        [VetClinicController::class, 'index']
    )->name('vet.clinic.dashboard');


    Route::post(
        '/vet/clinic/appointments/{appointment}/complete',
        [VetClinicController::class, 'markComplete']
    )->name('vet.clinic.appointments.complete');


    Route::prefix('vet/appointments')->middleware('auth:vet')->group(function () {
        Route::get('/history', [VetAppointmentHistoryController::class, 'index'])
            ->name('vet.appointments.history');
    
        Route::get('/history/{appointment}', [VetAppointmentHistoryController::class, 'show'])
            ->name('vet.appointments.history.show');
    });



    Route::middleware(['auth:vet'])->group(function () {

        Route::get('/pet-history', [VetPetHistoryController::class, 'index'])
            ->name('vet.pet.history');

        Route::post('/pet-history', [VetPetHistoryController::class, 'show'])
            ->name('vet.pet.history.result');

        Route::post(
            '/vet/appointments/{appointment}/diagnostics/extract',
            [\App\Http\Controllers\Vet\DiagnosticController::class, 'extract']
        )->name('vet.diagnostics.extract');    

        Route::post(
            '/vet/appointments/{appointment}/diagnostics/store',
            [\App\Http\Controllers\Vet\DiagnosticController::class, 'store']
        )->name('vet.diagnostics.store');

        Route::get(
            '/vet/diagnostics/files/{file}/download',
            [\App\Http\Controllers\Vet\DiagnosticController::class, 'download']
        )->name('vet.diagnostics.download');

        // Treatment
        Route::get(
            '/vet/drug-dosage/{generic}',
            [AppointmentController::class, 'drugDosage']
        )->middleware('auth:vet');

        Route::get(
            '/vet/drug-strengths/{generic}',
            [AppointmentController::class,'drugStrengths']
        )->middleware('auth:vet');

        Route::get(
            '/vet/drug-price-item/{inventoryItem}',
            [AppointmentController::class,'drugPriceItem']
        )->middleware('auth:vet')->name('vet.drug.price-item');

        Route::get(
            '/vet/drug-search',
            [InventoryController::class,'searchGenerics']
        )->middleware('auth:vet');

        // Edit diagnostics
        Route::get(
            '/vet/diagnostics/{report}/edit',
            [\App\Http\Controllers\Vet\DiagnosticController::class, 'edit']
        )->name('vet.diagnostics.edit');

        // Update diagnostics
        Route::put(
            '/vet/diagnostics/{report}',
            [\App\Http\Controllers\Vet\DiagnosticController::class, 'update']
        )->name('vet.diagnostics.update');

        // Delete individual diagnostic file
        Route::delete(
            '/vet/diagnostics/files/{file}',
            [\App\Http\Controllers\Vet\DiagnosticController::class, 'destroyFile']
        )->name('vet.diagnostics.files.destroy');

        Route::delete(
            '/vet/diagnostics/{report}',
            [\App\Http\Controllers\Vet\DiagnosticController::class, 'destroy']
        )->name('vet.diagnostics.destroy');

        Route::put(
            '/vet/diagnostics/files/{file}/summary',
            [DiagnosticController::class, 'updateFileSummary']
        )->name('vet.diagnostics.files.updateSummary');

        Route::get(
            'vet/diagnostics/files/{file}/view',
            [\App\Http\Controllers\Vet\DiagnosticController::class, 'view']
        )->name('vet.diagnostics.files.view');

        Route::get(
            'vet/diagnostics/files/{file}/embed',
            [\App\Http\Controllers\Vet\DiagnosticController::class, 'embed']
        )->name('vet.diagnostics.files.embed');

    });
    /*
    |--------------------------------------------------------------------------
    | Appointment Flow
    |--------------------------------------------------------------------------
    */

    Route::get('/vet/appointments/create',
        [AppointmentController::class, 'create']
    )->name('vet.appointments.create');

    Route::post('/vet/appointments/search',
        [AppointmentController::class, 'searchPetParent']
    )->name('vet.appointments.search');

    Route::get('/vet/appointments/create/pet/{pet}',
        [AppointmentController::class, 'createForPet']
    )->name('vet.appointments.createForPet');

    Route::post('/vet/appointments/store',
        [AppointmentController::class, 'store']
    )->name('vet.appointments.store');

    Route::post('/vet/appointments/{appointment}/assign',
        [AppointmentController::class, 'selfAssign']
    )->name('vet.appointments.assign');

    Route::get('/vet/appointments/{appointment}/case',
        [AppointmentController::class, 'viewCase']
    )->name('vet.appointments.case');

    Route::post(
        '/vet/appointments/{appointment}/treatment/add',
        [AppointmentController::class, 'addTreatment']
    )->name('vet.treatment.add');

    Route::delete(
        '/vet/appointments/{appointment}/treatment/{treatment}',
        [AppointmentController::class, 'deleteTreatment']
    )->name('vet.treatment.delete');

    Route::post(
        '/vet/appointments/{appointment}/followup',
        [AppointmentController::class, 'saveFollowup']
    )->name('vet.appointments.saveFollowup');

    Route::get(
        '/vet/appointments/{appointment}/diagnostics/create',
        [DiagnosticController::class, 'create']
    )->name('vet.diagnostics.create');

    Route::get('/vet/appointments/{appointment}/prescription/create',
        [AppointmentController::class, 'createPrescription']
    )->name('vet.prescription.create');

    Route::get(
        '/vet/appointments/{appointment}/drug-search',
        [AppointmentController::class, 'prescriptionDrugSearch']
    )->name('vet.prescription.drug.search');

    Route::get('/vet/appointments/{appointment}/prescription/edit',
        [AppointmentController::class, 'editPrescription']
    )->name('vet.prescription.edit');

    Route::post('/vet/appointments/{appointment}/prescription',
        [AppointmentController::class, 'storePrescription']
    )->name('vet.prescription.store');

    Route::get('/vet/appointments/{appointment}/case-sheet',
        [AppointmentController::class, 'editCaseSheet']
    )->name('vet.casesheet.edit');

    Route::post('/vet/appointments/{appointment}/case-sheet',
        [AppointmentController::class, 'storeCaseSheet']
    )->name('vet.casesheet.store');

    Route::get(
        '/vet/appointments/{appointment}/history-view',
        [AppointmentController::class, 'historyView']
    )->middleware('auth:vet');

    

    /*
    |--------------------------------------------------------------------------
    | Pet Parent
    |--------------------------------------------------------------------------
    */

    Route::get('/vet/pet-parent/create',
        [PetParentController::class, 'create']
    )->name('vet.petparent.create');

    Route::post('/vet/pet-parent/store',
        [PetParentController::class, 'store']
    )->name('vet.petparent.store');

    Route::get('/vet/pet-parents/{id}',
        [PetParentProfileController::class, 'show']
    )->name('vet.petparent.show');

    /*
    |--------------------------------------------------------------------------
    | Pets
    |--------------------------------------------------------------------------
    */

    Route::get('/vet/pets/create/{parent}',
        [PetController::class, 'create']
    )->name('vet.pets.create');

    Route::post('/vet/pets/store/{parent}',
        [PetController::class, 'store']
    )->name('vet.pets.store');

    Route::get('/vet/pets/{id}',
        [PetProfileController::class, 'show']
    )->name('vet.pet.show');
});


/*
    |--------------------------------------------------------------------------
    | OPENAI
    |--------------------------------------------------------------------------
    */


Route::post('/vet/ai/refine', [VetAiController::class, 'refine'])
    ->middleware('auth:vet');

Route::post('/vet/ai/clinical-insights', [VetAiController::class, 'clinicalInsights'])
->middleware('auth:vet');

Route::post(
    '/vet/ai/senior-support/{appointment}',
    [\App\Http\Controllers\Vet\VetAiController::class, 'seniorVetSupport']
)->middleware('auth:vet');

Route::post(
    '/vet/ai/prescription-support/{appointment}',
    [\App\Http\Controllers\Vet\VetAiController::class, 'prescriptionSupport']
)->middleware('auth:vet');

Route::post(
    '/appointments/{appointment}/ai/prescription',
    [VetAiController::class, 'prescriptionAI']
    )->middleware('auth:vet')
 ->name('vet.ai.prescription');

 Route::put(
    '/diagnostics/files/{file}/ai-summary',
    [DiagnosticController::class, 'updateFileSummary']
)->name('vet.diagnostics.files.aiSummary');

Route::post(
    '/diagnostics/files/{file}/verify',
    [DiagnosticController::class, 'verifyFile']
)->middleware('auth:vet')->name('vet.diagnostics.files.verify');

/* =========================
 | Vet IPD Routes
 ========================= */
Route::middleware('auth:vet')->prefix('vet/ipd')->name('vet.ipd.')->group(function () {
    Route::get('/', [VetIpdController::class, 'index'])->name('index');
    Route::get('/admit/{appointment}', [VetIpdController::class, 'admitFromCase'])->name('admitFromCase');
    Route::post('/', [VetIpdController::class, 'store'])->name('store');
    Route::get('/{admission}', [VetIpdController::class, 'show'])->name('show');
    Route::post('/{admission}/vitals', [VetIpdController::class, 'storeVitals'])->name('vitals.store');
    Route::post('/{admission}/treatments', [VetIpdController::class, 'storeTreatment'])->name('treatments.store');
    Route::post('/{admission}/notes', [VetIpdController::class, 'storeNote'])->name('notes.store');
    Route::post('/{admission}/discharge', [VetIpdController::class, 'discharge'])->name('discharge');
});

/* =========================
 | Vet Document Routes (Print/PDF)
 ========================= */
Route::middleware('auth:vet')->group(function () {
    Route::get('/vet/prescriptions/{prescription}/print', [DocumentController::class, 'prescriptionPrint'])->name('vet.prescription.print');
    Route::get('/vet/prescriptions/{prescription}/pdf', [DocumentController::class, 'prescriptionPdf'])->name('vet.prescription.pdf');
    Route::get('/vet/case-sheets/{caseSheet}/print', [DocumentController::class, 'caseSheetPrint'])->name('vet.casesheet.print');
    Route::get('/vet/case-sheets/{caseSheet}/pdf', [DocumentController::class, 'caseSheetPdf'])->name('vet.casesheet.pdf');
    Route::get('/vet/bills/{bill}/print', [DocumentController::class, 'billPrint'])->name('vet.bill.print');
    Route::get('/vet/bills/{bill}/pdf', [DocumentController::class, 'billPdf'])->name('vet.bill.pdf');
});

/* =========================
 | Vet Lab Order Routes
 ========================= */
Route::middleware('auth:vet')->prefix('vet/lab-orders')->name('vet.lab-orders.')->group(function () {
    Route::get('/', [VetLabOrderController::class, 'index'])->name('index');
    Route::get('/available-tests', [VetLabOrderController::class, 'availableTests'])->name('available-tests');
    Route::get('/results/{result}/download', [VetLabOrderController::class, 'downloadResult'])->name('result.download');
    Route::get('/{order}', [VetLabOrderController::class, 'show'])->name('show');
    Route::post('/{order}/approve', [VetLabOrderController::class, 'approve'])->name('approve');
    Route::post('/{order}/retest', [VetLabOrderController::class, 'requestRetest'])->name('retest');
});

Route::middleware('auth:vet')->group(function () {
    Route::post('/vet/appointments/{appointment}/lab-orders', [VetLabOrderController::class, 'store'])
        ->name('vet.lab-orders.store');
});

/* =========================
 | Clinic Lab Order Routes
 ========================= */
Route::middleware('auth')->prefix('clinic/lab-orders')->name('clinic.lab-orders.')->group(function () {
    Route::get('/', [ClinicLabOrderController::class, 'index'])->name('index');
    Route::put('/{order}/route', [ClinicLabOrderController::class, 'route'])->name('route');
    Route::post('/{order}/tests/{test}/result', [ClinicLabOrderController::class, 'uploadInHouseResult'])->name('upload-result');
    Route::post('/{order}/direct-upload', [ClinicLabOrderController::class, 'directUpload'])->name('direct-upload');
    Route::post('/{order}/complete', [ClinicLabOrderController::class, 'markInHouseComplete'])->name('complete');
});

/* =========================
 | Lab Portal Routes
 ========================= */
Route::middleware('guest:lab')->group(function () {
    Route::get('/lab/login', [LabAuthController::class, 'showLoginForm'])->name('lab.login');
    Route::post('/lab/login', [LabAuthController::class, 'login']);
});

Route::middleware('auth:lab')->prefix('lab')->name('lab.')->group(function () {
    Route::post('/logout', [LabAuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [LabDashboardController::class, 'index'])->name('dashboard');
    Route::post('/toggle-availability', [LabDashboardController::class, 'toggleAvailability'])->name('toggle-availability');
    Route::get('/orders', [LabPortalOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [LabPortalOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/start', [LabPortalOrderController::class, 'startProcessing'])->name('orders.start');
    Route::post('/orders/{order}/tests/{test}/result', [LabPortalOrderController::class, 'uploadResult'])->name('orders.upload-result');
    Route::post('/orders/{order}/complete', [LabPortalOrderController::class, 'markComplete'])->name('orders.complete');
});

/* =========================
     Pet Parent Portal
     ========================= */

Route::middleware('guest:pet_parent')->group(function () {
    Route::get('/parent/login', [ParentAuthController::class, 'showLoginForm'])->name('parent.login');
    Route::post('/parent/login', [ParentAuthController::class, 'login'])->middleware('throttle:5,1');
});

Route::middleware('auth:pet_parent')->prefix('parent')->name('parent.')->group(function () {
    Route::post('/logout', [ParentAuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [ParentDashboardController::class, 'index'])->name('dashboard');
    Route::get('/pets/{pet}', [ParentDashboardController::class, 'showPet'])->name('pets.show');
    Route::get('/appointments/{appointment}', [ParentDashboardController::class, 'showAppointment'])->name('appointments.show');
});

require __DIR__.'/auth.php';

