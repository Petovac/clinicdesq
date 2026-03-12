<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureStaffRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $currentRole = session('role');

        if (!$currentRole) {
            auth()->logout();
            return redirect('/login');
        }

        if (!empty($roles) && !in_array($currentRole, $roles)) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
