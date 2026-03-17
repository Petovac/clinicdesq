<?php

namespace App\Http\Controllers\Vet\Auth;

use App\Http\Controllers\Controller;
use App\Models\Vet;
use Illuminate\Http\Request;

class VetRegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('vet.auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'                => 'required|string|max:255',
            'phone'               => 'required|string|max:20|unique:vets,phone',
            'email'               => 'required|email|max:255|unique:vets,email',
            'password'            => 'required|confirmed|min:8',
            'registration_number' => 'required|string|max:255',
            'degree'              => 'required|string|max:255',
            'specialization'      => 'nullable|string|max:255',
            'skills'              => 'nullable|string|max:2000',
            'practicing_license'  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'certificates'        => 'nullable|array|max:5',
            'certificates.*'      => 'file|mimes:pdf,jpg,jpeg,png|max:5120',
            'signature'           => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        $vet = Vet::create([
            'name'                => $request->name,
            'phone'               => $request->phone,
            'email'               => $request->email,
            'password'            => $request->password,
            'registration_number' => $request->registration_number,
            'degree'              => $request->degree,
            'specialization'      => $request->specialization,
            'skills'              => $request->skills,
            'is_active'           => true,
        ]);

        $storagePath = "vets/{$vet->id}";

        // Practicing license
        if ($request->hasFile('practicing_license')) {
            $vet->license_path = $request->file('practicing_license')->store($storagePath, 'public');
        }

        // Certificates
        if ($request->hasFile('certificates')) {
            $certPaths = [];
            foreach ($request->file('certificates') as $cert) {
                $certPaths[] = $cert->store($storagePath, 'public');
            }
            $vet->certificate_paths = $certPaths;
        }

        // Signature
        if ($request->hasFile('signature')) {
            $vet->signature_path = $request->file('signature')->store($storagePath, 'public');
        }

        $vet->save();

        return redirect()->route('vet.login')
            ->with('success', 'Registration successful! You can now log in. Contact a clinic administrator to get linked to a clinic.');
    }
}
