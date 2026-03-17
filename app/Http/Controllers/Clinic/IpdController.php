<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IpdAdmission;
use App\Models\Pet;
use App\Models\PetParent;

class IpdController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $clinicIds = $user->assignedClinicIds();

        $filter = $request->get('filter', 'admitted');

        $query = IpdAdmission::whereIn('clinic_id', $clinicIds)
            ->with('pet.petParent', 'clinic');

        if ($filter === 'admitted') {
            $query->where('status', 'admitted');
        } elseif ($filter === 'discharged') {
            $query->where('status', 'discharged');
        }
        // 'all' = no filter

        $admissions = $query->orderBy('admission_date', 'desc')->paginate(30);

        return view('clinic.ipd.index', compact('admissions', 'filter'));
    }

    public function show(IpdAdmission $admission)
    {
        $user = auth()->user();
        $clinicIds = $user->assignedClinicIds();
        abort_if(!in_array($admission->clinic_id, $clinicIds), 403);

        $admission->load([
            'pet.petParent',
            'clinic',
            'vitals' => fn($q) => $q->orderBy('recorded_at', 'desc'),
            'treatments' => fn($q) => $q->with('drugGeneric', 'priceItem')->orderBy('administered_at', 'desc'),
            'notes' => fn($q) => $q->orderBy('created_at', 'desc'),
        ]);

        return view('clinic.ipd.show', compact('admission'));
    }

    public function create()
    {
        return view('clinic.ipd.create');
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $clinicIds = $user->assignedClinicIds();

        $request->validate([
            'clinic_id'            => 'required|exists:clinics,id',
            'pet_id'               => 'required|exists:pets,id',
            'pet_parent_id'        => 'required|exists:pet_parents,id',
            'admission_date'       => 'required|date',
            'admission_reason'     => 'required|string|max:2000',
            'tentative_diagnosis'  => 'nullable|string|max:2000',
            'cage_number'          => 'nullable|string|max:50',
            'ward'                 => 'nullable|string|max:100',
        ]);

        abort_if(!in_array((int) $request->clinic_id, $clinicIds), 403);

        // Check existing active admission
        $existing = IpdAdmission::where('pet_id', $request->pet_id)
            ->where('clinic_id', $request->clinic_id)
            ->where('status', 'admitted')
            ->first();

        if ($existing) {
            return redirect()->route('clinic.ipd.show', $existing->id)
                ->with('error', 'This pet already has an active IPD admission.');
        }

        $admission = IpdAdmission::create([
            'clinic_id'            => $request->clinic_id,
            'pet_id'               => $request->pet_id,
            'pet_parent_id'        => $request->pet_parent_id,
            'admitted_by_type'     => 'user',
            'admitted_by_id'       => $user->id,
            'admission_date'       => $request->admission_date,
            'admission_reason'     => $request->admission_reason,
            'tentative_diagnosis'  => $request->tentative_diagnosis,
            'cage_number'          => $request->cage_number,
            'ward'                 => $request->ward,
            'status'               => 'admitted',
        ]);

        return redirect()->route('clinic.ipd.show', $admission->id)
            ->with('success', 'Patient admitted to IPD successfully.');
    }

    public function storeVitals(Request $request, IpdAdmission $admission)
    {
        $user = auth()->user();
        abort_if(!in_array($admission->clinic_id, $user->assignedClinicIds()), 403);
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
                'recorded_by_type' => 'user',
                'recorded_by_id'   => $user->id,
            ]
        ));

        return response()->json(['success' => true]);
    }

    public function storeNote(Request $request, IpdAdmission $admission)
    {
        $user = auth()->user();
        abort_if(!in_array($admission->clinic_id, $user->assignedClinicIds()), 403);

        $request->validate([
            'note_type' => 'required|in:progress,handover,observation,plan',
            'content'   => 'required|string|max:5000',
        ]);

        $admission->notes()->create([
            'noted_by_type' => 'user',
            'noted_by_id'   => $user->id,
            'note_type'     => $request->note_type,
            'content'       => $request->content,
        ]);

        return response()->json(['success' => true]);
    }

    public function discharge(Request $request, IpdAdmission $admission)
    {
        $user = auth()->user();
        abort_if(!in_array($admission->clinic_id, $user->assignedClinicIds()), 403);
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
            'discharged_by_type' => 'user',
            'discharged_by_id'   => $user->id,
        ]);

        return redirect()->route('clinic.ipd.show', $admission->id)
            ->with('success', 'Patient discharged successfully.');
    }
}
