<?php

namespace App\Http\Controllers\Organisation;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Organisation;
use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Vet;
use App\Models\Bill;

class OrganisationDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $organisation = Organisation::findOrFail($user->organisation_id);

        /*
        |--------------------------------------------------------------------------
        | Organisation Metrics
        |--------------------------------------------------------------------------
        */

        $clinicIds = Clinic::where('organisation_id', $organisation->id)
            ->pluck('id');

        $todayAppointments = Appointment::whereIn('clinic_id', $clinicIds)
            ->whereDate('scheduled_at', today())
            ->count();

        $upcomingAppointments = Appointment::whereIn('clinic_id', $clinicIds)
            ->whereDate('scheduled_at', '>', today())
            ->count();

        $totalClinics = $clinicIds->count();

        $totalVets = Vet::whereHas('clinics', function ($q) use ($clinicIds) {
            $q->whereIn('clinics.id', $clinicIds);
        })->count();

        $totalBills = Bill::whereIn('clinic_id', $clinicIds)->count();

        /*
        |--------------------------------------------------------------------------
        | Module Permissions
        |--------------------------------------------------------------------------
        */

        return view('organisation.dashboard', [
            'organisation' => $organisation,
            'user' => $user,

            'todayAppointments' => $todayAppointments,
            'upcomingAppointments' => $upcomingAppointments,
            'totalClinics' => $totalClinics,
            'totalVets' => $totalVets,
            'totalBills' => $totalBills,

            'showAppointments' => $user->hasPermission('appointments.view'),
            'showBilling' => $user->hasPermission('billing.view'),
            'showInventory' => $user->hasPermission('inventory.view'),
            'showClinics' => $user->hasPermission('clinics.view'),
            'showUsers' => $user->hasPermission('users.view'),
            'showReports' => $user->hasPermission('reports.view'),
        ]);
    }
}