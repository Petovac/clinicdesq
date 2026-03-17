<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::if('feature', function (string $slug) {
            $user = auth()->user();
            if (!$user) return false;

            $org = null;
            if ($user->organisation_id ?? null) {
                $org = $user->organisation;
            } elseif ($user->clinic_id ?? null) {
                $org = $user->clinic?->organisation;
            }

            return $org ? $org->hasFeature($slug) : false;
        });
    }
}
