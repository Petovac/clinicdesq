<?php

namespace App\Http\Controllers\Vet\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VetLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('vet.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (
            Auth::guard('vet')->attempt(
                $request->only('email', 'password'),
                true
            )
        ) {
            return redirect()->route('vet.dashboard');
        }

        return back()->withErrors([
            'email' => 'Invalid credentials',
        ]);
    }

    public function logout()
    {
        Auth::guard('vet')->logout();

        return redirect()->route('vet.login');
    }
}