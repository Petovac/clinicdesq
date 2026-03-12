<?php

namespace App\Http\Controllers\Vet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PetParent;

class PetParentController extends Controller
{
    public function create()
    {
        return view('vet.pet_parents.create');
    }

    public function store(Request $request)
    {
        $parent = PetParent::create($request->only(['name','phone']));

        return redirect()->route('vet.pets.create', $parent->id);
    }
}
