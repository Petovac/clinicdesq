<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\PetParent;
use App\Models\Pet;
use App\Models\Vet;

class ClinicAppointmentController extends Controller
{
    public function index()
    {
        $user     = Auth::user();
        $clinicId = $user->clinic_id;

        // Today's appointments (waiting queue + consultation + table)
        $appointments = Appointment::with(['pet.petParent', 'vet'])
            ->where('clinic_id', $clinicId)
            ->whereDate('scheduled_at', '>=', today())
            ->orderBy('appointment_number')
            ->get();

        // Status counts
        $waitingCount = Appointment::where('clinic_id', $clinicId)
            ->where('status', 'checked_in')
            ->count();

        $consultationCount = Appointment::where('clinic_id', $clinicId)
            ->where('status', 'in_consultation')
            ->count();

        $completedCount = Appointment::where('clinic_id', $clinicId)
            ->where('status', 'completed')
            ->whereDate('completed_at', today())
            ->count();

        $awaitingLabCount = Appointment::where('clinic_id', $clinicId)
            ->where('status', 'awaiting_lab_results')
            ->count();

        // Ready for billing: completed appointments without a confirmed bill
        $needsBilling = Appointment::with(['pet.petParent', 'vet', 'bill'])
            ->where('clinic_id', $clinicId)
            ->where('status', 'completed')
            ->where(function ($q) {
                $q->whereDoesntHave('bill')
                  ->orWhereHas('bill', fn($b) => $b->where('status', 'draft'));
            })
            ->latest('completed_at')
            ->limit(50)
            ->get();

        $needsBillingCount = $needsBilling->count();

        return view('clinic.appointments.index', compact(
            'appointments',
            'waitingCount',
            'consultationCount',
            'completedCount',
            'awaitingLabCount',
            'needsBilling',
            'needsBillingCount'
        ));
    }

    public function create()
    {
        return view('clinic.appointments.create');
    }

    public function searchPetParent(Request $request)
    {
        $request->validate(['mobile' => 'required|string']);

        $petParent = PetParent::where('phone', $request->mobile)
            ->with('pets')
            ->first();

        if (!$petParent) {
            return view('clinic.appointments.create', [
                'notFound' => true,
                'mobile'   => $request->mobile
            ]);
        }

        return view('clinic.appointments.pet_selection', [
            'petParent' => $petParent
        ]);
    }

    public function createForPet($petId)
    {
        $pet      = Pet::with('petParent')->findOrFail($petId);
        $clinicId = auth()->user()->clinic_id;

        $vets = Vet::whereHas('clinics', function ($q) use ($clinicId) {
            $q->where('clinics.id', $clinicId)
              ->where('clinic_vet.is_active', 1);
        })->get();

        return view('clinic.appointments.create_for_pet', [
            'pet'  => $pet,
            'vets' => $vets
        ]);
    }

    /**
     * AJAX: Get available time slots for a vet on a date
     */
    public function availableSlots(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'vet_id' => 'required|exists:vets,id',
        ]);

        $clinicId = session('active_clinic_id') ?? auth()->user()->clinic_id;
        $date = \Carbon\Carbon::parse($request->date);

        $slots = \App\Models\VetSchedule::generateSlots($date, $request->vet_id, $clinicId);

        return response()->json($slots);
    }

    public function store(Request $request)
    {
        $request->validate([
            'pet_id'       => 'required|exists:pets,id',
            'pet_parent_id'=> 'required|exists:pet_parents,id',
            'scheduled_at' => 'required|date',
            'vet_id'       => 'nullable|exists:vets,id',
            'weight'       => 'nullable|numeric|min:0'
        ]);

        $user     = auth()->user();
        $clinicId = $user->clinic_id;

        $nextNumber = Appointment::where('clinic_id', $clinicId)
            ->whereDate('scheduled_at', Carbon::parse($request->scheduled_at)->toDateString())
            ->max('appointment_number');

        $nextNumber = $nextNumber ? $nextNumber + 1 : 1;

        Appointment::create([
            'clinic_id'          => $clinicId,
            'vet_id'             => $request->vet_id,
            'pet_parent_id'      => $request->pet_parent_id,
            'pet_id'             => $request->pet_id,
            'scheduled_at'       => $request->scheduled_at,
            'weight'             => $request->weight,
            'created_by'         => auth()->id(),
            'status'             => 'scheduled',
            'appointment_number' => $nextNumber
        ]);

        return redirect()
            ->route('clinic.appointments.index')
            ->with('success', 'Appointment created successfully');
    }

    public function updateStatus(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);
        abort_if($appointment->clinic_id !== (int) session('active_clinic_id'), 403);
        $status      = $request->status;

        if ($status == 'checked_in') {
            $appointment->checked_in_at = now();
        }
        if ($status == 'in_consultation') {
            $appointment->consultation_started_at = now();
        }
        if ($status == 'completed' || $status == 'awaiting_lab_results') {
            if (!$appointment->completed_at) {
                $appointment->completed_at = now();
            }
        }

        $appointment->status = $status;
        $appointment->save();

        // On completion: create review request + send via WhatsApp
        if ($status == 'completed') {
            try {
                $appointment->load(['pet.petParent', 'clinic.organisation', 'vet']);
                $parent = $appointment->pet->petParent ?? null;

                if ($parent) {
                    // Create pending review
                    $review = \App\Models\ClinicReview::create([
                        'clinic_id' => $appointment->clinic_id,
                        'appointment_id' => $appointment->id,
                        'pet_parent_id' => $parent->id,
                        'vet_id' => $appointment->vet_id,
                        'status' => 'pending',
                    ]);

                    // Send review link via WhatsApp
                    $orgId = $appointment->clinic->organisation_id;
                    $waConfig = \App\Models\WhatsappConfig::where('organisation_id', $orgId)->first();

                    if ($waConfig && $waConfig->isConfigured() && $parent->phone) {
                        \App\Services\WhatsappService::sendTemplate(
                            organisationId: $orgId,
                            recipientPhone: $parent->phone,
                            recipientName: $parent->name,
                            templateName: 'clinicdesq_review_request',
                            messageType: 'custom',
                            templateVariables: [
                                'body' => [
                                    $parent->name,
                                    $appointment->pet->name,
                                    $appointment->clinic->name,
                                    $review->getReviewUrl(),
                                ],
                            ],
                            clinicId: $appointment->clinic_id,
                            referenceType: \App\Models\ClinicReview::class,
                            referenceId: $review->id,
                            sentBy: auth()->id(),
                        );
                    }
                }

                // Fire webhook
                \App\Services\WebhookService::dispatch($appointment->clinic->organisation_id, 'appointment.completed', [
                    'appointment_id' => $appointment->id,
                    'pet' => $appointment->pet->toArray(),
                    'pet_parent' => $parent?->toArray(),
                    'clinic' => $appointment->clinic->only(['id', 'name', 'city']),
                ]);
            } catch (\Exception $e) {
                \Log::error('Post-completion actions failed', ['error' => $e->getMessage()]);
            }
        }

        return back();
    }

    public function reschedule(Request $request, Appointment $appointment)
    {
        abort_if($appointment->clinic_id !== (int) session('active_clinic_id'), 403);
        $request->validate(['scheduled_at' => 'required|date']);
        $appointment->update(['scheduled_at' => $request->scheduled_at]);
        return back();
    }
}
