<?php

namespace App\Http\Controllers\Vet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IpdAdmission;
use App\Models\Appointment;
use App\Models\PriceListItem;
use App\Models\InjectionRouteFee;

class IpdController extends Controller
{
    public function index()
    {
        $clinicId = session('active_clinic_id');
        if (!$clinicId) {
            return redirect()->route('vet.dashboard')
                ->with('error', 'Please select a clinic first.');
        }

        $admissions = IpdAdmission::where('clinic_id', $clinicId)
            ->with('pet.petParent', 'clinic')
            ->orderByRaw("FIELD(status, 'admitted', 'discharged', 'transferred', 'deceased')")
            ->orderBy('admission_date', 'desc')
            ->paginate(20);

        return view('vet.ipd.index', compact('admissions', 'clinicId'));
    }

    public function show(IpdAdmission $admission)
    {
        $clinicId = session('active_clinic_id');
        abort_if($admission->clinic_id !== $clinicId, 403);

        $admission->load([
            'pet.petParent',
            'clinic',
            'vitals' => fn($q) => $q->orderBy('recorded_at', 'desc'),
            'treatments' => fn($q) => $q->with('drugGeneric', 'priceItem')->orderBy('administered_at', 'desc'),
            'notes' => fn($q) => $q->orderBy('created_at', 'desc'),
        ]);

        $orgId = $admission->clinic->organisation_id;
        $injectionRoutes = InjectionRouteFee::where('organisation_id', $orgId)
            ->where('is_active', true)
            ->orderByRaw("FIELD(route_code, 'IV','IM','SC','ID','PO','IO','IT')")
            ->get();

        $priceListItems = PriceListItem::where('is_active', 1)->get();
        $drugGenerics = \App\Models\DrugGeneric::orderBy('name')->get();

        return view('vet.ipd.show', compact('admission', 'injectionRoutes', 'priceListItems', 'drugGenerics'));
    }

    public function admitFromCase(Appointment $appointment)
    {
        $clinicId = session('active_clinic_id');
        abort_if($appointment->clinic_id !== $clinicId, 403);

        $appointment->load('pet.petParent', 'caseSheet');

        // Check if pet already has an active admission at this clinic
        $existingAdmission = IpdAdmission::where('pet_id', $appointment->pet_id)
            ->where('clinic_id', $clinicId)
            ->where('status', 'admitted')
            ->first();

        if ($existingAdmission) {
            return redirect()->route('vet.ipd.show', $existingAdmission->id)
                ->with('error', 'This pet already has an active IPD admission.');
        }

        return view('vet.ipd.admit', compact('appointment'));
    }

    public function store(Request $request)
    {
        $clinicId = session('active_clinic_id');
        abort_if(!$clinicId, 403);

        $request->validate([
            'pet_id'               => 'required|exists:pets,id',
            'pet_parent_id'        => 'required|exists:pet_parents,id',
            'appointment_id'       => 'nullable|exists:appointments,id',
            'admission_date'       => 'required|date',
            'admission_reason'     => 'required|string|max:2000',
            'tentative_diagnosis'  => 'nullable|string|max:2000',
            'cage_number'          => 'nullable|string|max:50',
            'ward'                 => 'nullable|string|max:100',
        ]);

        $admission = IpdAdmission::create([
            'clinic_id'            => $clinicId,
            'pet_id'               => $request->pet_id,
            'pet_parent_id'        => $request->pet_parent_id,
            'appointment_id'       => $request->appointment_id,
            'admitted_by_type'     => 'vet',
            'admitted_by_id'       => auth('vet')->id(),
            'admission_date'       => $request->admission_date,
            'admission_reason'     => $request->admission_reason,
            'tentative_diagnosis'  => $request->tentative_diagnosis,
            'cage_number'          => $request->cage_number,
            'ward'                 => $request->ward,
            'status'               => 'admitted',
        ]);

        return redirect()->route('vet.ipd.show', $admission->id)
            ->with('success', 'Patient admitted to IPD successfully.');
    }

    public function storeVitals(Request $request, IpdAdmission $admission)
    {
        abort_if($admission->clinic_id !== session('active_clinic_id'), 403);
        abort_if(!$admission->isAdmitted(), 403);

        $request->validate([
            'recorded_at'              => 'required|date',
            'temperature'              => 'nullable|numeric|between:90,115',
            'heart_rate'               => 'nullable|integer|between:10,400',
            'respiratory_rate'         => 'nullable|integer|between:2,200',
            'weight'                   => 'nullable|numeric|between:0.01,500',
            'spo2'                     => 'nullable|integer|between:0,100',
            'blood_pressure_systolic'  => 'nullable|integer|between:40,300',
            'blood_pressure_diastolic' => 'nullable|integer|between:20,200',
            'mucous_membrane'          => 'nullable|string|max:50',
            'crt'                      => 'nullable|numeric|between:0,10',
            'pain_score'               => 'nullable|integer|between:0,10',
            'notes'                    => 'nullable|string|max:2000',
        ]);

        $admission->vitals()->create(array_merge(
            $request->only([
                'recorded_at', 'temperature', 'heart_rate', 'respiratory_rate',
                'weight', 'spo2', 'blood_pressure_systolic', 'blood_pressure_diastolic',
                'mucous_membrane', 'crt', 'pain_score', 'notes',
            ]),
            [
                'recorded_by_type' => 'vet',
                'recorded_by_id'   => auth('vet')->id(),
            ]
        ));

        return response()->json(['success' => true]);
    }

