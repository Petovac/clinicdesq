<?php

namespace App\Http\Controllers\Lab;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\ExternalLab;
use App\Models\LabUser;

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

    public function showRegisterForm()
    {
        return view('lab.auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'lab_name' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:10',
            'lab_phone' => 'nullable|string|max:20',
            'lab_email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:lab_users,email',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'nullable|string|max:20',
        ]);

        // Create the external lab
        $lab = ExternalLab::create([
            'name' => $request->lab_name,
            'city' => $request->city,
            'state' => $request->state,
            'pincode' => $request->pincode,
            'phone' => $request->lab_phone,
            'email' => $request->lab_email,
            'address' => $request->address,
        ]);

        // Create the admin user for this lab
        $labUser = LabUser::create([
            'external_lab_id' => $lab->id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'role' => 'lab_admin',
        ]);

        Auth::guard('lab')->login($labUser);

        return redirect()->route('lab.dashboard')
            ->with('success', 'Lab registered successfully! Clinics can now onboard your lab.');
    }

    public function logout(Request $request)
    {
        auth('lab')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('lab.login');
    }
}
