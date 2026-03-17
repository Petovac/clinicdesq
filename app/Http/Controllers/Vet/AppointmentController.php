<?php

namespace App\Http\Controllers\Vet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pet;
use App\Models\PetParent;
use App\Models\PetParentAccessOtp;
use App\Models\PetParentClinicAccess;
use App\Models\Appointment;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\CaseSheet;
use App\Models\PriceListItem;
use App\Models\InjectionRouteFee;

class AppointmentController extends Controller
{
    /**
     * STEP 1
     * Show search screen
     */
    public function create()
    {
        if (!session()->has('active_clinic_id')) {
            return redirect()->route('vet.dashboard')
                ->with('error', 'Please select a clinic first before creating an appointment.');
        }

        return view('vet.appointments.create');
    }

    /**
     * STEP 2
     * Search pet parent by mobile
     */
    public function searchPetParent(Request $request)
    {
        // Vet must be inside clinic to create appointment
        if (!session()->has('active_clinic_id')) {
            return redirect()->route('vet.dashboard')
                ->with('error', 'Please select a clinic first before creating an appointment.');
        }
    
        $request->validate([
            'mobile' => 'required|string',
        ]);
    
        $petParent = PetParent::where('phone', $request->mobile)
            ->with('pets')
            ->first();
    
        if (!$petParent) {
            return view('vet.appointments.create', [
                'notFound' => true,
                'mobile'   => $request->mobile,
            ]);
        }
    
        // DIRECTLY SHOW PROFILE (NO OTP)
        return redirect()->route('vet.petparent.show', $petParent->id);
    }

    /**
     * STEP 5
     * Create appointment for specific pet
     */
    public function createForPet($petId)
    {
        $pet = Pet::with('petParent')->findOrFail($petId);

        // Get last recorded weight for this pet
        $lastWeight = Appointment::where('pet_id', $petId)
            ->whereNotNull('weight')
            ->orderByDesc('id')
            ->value('weight');

        return view('vet.appointments.create_for_pet', [
            'pet' => $pet,
            'lastWeight' => $lastWeight,
        ]);
    }


    public function store(Request $request)
    {
        // HARD BLOCK — cannot bypass
        if (!$request->filled('weight')) {
            return back()
                ->withInput()
                ->with('error', 'Pet weight is mandatory for every appointment.');
        }

        $request->validate([
            'pet_id'         => 'required|exists:pets,id',
            'pet_parent_id'  => 'required|exists:pet_parents,id',
            'scheduled_at'   => 'required|date',
            'weight'         => 'required|numeric|min:0.1',
        ]);

        $pet = Pet::findOrFail($request->pet_id);

        /*
        |--------------------------------------------------------------------------
        | Calculate pet age at visit
        |--------------------------------------------------------------------------
        */
        $petAgeAtVisit = null;

        if ($pet->age && $pet->age_recorded_at) {
            $recordedDate = \Carbon\Carbon::parse($pet->age_recorded_at);
            $visitDate = \Carbon\Carbon::parse($request->scheduled_at);

            $yearsPassed = $recordedDate->diffInYears($visitDate);
            $petAgeAtVisit = $pet->age + $yearsPassed;
        }

        Appointment::create([
            'clinic_id'         => session('active_clinic_id') ?? 1,
            'vet_id'            => auth('vet')->id(),
            'pet_parent_id'     => $request->pet_parent_id,
            'pet_id'            => $request->pet_id,
            'scheduled_at'      => $request->scheduled_at,
            'weight'            => $request->weight,
            'pet_age_at_visit'  => $petAgeAtVisit,
            'created_by' => auth('vet')->id(),
            'status'            => 'scheduled',
        ]);

        return redirect()
            ->route('vet.dashboard')
            ->with('success', 'Appointment created successfully');
    }

    public function selfAssign(Appointment $appointment)
    {
        // Prevent overwriting another vet
        if ($appointment->vet_id) {
            return back()->with('error', 'Appointment already assigned.');
        }

        $appointment->update([
            'vet_id' => auth('vet')->id(),
            'status' => 'scheduled',
        ]);

        return back()->with('success', 'Appointment assigned to you.');
    }


