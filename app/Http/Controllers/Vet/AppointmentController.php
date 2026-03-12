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

class AppointmentController extends Controller
{
    /**
     * STEP 1
     * Show search screen
     */
    public function create()
    {
        return view('vet.appointments.create');
    }

    /**
     * STEP 2
     * Search pet parent by mobile
     */
    public function searchPetParent(Request $request)
    {
        // Vet must be inside clinic to create appointment
        abort_if(!session()->has('active_clinic_id'), 403);
    
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
            'created_by' => auth()->id(),
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
            'vet_id' => 1, // TEMP until auth
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
            'diagnosticReports'
        ]);
    
        // Load pet history (read-only)
        $petHistory = Appointment::where('pet_id', $appointment->pet_id)
            ->where('id', '!=', $appointment->id)
            ->orderByDesc('scheduled_at')
            ->with(['caseSheet', 'prescription.items'])
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
        $appointment->load(['caseSheet', 'prescription.items']);

        return view('vet.appointments.partials.history_case', [
            'appointment' => $appointment,
        ]);
    }

    public function addTreatment(Request $request, Appointment $appointment)
    {
        $request->validate([
            'price_list_item_id' => 'required|exists:price_list_items,id',
        
            'drug_generic_id' => 'nullable|exists:drug_generics,id',
            'drug_brand_id' => 'nullable|exists:drug_brands,id',
        
            'dose_mg' => 'nullable|numeric',
            'dose_volume_ml' => 'nullable|numeric',
        
            'route' => 'nullable|string',
        
            'quantity' => 'nullable|numeric|min:1'
        ]);

        $appointment->treatments()->create([
            'price_list_item_id' => $request->price_list_item_id,
        
            'drug_generic_id' => $request->drug_generic_id,
            'drug_brand_id' => $request->drug_brand_id,
        
            'dose_mg' => $request->dose_mg,
            'dose_volume_ml' => $request->dose_volume_ml,
        
            'route' => $request->route,
        
            'billing_quantity' => $request->billing_quantity ?? 1,
        
            'quantity' => $request->quantity ?? 1
        ]);

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

        $appointment->load('pet');

        return view('vet.prescriptions.create', compact('appointment'));
    }

    public function editPrescription($appointmentId)
    {
        $appointment = Appointment::with([
            'pet',
            'prescription.items'
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
                        'medicine'     => $item['medicine'],
                        'dosage'       => $item['dosage'] ?? null,
                        'frequency'    => $item['frequency'] ?? null,
                        'duration'     => $item['duration'] ?? null,
                        'instructions' => $item['instructions'] ?? null,
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

        $appointment->load(['caseSheet', 'pet', 'treatments.priceItem']);

        $priceListItems = PriceListItem::where('is_active',1)->get();
        $drugGenerics = \App\Models\DrugGeneric::orderBy('name')->get();

        return view('vet.case_sheets.edit', [
            'appointment' => $appointment,
            'caseSheet'   => $appointment->caseSheet,
            'priceListItems' => $priceListItems,
            'drugGenerics' => $drugGenerics
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


    public function drugStrengths($genericId)
    {
        $generic = \App\Models\DrugGeneric::find($genericId);

        if(!$generic){
            return response()->json([]);
        }

        // KB strengths
        $kbStrengths = \App\Models\DrugBrand::where('generic_id',$genericId)
            ->select('strength_value','strength_unit','form')
            ->get();

        // Org custom drugs (match by generic name)
        $orgStrengths = \App\Models\InventoryItem::where('item_type','drug')
            ->where('generic_name',$generic->name)
            ->select(
                'strength_value',
                'strength_unit',
                'package_type as form'
            )
            ->get();

            $strengths = $kbStrengths
            ->concat($orgStrengths)
            ->unique(function ($item) {
                return $item->strength_value.'-'.$item->strength_unit.'-'.$item->form;
            })
            ->values();

        return response()->json($strengths);
    }
}
