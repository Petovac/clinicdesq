<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    */

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    */

    'guards' => [

        // Org / Clinic users
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        // Vets (separate auth universe)
        'vet' => [
            'driver' => 'session',
            'provider' => 'vets',
        ],

        // Pet parents (phone-only login)
        'pet_parent' => [
            'driver' => 'session',
            'provider' => 'pet_parents',
        ],

        // Lab users (external/in-house lab staff)
        'lab' => [
            'driver' => 'session',
            'provider' => 'lab_users',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    */

    'providers' => [

        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],

        'vets' => [
            'driver' => 'eloquent',
            'model' => App\Models\Vet::class,
        ],

        'pet_parents' => [
            'driver' => 'eloquent',
            'model' => App\Models\PetParent::class,
        ],

        'lab_users' => [
            'driver' => 'eloquent',
            'model' => App\Models\LabUser::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    */

    'passwords' => [

        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],

        // Vet reset can be added later safely
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    */

    'password_timeout' => 10800,
];