    public function viewCase(Appointment $appointment)
    {
        $clinicId = session('active_clinic_id');
        $vetId = auth('vet')->id();
    
        abort_if(!$clinicId, 403);
    
        // 1️⃣ Clinic isolation
        abort_if($appointment->clinic_id !== $clinicId, 403);
    
        // 2️⃣ Completed cases are locked
        abort_if($appointment->status === 'completed', 403);
    
        // 3️⃣ Only assigned vet can open
        abort_if($appointment->vet_id !== $vetId, 403);
    
        // Load active appointment
        $appointment->load([
            'pet.petParent',
            'caseSheet',
            'prescription.items',
            'treatments.drugGeneric',
            'treatments.priceItem',
            'diagnosticReports',
            'labOrders.tests',
        ]);
    
        // Load pet history (read-only)
        $petHistory = Appointment::where('pet_id', $appointment->pet_id)
            ->where('id', '!=', $appointment->id)
            ->orderByDesc('scheduled_at')
            ->with(['caseSheet', 'prescription.items', 'treatments.drugGeneric', 'treatments.priceItem'])
            ->get();
    
            return view('vet.appointments.case', [
                'appointment' => $appointment,
                'petHistory'  => $petHistory,
                'readOnly'    => false,
                'priceListItems' => PriceListItem::where('is_active',1)->get(),
                'drugGenerics' => \App\Models\DrugGeneric::orderBy('name')->get()
            ]);
    }

    public function historyView(Appointment $appointment)
    {
        $clinicId = session('active_clinic_id');
        $vetId = auth('vet')->id();

        abort_if(!$clinicId, 403);
        abort_if($appointment->clinic_id !== $clinicId, 403);

        // History is read-only, but still clinic-restricted
        $appointment->load(['caseSheet', 'prescription.items', 'treatments.drugGeneric', 'treatments.priceItem', 'diagnosticReports']);

        return view('vet.appointments.partials.history_case', [
            'appointment' => $appointment,
        ]);
    }

    public function addTreatment(Request $request, Appointment $appointment)
    {
        $request->validate([
            // For injectable drugs — pass inventory_item_id, price_list_item_id auto-resolved
            'inventory_item_id'  => 'nullable|exists:inventory_items,id',
            'drug_generic_id'    => 'nullable|exists:drug_generics,id',
            'drug_brand_id'      => 'nullable|exists:drug_brands,id',
            'dose_mg'            => 'nullable|numeric',
            'dose_volume_ml'     => 'nullable|numeric',
            'route'              => 'nullable|string',
            // For procedures — pass price_list_item_id directly
            'price_list_item_id' => 'nullable|exists:price_list_items,id',
        ]);

        $priceListItemId = $request->price_list_item_id;

        // Auto-resolve price list item from inventory item (for injectable drugs)
        if (!$priceListItemId && $request->inventory_item_id) {
            $clinic     = \App\Models\Clinic::find($appointment->clinic_id);
            $activeList = \App\Models\PriceList::where('organisation_id', $clinic->organisation_id)
                ->where('is_active', 1)
                ->first();

            if ($activeList) {
                $priceItem = \App\Models\PriceListItem::where('price_list_id', $activeList->id)
                    ->where('inventory_item_id', $request->inventory_item_id)
                    ->where('is_active', 1)
                    ->first();

                $priceListItemId = $priceItem?->id;
            }
        }

        // billing_quantity: for multi-use injectables bill per ml; for single-use or procedures bill per unit
        $billingQuantity = 1;
        if ($request->inventory_item_id && $request->dose_volume_ml) {
            $invItem = \App\Models\InventoryItem::find($request->inventory_item_id);
            $billingQuantity = $invItem?->is_multi_use ? $request->dose_volume_ml : 1;
        }

        $treatment = $appointment->treatments()->create([
            'price_list_item_id' => $priceListItemId,
            'drug_generic_id'    => $request->drug_generic_id,
            'drug_brand_id'      => $request->drug_brand_id,
            'inventory_item_id'  => $request->inventory_item_id,
            'dose_mg'            => $request->dose_mg,
            'dose_volume_ml'     => $request->dose_volume_ml,
            'route'              => $request->route,
            'billing_quantity'   => $billingQuantity,
        ]);

        return response()->json(['success' => true, 'id' => $treatment->id]);
    }

