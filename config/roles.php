<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Role Hierarchy & Authority Levels
    |--------------------------------------------------------------------------
    |
    | Higher number = higher authority
    | A user can create ONLY users with a LOWER authority value.
    |
    | Example:
    | organisation_owner (100) can create everyone
    | clinic_manager (40) can create receptionist (20) and sales (10)
    | receptionist (20) cannot create anyone
    |
    */

    'organisation_owner' => 100,

    'regional_manager'   => 80,

    'area_manager'       => 60,

    'clinic_manager'     => 40,

    'receptionist'       => 20,

    'sales'              => 10,

];
