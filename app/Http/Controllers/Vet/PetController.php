<?php

namespace App\Http\Controllers\Vet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pet;
use App\Models\PetParent;

class PetController extends Controller
{
    public function create($parentId)
    {
        $parent = PetParent::findOrFail($parentId);
        return view('vet.pets.create', compact('parent'));
    }

    public function store(Request $request, $parentId)
    {
        $request->validate([
            'name'        => 'required|string',
            'species' => 'required|in:dog,cat,rabbit,bird,horse,cow,goat',
            'age'         => 'required|integer|min:0',
            'age_months'  => 'nullable|integer|min:0|max:11',
            'gender'      => 'nullable|string',
            'breed'       => 'nullable|string',
        ]);
    
        Pet::create([
            'pet_parent_id'   => $parentId,
            'name'            => $request->name,
            'species'         => $request->species,
            'breed'           => $request->breed,
            'gender'          => $request->gender,
            'age'             => $request->age,
            'age_months'      => $request->age_months ?? 0,
            'age_recorded_at' => now(),
        ]);
    
        if ($request->filled('redirect_to')) {
            return redirect($request->redirect_to)
                ->with('success', 'Pet added successfully');
        }
    
        return redirect()
            ->route('vet.petparent.show', $parentId)
            ->with('success', 'Pet added successfully');
    }

}
