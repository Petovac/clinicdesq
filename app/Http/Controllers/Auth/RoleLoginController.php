<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

class RoleLoginController extends Controller
{
    /**
     * Staff login (Superadmin + Organisation + Clinic staff)
     */
    public function staffLogin()
{
    return view('auth.login', [
        'loginAction' => route('login'),
        'expectedRole' => 'staff'
    ]);
}

    /**
     * Vet login (separate system, unchanged)
     */
    public function vetLogin()
    {
        return view('auth.login', [
            'loginAction' => route('vet.login'),
        ]);
    }
}
