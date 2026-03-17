<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CaseSheet;
use Carbon\Carbon;

class FollowupController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $clinicIds = $user->assignedClinicIds();

        $filter = $request->get('filter', 'upcoming');
        $today = Carbon::today();

        $query = CaseSheet::whereNotNull('followup_date')
            ->whereHas('appointment', function ($q) use ($clinicIds) {
                $q->whereIn('clinic_id', $clinicIds);
            })
            ->with([
                'appointment.pet.petParent',
                'appointment.clinic',
                'appointment.vet',
            ]);

        switch ($filter) {
            case 'today':
                $query->whereDate('followup_date', $today);
                break;
            case 'overdue':
                $query->whereDate('followup_date', '<', $today);
                break;
            case 'all':
                // no filter
                break;
            case 'upcoming':
            default:
                $query->whereDate('followup_date', '>=', $today);
                $filter = 'upcoming';
                break;
        }

        $followups = $query->orderBy('followup_date', 'asc')->paginate(30);

        return view('clinic.followups.index', compact('followups', 'filter', 'today'));
    }
}
