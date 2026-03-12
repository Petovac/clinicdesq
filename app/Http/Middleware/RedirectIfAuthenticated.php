<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $guards = empty($guards) ? ['web'] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {

                // 🩺 Vet
                if ($guard === 'vet') {
                    return redirect()->route('vet.dashboard');
                }

                // 👤 Org / Clinic users
                if ($guard === 'web') {
                    $user = Auth::guard('web')->user();

                    if (!$user) {
                        return redirect('/');
                    }

                    return match ($user->role) {
                        'superadmin'          => redirect('/admin/dashboard'),

                        'organisation_owner',
                        'regional_manager',
                        'area_manager'        => redirect('/organisation/dashboard'),

                        'clinic_manager',
                        'receptionist',
                        'sales'               => redirect('/clinic/dashboard'),

                        default               => redirect('/'),
                    };
                }
            }
        }

        return $next($request);
    }
}