    public function deleteTreatment(Appointment $appointment, \App\Models\AppointmentTreatment $treatment)
    {
        $clinicId = session('active_clinic_id') ?? 1;
        abort_if($appointment->clinic_id !== $clinicId, 403);
        abort_if($treatment->appointment_id !== $appointment->id, 403);

        $treatment->delete();

        return response()->json(['success' => true]);
    }

    public function createPrescription(Appointment $appointment)
    {

        abort_if($appointment->status === 'completed', 403);
        abort_if($appointment->vet_id !== auth('vet')->id(), 403);
        abort_if($appointment->clinic_id !== session('active_clinic_id'), 403);

        // prevent duplicate prescription
        if ($appointment->prescription) {
            return redirect()
                ->route('vet.appointments.case', $appointment->id)
                ->with('error', 'Prescription already exists.');
        }

        $appointment->load('pet', 'clinic.organisation', 'vet');

        return view('vet.prescriptions.create', compact('appointment'));
    }

    public function editPrescription($appointmentId)
    {
        $appointment = Appointment::with([
            'pet',
            'prescription.items',
            'clinic.organisation',
            'vet',
        ])->findOrFail($appointmentId);

        abort_if($appointment->status === 'completed', 403);
        abort_if($appointment->vet_id !== auth('vet')->id(), 403);
        abort_if($appointment->clinic_id !== session('active_clinic_id'), 403);

        return view('vet.prescriptions.create', [
            'appointment'  => $appointment,
            'prescription' => $appointment->prescription,
        ]);
    }

    public function drugSearch(Request $request, Appointment $appointment)
    {
        $clinicId = $appointment->clinic_id;
        $search = $request->get('q');

        $drugs = \DB::table('drug_generics as dg')
            ->join('drug_brands as db', 'dg.id', '=', 'db.generic_id')
            ->leftJoin('clinic_brands as cb', function ($join) use ($clinicId) {
                $join->on('db.id', '=', 'cb.brand_id')
                    ->where('cb.clinic_id', '=', $clinicId);
            })
            ->select(
                'dg.name as generic_name',
                'db.id as brand_id',
                'db.brand_name',
                'db.strength',
                'db.form',
                \DB::raw("CASE WHEN cb.brand_id IS NOT NULL THEN 'available' ELSE 'not_available' END as clinic_status")
            )
            ->where('dg.name', 'like', "{$search}%")
            ->limit(10)
            ->get();

        return response()->json($drugs);
    }

    
    public function storePrescription(Request $request, $appointmentId)
    {
        $appointment = Appointment::findOrFail($appointmentId);

        abort_if($appointment->status === 'completed', 403);
        abort_if($appointment->vet_id !== auth('vet')->id(), 403);
        abort_if($appointment->clinic_id !== session('active_clinic_id'), 403);

        $request->validate([
            'notes' => 'nullable|string',
        ]);
    
        /*
        |--------------------------------------------------------------------------
        | 1️⃣ Create OR Update Prescription (CRITICAL FIX)
        |--------------------------------------------------------------------------
        */
        $prescription = Prescription::updateOrCreate(
            ['appointment_id' => $appointment->id],
            ['notes' => $request->notes]
        );
    
        /*
        |--------------------------------------------------------------------------
        | 2️⃣ Remove OLD medicines (important for edit)
        |--------------------------------------------------------------------------
        */
        $prescription->items()->delete();
    
        /*
        |--------------------------------------------------------------------------
        | 3️⃣ Re-insert medicines from form
        |--------------------------------------------------------------------------
        */
        if ($request->medicines) {
            foreach ($request->medicines as $item) {
                if (!empty($item['medicine'])) {
                    $prescription->items()->create([
                        'medicine'          => $item['medicine'],
                        'dosage'            => $item['dosage'] ?? null,
                        'frequency'         => $item['frequency'] ?? null,
                        'duration'          => $item['duration'] ?? null,
                        'instructions'      => $item['instructions'] ?? null,
                        'drug_generic_id'   => ($item['drug_generic_id'] ?? null) ?: null,
                        'inventory_item_id' => ($item['inventory_item_id'] ?? null) ?: null,
                        'strength_value'    => ($item['strength_value'] ?? null) ?: null,
                        'strength_unit'     => ($item['strength_unit'] ?? null) ?: null,
                        'form'              => ($item['form'] ?? null) ?: null,
                    ]);
                }
            }
        }
    
        return redirect()
            ->route('vet.appointments.case', $appointment->id)
            ->with('success', 'Prescription saved successfully');

    }
    


