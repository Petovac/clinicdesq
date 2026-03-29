<?php

namespace App\Http\Controllers\Vet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PetParent;

class PetParentController extends Controller
{
    public function create(Request $request)
    {
        $prefillPhone = $request->query('phone', session('prefill_mobile'));
        $redirect = $request->query('redirect');

        return view('vet.pet_parents.create', compact('prefillPhone', 'redirect'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        $parent = PetParent::create($request->only(['name', 'phone']));

        // If coming from clinic appointment flow, redirect to pet creation under clinic routes
        if ($request->input('redirect') === 'clinic') {
            return redirect()->route('clinic.pets.create', $parent->id);
        }

        return redirect()->route('vet.pets.create', $parent->id);
    }
}
