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
            'gmb_review_url' => 'nullable|url|max:500',
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
            'gmb_review_url' => $request->gmb_review_url,
        ]);

        return redirect()
            ->route('organisation.clinics.index')
            ->with('success', 'Clinic created successfully');
    }

    public function edit(Clinic $clinic)
    {
        abort_if($clinic->organisation_id !== Auth::user()->organisation_id, 403);
        return view('organisation.clinics.edit', compact('clinic'));
    }

    public function update(Request $request, Clinic $clinic)
    {
        abort_if($clinic->organisation_id !== Auth::user()->organisation_id, 403);

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
            'pincode' => 'nullable|string|max:10',
            'gst_number' => 'nullable|string|max:20',
            'gmb_review_url' => 'nullable|url|max:500',
        ]);

        $clinic->update($request->only([
            'name', 'phone', 'email', 'address', 'city', 'state', 'pincode', 'gst_number', 'gmb_review_url'
        ]));

        return redirect()
            ->route('organisation.clinics.index')
            ->with('success', 'Clinic updated successfully');
    }
}