    public function editCaseSheet(Appointment $appointment)
    {
        $clinicId = session('active_clinic_id') ?? 1;

        abort_if($appointment->clinic_id !== $clinicId, 403);

        $appointment->load(['caseSheet', 'pet', 'clinic.organisation', 'vet', 'treatments.priceItem', 'treatments.drugGeneric', 'labOrders.tests']);

        $priceListItems = PriceListItem::where('is_active',1)->get();
        $drugGenerics = \App\Models\DrugGeneric::orderBy('name')->get();

        // Get org's active injection routes for the route selector
        $orgId = $appointment->clinic->organisation_id ?? null;
        $injectionRoutes = $orgId
            ? InjectionRouteFee::where('organisation_id', $orgId)
                ->where('is_active', true)
                ->orderByRaw("FIELD(route_code, 'IV','IM','SC','ID','PO','IO','IT')")
                ->get()
            : collect();

        return view('vet.case_sheets.edit', [
            'appointment' => $appointment,
            'caseSheet'   => $appointment->caseSheet,
            'priceListItems' => $priceListItems,
            'drugGenerics' => $drugGenerics,
            'injectionRoutes' => $injectionRoutes,
        ]);
    }

    public function storeCaseSheet(Request $request, Appointment $appointment)
    {
        $clinicId = session('active_clinic_id') ?? 1;

        abort_if($appointment->clinic_id !== $clinicId, 403);

        CaseSheet::updateOrCreate(
            ['appointment_id' => $appointment->id],
            $request->only([
                'presenting_complaint',
                'history',
                'clinical_examination',
                'temperature',
                'heart_rate',
                'respiratory_rate',
                'capillary_refill_time',
                'mucous_membrane',
                'hydration_status',
                'lymph_nodes',
                'body_condition_score',
                'pain_score',
                'differentials',
                'diagnosis',
                'treatment_given',
                'procedures_done',
                'further_plan',
                'advice',
            ])
        );

        return redirect()
            ->route('vet.appointments.case', $appointment->id)
            ->with('success', 'Case sheet saved successfully');
    }


    public function saveFollowup(Request $request, Appointment $appointment)
    {
        $clinicId = session('active_clinic_id');
        abort_if($appointment->clinic_id !== $clinicId, 403);
        abort_if($appointment->vet_id !== auth('vet')->id(), 403);

        $request->validate([
            'prognosis'       => 'nullable|in:good,guarded,poor,grave,hopeless',
            'followup_date'   => 'nullable|date|after_or_equal:today',
            'followup_reason' => 'nullable|string|max:1000',
        ]);

        $caseSheet = CaseSheet::firstOrCreate(
            ['appointment_id' => $appointment->id]
        );

        $caseSheet->update($request->only(['prognosis', 'followup_date', 'followup_reason']));

        return response()->json(['success' => true]);
    }

    public function drugDosage(Request $request, $genericId)
    {
        $species = strtolower($request->species);

        $dosages = \App\Models\DrugDosage::where('generic_id', $genericId)
            ->whereRaw('LOWER(species) = ?', [$species])
            ->get();

        return response()->json([
            'dosages' => $dosages
        ]);
    }


