<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class ClinicController extends Controller
{
    /**
     * List clinics
     */
    public function index()
    {
        $clinics = Clinic::latest()->get();
        return view('admin.clinics.index', compact('clinics'));
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('admin.clinics.create');
    }

    /**
     * Store clinic
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        'name'       => 'required|string|max:255',
        'email'      => 'required|email|max:255|unique:users,email',
        'password'   => 'nullable|string|min:8',
        'phone'      => 'nullable|string|max:20',
        'address'    => 'nullable|string',
        'city'       => 'nullable|string|max:100',
        'state'      => 'nullable|string|max:100',
        'pincode'    => 'nullable|string|max:20',
        'gst_number' => 'nullable|string|max:50',
    ]);

    /** -----------------------------
     *  PASSWORD HANDLING
     * ----------------------------- */
    $plainPassword = $validated['password'] ?? Str::random(10);

    /** -----------------------------
     *  CREATE CLINIC USER
     * ----------------------------- */
    $user = User::create([
        'name'      => $validated['name'],
        'email'     => $validated['email'],
        'password'  => Hash::make($plainPassword),
        'role'      => 'clinic',
        'is_active' => 1,
    ]);

    /** -----------------------------
     *  CREATE CLINIC
     * ----------------------------- */
    $clinic = Clinic::create([
        'user_id'    => $user->id,
        'name'       => $validated['name'],
        'email'      => $validated['email'],
        'phone'      => $validated['phone'] ?? null,
        'address'    => $validated['address'] ?? null,
        'city'       => $validated['city'] ?? null,
        'state'      => $validated['state'] ?? null,
        'pincode'    => $validated['pincode'] ?? null,
        'gst_number' => $validated['gst_number'] ?? null,
    ]);

    /** -----------------------------
     *  EMAIL CREDENTIALS
     * ----------------------------- */
    // try {
    //     Mail::raw(
    //         "Your Clinicdesq login credentials:\n\n" .
    //         "Login URL: " . url('/clinic/login') . "\n" .
    //         "Email: {$validated['email']}\n" .
    //         "Password: {$plainPassword}\n\n" .
    //         "Please change your password after login.",
    //         function ($message) use ($validated) {
    //             $message->to($validated['email'])
    //                     ->subject('Clinicdesq Clinic Login Credentials');
    //         }
    //     );
    // } catch (\Exception $e) {
    //     // Email failure should NOT block clinic creation
    // }

    /** -----------------------------
     *  REDIRECT WITH ONE-TIME INFO
     * ----------------------------- */
    return redirect('/admin/clinics')
        ->with('success', 'Clinic created successfully')
        ->with('credentials', [
            'email'    => $validated['email'],
            'password' => $plainPassword,
        ]);
}


    /**
     * Show clinic details
     */
    public function show($id)
    {
        $clinic = Clinic::with('vets')->findOrFail($id);
        return view('admin.clinics.show', compact('clinic'));
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $clinic = Clinic::findOrFail($id);
        return view('admin.clinics.edit', compact('clinic'));
    }

    /**
     * Update clinic
     */
    public function update(Request $request, $id)
{
    $clinic = Clinic::with('user')->findOrFail($id);

    $validated = $request->validate([
        'name'       => 'required|string|max:255',
        'email'      => 'required|email|max:255|unique:users,email,' . $clinic->user_id,
        'password'   => 'nullable|string|min:6',
        'phone'      => 'nullable|string|max:20',
        'address'    => 'nullable|string',
        'city'       => 'nullable|string|max:100',
        'state'      => 'nullable|string|max:100',
        'pincode'    => 'nullable|string|max:20',
        'gst_number' => 'nullable|string|max:50',
    ]);

    // Update USER (login)
    $clinic->user->update([
        'name'  => $validated['name'],
        'email' => $validated['email'],
    ]);

    // Update password ONLY if provided
    if (!empty($validated['password'])) {
        $clinic->user->update([
            'password' => bcrypt($validated['password']),
        ]);
    }

    // Update CLINIC profile
    $clinic->update([
        'name'       => $validated['name'],
        'phone'      => $validated['phone'] ?? null,
        'address'    => $validated['address'] ?? null,
        'city'       => $validated['city'] ?? null,
        'state'      => $validated['state'] ?? null,
        'pincode'    => $validated['pincode'] ?? null,
        'gst_number' => $validated['gst_number'] ?? null,
    ]);

    return redirect('/admin/clinics')
        ->with('success', 'Clinic updated successfully');
}

}
