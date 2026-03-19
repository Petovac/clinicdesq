<?php

namespace App\Http\Controllers\Organisation;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Organisation;
use App\Models\Clinic;
use App\Models\Vet;
use App\Models\User;
use App\Models\Bill;

class OrganisationDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $organisation = Organisation::findOrFail($user->organisation_id);

        $clinicIds = Clinic::where('organisation_id', $organisation->id)->pluck('id');

        $totalClinics = $clinicIds->count();

        $totalVets = Vet::whereHas('clinics', function ($q) use ($clinicIds) {
            $q->whereIn('clinics.id', $clinicIds);
        })->count();

        $totalUsers = User::where('organisation_id', $organisation->id)->count();

        $totalBills = Bill::whereIn('clinic_id', $clinicIds)->count();

        return view('organisation.dashboard', [
            'organisation' => $organisation,
            'user' => $user,
            'totalClinics' => $totalClinics,
            'totalVets' => $totalVets,
            'totalUsers' => $totalUsers,
            'totalBills' => $totalBills,
            'showBilling' => $user->hasPermission('billing.view'),
            'showInventory' => $user->hasPermission('inventory.view'),
            'showClinics' => $user->hasPermission('clinics.view'),
            'showUsers' => $user->hasPermission('users.view'),
        ]);
    }
}
