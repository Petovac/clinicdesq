<?php

namespace App\Http\Controllers\Vet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VetProfileController extends Controller
{
    /**
     * Show logged-in vet profile
     */
    public function show()
    {
        $vet = auth('vet')->user();

        if (!$vet) {
            abort(401, 'Unauthenticated vet');
        }

        return view('vet.profile.show', compact('vet'));
    }

    /**
     * Edit logged-in vet profile
     */
    public function edit()
    {
        $vet = auth('vet')->user();

        if (!$vet) {
            abort(401, 'Unauthenticated vet');
        }

        return view('vet.profile.edit', compact('vet'));
    }

    /**
     * Update logged-in vet profile
     */
    public function update(Request $request)
    {
        $vet = auth('vet')->user();

        if (!$vet) {
            abort(401, 'Unauthenticated vet');
        }

        $request->validate([
            'name'                => 'required|string|max:255',
            'email'               => 'nullable|email|max:255',
            'phone'               => 'nullable|string|max:20',
            'registration_number' => 'nullable|string|max:255',
            'specialisation'      => 'nullable|string|max:255',
            'degree'              => 'nullable|string|max:255',
            'skills'              => 'nullable|string',
            'certifications'      => 'nullable|string',
            'experience'          => 'nullable|string',
        ]);

        $vet->update([
            'name'                => $request->name,
            'email'               => $request->email,
            'phone'               => $request->phone,
            'registration_number' => $request->registration_number,
            'specialisation'      => $request->specialisation,
            'degree'              => $request->degree,
            'skills'              => $request->skills,
            'certifications'      => $request->certifications,
            'experience'          => $request->experience,
        ]);

        return redirect()
            ->route('vet.profile')
            ->with('success', 'Profile updated successfully');
    }
}