    public function storeTreatment(Request $request, IpdAdmission $admission)
    {
        abort_if($admission->clinic_id !== session('active_clinic_id'), 403);
        abort_if(!$admission->isAdmitted(), 403);

        $request->validate([
            'price_list_item_id' => 'nullable|exists:price_list_items,id',
            'drug_generic_id'    => 'nullable|exists:drug_generics,id',
            'drug_brand_id'      => 'nullable|exists:drug_brands,id',
            'inventory_item_id'  => 'nullable|exists:inventory_items,id',
            'dose_mg'            => 'nullable|numeric|min:0',
            'dose_volume_ml'     => 'nullable|numeric|min:0',
            'route'              => 'nullable|string|max:100',
            'treatment_type'     => 'required|in:injection,procedure,medication,fluid',
            'notes'              => 'nullable|string|max:2000',
            'administered_at'    => 'required|date',
        ]);

        // Auto-resolve price list item from inventory item (same as AppointmentController)
        $priceListItemId = $request->price_list_item_id;
        if (!$priceListItemId && $request->inventory_item_id) {
            $clinic = $admission->clinic;
            $activeList = \App\Models\PriceList::where('organisation_id', $clinic->organisation_id)
                ->where('is_active', 1)->first();
            if ($activeList) {
                $priceItem = PriceListItem::where('price_list_id', $activeList->id)
                    ->where('inventory_item_id', $request->inventory_item_id)
                    ->where('is_active', 1)->first();
                $priceListItemId = $priceItem?->id;
            }
        }

        // Calculate billing quantity
        $billingQuantity = 1;
        if ($request->inventory_item_id && $request->dose_volume_ml) {
            $invItem = \App\Models\InventoryItem::find($request->inventory_item_id);
            $billingQuantity = $invItem?->is_multi_use ? $request->dose_volume_ml : 1;
        }

        $admission->treatments()->create([
            'treated_by_type'    => 'vet',
            'treated_by_id'      => auth('vet')->id(),
            'price_list_item_id' => $priceListItemId,
            'drug_generic_id'    => $request->drug_generic_id,
            'drug_brand_id'      => $request->drug_brand_id,
            'inventory_item_id'  => $request->inventory_item_id,
            'dose_mg'            => $request->dose_mg,
            'dose_volume_ml'     => $request->dose_volume_ml,
            'route'              => $request->route,
            'billing_quantity'   => $billingQuantity,
            'treatment_type'     => $request->treatment_type,
            'notes'              => $request->notes,
            'administered_at'    => $request->administered_at,
        ]);

        return response()->json(['success' => true]);
    }

    public function storeNote(Request $request, IpdAdmission $admission)
    {
        abort_if($admission->clinic_id !== session('active_clinic_id'), 403);

        $request->validate([
            'note_type' => 'required|in:progress,handover,observation,plan',
            'content'   => 'required|string|max:5000',
        ]);

        $admission->notes()->create([
            'noted_by_type' => 'vet',
            'noted_by_id'   => auth('vet')->id(),
            'note_type'     => $request->note_type,
            'content'       => $request->content,
        ]);

        return response()->json(['success' => true]);
    }

    public function discharge(Request $request, IpdAdmission $admission)
    {
        abort_if($admission->clinic_id !== session('active_clinic_id'), 403);
        abort_if(!$admission->isAdmitted(), 403);

        $request->validate([
            'discharge_notes'   => 'nullable|string|max:5000',
            'discharge_summary' => 'nullable|string|max:10000',
        ]);

        $admission->update([
            'status'             => 'discharged',
            'discharged_at'      => now(),
            'discharge_notes'    => $request->discharge_notes,
            'discharge_summary'  => $request->discharge_summary,
            'discharged_by_type' => 'vet',
            'discharged_by_id'   => auth('vet')->id(),
        ]);

        return redirect()->route('vet.ipd.show', $admission->id)
            ->with('success', 'Patient discharged successfully.');
    }

    /**
     * Return HTML fragment for IPD history modal (no layout)
     */
    public function historyView(IpdAdmission $admission)
    {
        $admission->load(['treatments', 'notes', 'clinic:id,name', 'pet']);
        return view('vet.ipd.partials.history_view', compact('admission'));
    }
}
