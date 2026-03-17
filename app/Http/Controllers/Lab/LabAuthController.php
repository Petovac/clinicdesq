<?php

namespace App\Http\Controllers\Lab;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LabAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('lab.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('lab')->attempt($request->only('email', 'password'), true)) {
            $labUser = auth('lab')->user();

            if (!$labUser->is_active) {
                auth('lab')->logout();
                return back()->withErrors(['email' => 'Your account has been deactivated.']);
            }

            return redirect()->route('lab.dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    public function logout(Request $request)
    {
        auth('lab')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('lab.login');
    }
}
