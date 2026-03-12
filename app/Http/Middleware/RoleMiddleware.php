<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, ...$roles)
{
    if (!auth()->check()) {
        return redirect('/login');
    }    

    if (!in_array(auth()->user()->role, $roles)) {
        return redirect()->back()->withErrors('Unauthorized access');
    }
    

    if (!auth()->user()->is_active) {
        abort(403, 'Account disabled');
    }

    return $next($request);
}

}
