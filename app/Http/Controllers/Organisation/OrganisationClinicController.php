<?php

namespace App\Http\Controllers\Organisation;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrganisationClinicController extends Controller
{
    public function index()
    {
        $clinics = Clinic::where(
            'organisation_id',
            Auth::user()->organisation_id
        )->latest()->get();

        return view('organisation.clinics.index', compact('clinics'));
    }

    public function create()
    {
        return view('organisation.clinics.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'pincode' => 'nullable|string',
            'gst_number' => 'nullable|string',
        ]);

        Clinic::create([
            'organisation_id' => Auth::user()->organisation_id,
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'city' => $request->city,
            'state' => $request->state,
            'pincode' => $request->pincode,
            'gst_number' => $request->gst_number,
        ]);

        return redirect()
            ->route('organisation.clinics.index')
            ->with('success', 'Clinic created successfully');
    }
}
