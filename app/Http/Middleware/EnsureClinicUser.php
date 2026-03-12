<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureClinicUser
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user) {
            abort(403);
        }

        // Clinic roles ONLY
        $clinicRoles = [
            'clinic_manager',
            'receptionist',
            'sales',
            'vet',
        ];

        if (!in_array($user->role, $clinicRoles)) {
            abort(403, 'Clinic access only');
        }

        // Must be attached to at least one clinic
        if (!$user->clinics || $user->clinics->isEmpty()) {
            abort(403, 'No clinic assigned to user');
        }

        return $next($request);
    }
}
