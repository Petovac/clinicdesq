<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): \Illuminate\Http\RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user();

    /*
    |--------------------------------------------------------------------------
    | Superadmin
    |--------------------------------------------------------------------------
    */
    if ($user->role === 'superadmin') {
        return redirect('/admin/dashboard');
    }

    /*
|--------------------------------------------------------------------------
| Organisation panel
|--------------------------------------------------------------------------
*/
if (
    $user->hasPermission('dashboard.view') ||
    $user->hasPermission('users.view') ||
    $user->hasPermission('clinics.view') ||
    $user->hasPermission('inventory.view') ||
    $user->hasPermission('appointments.metrics')
) {
    // Org-level users with a clinic_id also need it in session for clinic routes
    if ($user->clinic_id) {
        session(['active_clinic_id' => $user->clinic_id]);
    }
    return redirect('/organisation/dashboard');
}

/*
|--------------------------------------------------------------------------
| Clinic panel
|--------------------------------------------------------------------------
*/
if (
    $user->hasPermission('appointments.view') ||
    $user->hasPermission('appointments.create') ||
    $user->hasPermission('billing.create') ||
    $user->hasPermission('reports.upload')
) {
    // Set active_clinic_id for clinic staff so all controllers can use it
    if ($user->clinic_id) {
        session(['active_clinic_id' => $user->clinic_id]);
    }
    return redirect('/clinic/dashboard');
}

    /*
    |--------------------------------------------------------------------------
    | Fallback
    |--------------------------------------------------------------------------
    */
    Auth::logout();
    return redirect('/login')->withErrors([
        'email' => 'No dashboard assigned for this account.',
    ]);
    }


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
