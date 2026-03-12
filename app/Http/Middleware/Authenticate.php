<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    protected function redirectTo($request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        // Vet panel → vet login
        if ($request->is('vet/*')) {
            return route('vet.login');
        }

        // Everything else → staff login (/login)
        return route('login');
    }
}