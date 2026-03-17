<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPackageFeature
{
    public function handle(Request $request, Closure $next, string $feature)
    {
        $user = $request->user();

        if (!$user) {
            abort(403, 'Unauthenticated.');
        }

        // Resolve organisation from user
        $org = null;
        if ($user->organisation_id) {
            $org = $user->organisation;
        } elseif ($user->clinic_id) {
            $org = $user->clinic?->organisation;
        }

        if (!$org) {
            abort(403, 'No organisation found.');
        }

        if (!$org->hasFeature($feature)) {
            abort(403, 'This feature is not available on your current plan. Please upgrade to access this functionality.');
        }

        return $next($request);
    }
}
