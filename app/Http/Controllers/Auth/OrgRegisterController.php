<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Organisation;
use App\Models\Package;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrgRegisterController extends Controller
{
    public function showForm()
    {
        $packages = Package::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('auth.org-register', compact('packages'));
    }

    public function register(Request $request)
    {
        $request->validate([
            // Step 1: Admin details
            'admin_name'     => 'required|string|max:255',
            'admin_email'    => 'required|email|max:255|unique:users,email',
            'admin_phone'    => 'required|string|max:20',
            'password'       => 'required|confirmed|min:8',

            // Step 2: Organisation details
            'org_name'       => 'required|string|max:255',
            'org_type'       => 'required|in:single_clinic,hospital,multi_clinic',
            'org_email'      => 'nullable|email|max:255',
            'org_phone'      => 'nullable|string|max:20',

            // Step 3: Plan selection
            'package_id'     => 'required|exists:packages,id',
        ], [
            'package_id.required' => 'Please select a plan to continue.',
            'admin_email.unique'  => 'This email is already registered. Try logging in instead.',
        ]);

        $package = Package::findOrFail($request->package_id);

        $result = DB::transaction(function () use ($request, $package) {
            $org = Organisation::create([
                'name'          => $request->org_name,
                'type'          => $request->org_type,
                'primary_email' => $request->org_email ?? $request->admin_email,
                'primary_phone' => $request->org_phone ?? $request->admin_phone,
                'is_active'     => true,
                'package_id'    => $package->id,
                'trial_ends_at' => now()->addDays($package->trial_days ?? 30),
            ]);

            $user = User::create([
                'name'            => $request->admin_name,
                'email'           => $request->admin_email,
                'phone'           => $request->admin_phone,
                'password'        => $request->password,
                'role'            => 'organisation_owner',
                'organisation_id' => $org->id,
                'is_active'       => true,
            ]);

            return ['org' => $org, 'user' => $user];
        });

        Auth::login($result['user']);

        return redirect()->route('organisation.dashboard')
            ->with('success', 'Welcome! Your organisation has been created with a ' . ($package->trial_days ?? 30) . '-day free trial.');
    }
}
