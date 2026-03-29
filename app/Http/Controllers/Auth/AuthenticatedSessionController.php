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
    | Determine panel: clinic-scoped users → clinic panel, central → org panel
    |--------------------------------------------------------------------------
    */

    // Set clinic session for users assigned to a clinic
    if ($user->clinic_id) {
        session(['active_clinic_id' => $user->clinic_id]);
    }

    // Check the user's role scope to decide which panel
    $roleAssignment = \App\Models\OrganisationUserRole::where('user_id', $user->id)
        ->where('organisation_id', $user->organisation_id)
        ->with('role')
        ->first();

    $clinicScope = $roleAssignment?->role?->clinic_scope ?? 'none';

    // Organisation owner always goes to org panel
    if ($user->role === 'organisation_owner' || $user->role === 'Organisation Owner') {
        return redirect('/organisation/dashboard');
    }

    // Single-clinic users → clinic panel (receptionist, clinic manager, etc.)
    if ($clinicScope === 'single' && $user->clinic_id) {
        return redirect('/clinic/dashboard');
    }

    // Central / multi-clinic users → org panel
    if ($clinicScope === 'none' || $clinicScope === 'multiple') {
        return redirect('/organisation/dashboard');
    }

    // Fallback: anyone with clinic permissions goes to clinic panel
    if ($user->hasPermission('appointments.view') ||
        $user->hasPermission('appointments.create') ||
        $user->hasPermission('billing.create') ||
        $user->hasPermission('reports.upload') ||
        $user->hasPermission('lab_orders.view') ||
        $user->hasPermission('inventory.view')) {
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
