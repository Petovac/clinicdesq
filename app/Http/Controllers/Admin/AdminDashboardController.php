<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Models\Vet;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalClinics = Clinic::count();
        $totalVets    = Vet::count();
        $activeClinics = Clinic::count(); // later add status column

        $recentClinics = Clinic::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalClinics',
            'totalVets',
            'activeClinics',
            'recentClinics'
        ));
    }
}