    /**
     * Returns strengths available in the clinic's own inventory for a given generic.
     * Only shows what the clinic actually has in stock.
     */
    public function drugStrengths($genericId)
    {
        $generic = \App\Models\DrugGeneric::find($genericId);
        if (!$generic) return response()->json([]);

        $clinicId = session('active_clinic_id');
        $clinic   = \App\Models\Clinic::find($clinicId);
        if (!$clinic) return response()->json([]);

        $orgId = $clinic->organisation_id;

        // 1. All org inventory items linked to this generic (in-stock OR not)
        $invItems = \App\Models\InventoryItem::where('organisation_id', $orgId)
            ->where('item_type', 'drug')
            ->where(function ($q) use ($genericId, $generic) {
                $q->where('drug_generic_id', $genericId)
                  ->orWhere('generic_name', $generic->name);
            })
            ->get();

        $results = [];
        $coveredKeys = [];

        foreach ($invItems as $item) {
            $inStock = \App\Models\InventoryBatch::where('inventory_item_id', $item->id)
                ->where('clinic_id', $clinicId)
                ->where('quantity', '>', 0)
                ->where(function ($q) {
                    $q->whereNull('expiry_date')
                       ->orWhere('expiry_date', '>=', now()->toDateString());
                })
                ->exists();

            $results[] = [
                'inventory_item_id' => $item->id,
                'name'              => $item->name,
                'strength_value'    => $item->strength_value,
                'strength_unit'     => $item->strength_unit,
                'form'              => $item->package_type,
                'is_multi_use'      => (bool) $item->is_multi_use,
                'unit_volume_ml'    => $item->unit_volume_ml,
                'in_stock'          => $inStock,
                'source'            => 'inventory',
            ];
            $coveredKeys[] = $item->strength_value . '_' . $item->strength_unit;
        }

        // 2. KB brands for this generic that are NOT already covered by an inventory item
        $kbBrands = \App\Models\DrugBrand::where('generic_id', $genericId)->get();
        foreach ($kbBrands as $brand) {
            $key = $brand->strength_value . '_' . $brand->strength_unit;
            if (in_array($key, $coveredKeys)) continue; // already shown via inventory

            $results[] = [
                'inventory_item_id' => null,
                'name'              => $brand->brand_name,
                'strength_value'    => $brand->strength_value,
                'strength_unit'     => $brand->strength_unit,
                'form'              => $brand->form,
                'is_multi_use'      => true,
                'unit_volume_ml'    => $brand->pack_size,
                'in_stock'          => false,
                'source'            => 'kb',
            ];
        }

        // In-stock items first, then by strength ascending
        usort($results, fn($a, $b) =>
            $b['in_stock'] <=> $a['in_stock'] ?: $a['strength_value'] <=> $b['strength_value']
        );

        return response()->json($results);
    }

    /**
     * Resolve the active PriceListItem for a given inventory_item_id.
     * Called by the frontend after vet picks a strength.
     */
    public function drugPriceItem(Request $request, $inventoryItemId)
    {
        $clinicId = session('active_clinic_id');
        $clinic   = \App\Models\Clinic::find($clinicId);
        if (!$clinic) {
            return response()->json(['found' => false]);
        }

        $activeList = \App\Models\PriceList::where('organisation_id', $clinic->organisation_id)
            ->where('is_active', 1)
            ->first();

        if (!$activeList) {
            return response()->json(['found' => false, 'message' => 'No active price list']);
        }

        $priceItem = \App\Models\PriceListItem::where('price_list_id', $activeList->id)
            ->where('inventory_item_id', $inventoryItemId)
            ->where('is_active', 1)
            ->first();

        if (!$priceItem) {
            return response()->json(['found' => false, 'message' => 'Drug not in price list']);
        }

        return response()->json([
            'found'             => true,
            'price_list_item_id'=> $priceItem->id,
            'name'              => $priceItem->name,
            'price'             => $priceItem->price,
            'billing_type'      => $priceItem->billing_type,
        ]);
    }

    /**
     * Prescription drug search — searches KB (drug_generics + drug_brands)
     * and clinic inventory, returns availability flag.
     */
    public function prescriptionDrugSearch(Request $request, Appointment $appointment)
    {
        $q        = $request->get('q', '');
        $clinicId = $appointment->clinic_id;
        $clinic   = \App\Models\Clinic::find($clinicId);
        $orgId    = $clinic?->organisation_id;
        $species  = strtolower($appointment->pet?->species ?? 'dog');

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        // Helper: get dosage info for a generic + species
        $getDosage = function ($genericId) use ($species) {
            if (!$genericId) return null;
            $d = \App\Models\DrugDosage::where('generic_id', $genericId)
                ->where('species', $species)
                ->first();
            if (!$d) {
                // Fallback to any species
                $d = \App\Models\DrugDosage::where('generic_id', $genericId)->first();
            }
            if (!$d) return null;
            return [
                'dose_min'   => (float) $d->dose_min,
                'dose_max'   => (float) $d->dose_max,
                'dose_unit'  => $d->dose_unit,
                'frequencies'=> is_string($d->frequencies) ? json_decode($d->frequencies, true) : ($d->frequencies ?? []),
                'routes'     => is_string($d->routes) ? json_decode($d->routes, true) : ($d->routes ?? []),
            ];
        };

        // Helper: check if an inventory item has stock at this clinic
        $hasStock = function ($inventoryItemId) use ($clinicId) {
            if (!$inventoryItemId) return false;
            return \App\Models\InventoryBatch::where('inventory_item_id', $inventoryItemId)
                ->where('clinic_id', $clinicId)
                ->where('quantity', '>', 0)
                ->exists();
        };

        // 1. KB matches — DrugGeneric + DrugBrand (search by generic name OR brand name)
        $kbRows = \DB::table('drug_generics as dg')
            ->join('drug_brands as db', 'dg.id', '=', 'db.generic_id')
            ->where(function ($query) use ($q) {
                $query->where('dg.name', 'like', "%{$q}%")
                      ->orWhere('db.brand_name', 'like', "%{$q}%");
            })
            ->select('dg.id as drug_generic_id', 'dg.name as generic_name',
                     'db.id as drug_brand_id', 'db.brand_name',
                     'db.strength_value', 'db.strength_unit', 'db.form', 'db.pack_size')
            ->limit(15)
            ->get();

        $results     = [];
        $coveredInvIds = [];

        foreach ($kbRows as $row) {
            // Find matching org inventory item (by drug_generic_id + matching strength)
            $invItem = \App\Models\InventoryItem::where('organisation_id', $orgId)
                ->where('drug_generic_id', $row->drug_generic_id)
                ->where('strength_value', $row->strength_value)
                ->where('strength_unit', $row->strength_unit)
                ->first();

            // Fallback: match by drug_brand_id
            if (!$invItem) {
                $invItem = \App\Models\InventoryItem::where('organisation_id', $orgId)
                    ->where('drug_brand_id', $row->drug_brand_id)
                    ->first();
            }

            if ($invItem) $coveredInvIds[] = $invItem->id;

            $results[] = [
                'drug_generic_id'   => $row->drug_generic_id,
                'drug_brand_id'     => $row->drug_brand_id,
                'display_name'      => $row->brand_name . ' ' . $row->strength_value . $row->strength_unit . ' (' . $row->form . ')',
                'strength_value'    => $row->strength_value,
                'strength_unit'     => $row->strength_unit,
                'form'              => $row->form,
                'inventory_item_id' => $invItem?->id,
                'in_inventory'      => $invItem && $hasStock($invItem->id),
                'dosage'            => $getDosage($row->drug_generic_id),
            ];
        }

        // 2. Org inventory items not covered by KB search
        $invOnly = \App\Models\InventoryItem::where('organisation_id', $orgId)
            ->where('item_type', 'drug')
            ->where('name', 'like', "%{$q}%")
            ->whereNotIn('id', $coveredInvIds)
            ->limit(5)
            ->get()
            ->map(function ($item) use ($hasStock, $getDosage) {
                return [
                    'drug_generic_id'   => $item->drug_generic_id,
                    'drug_brand_id'     => null,
                    'display_name'      => $item->name . ($item->strength_value ? ' ' . $item->strength_value . $item->strength_unit : ''),
                    'strength_value'    => $item->strength_value,
                    'strength_unit'     => $item->strength_unit,
                    'form'              => $item->package_type,
                    'inventory_item_id' => $item->id,
                    'in_inventory'      => $hasStock($item->id),
                    'dosage'            => $getDosage($item->drug_generic_id),
                ];
            });

        $all = array_merge($results, $invOnly->toArray());

        // In-stock first
        usort($all, fn($a, $b) => $b['in_inventory'] <=> $a['in_inventory']);

        return response()->json(array_values($all));
    }
}
