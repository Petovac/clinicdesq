<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vet;
use Illuminate\Http\Request;

class VetController extends Controller
{
    public function index(Request $request)
    {
        $query = Vet::query();

        if($request->search){
            $query->where('name','like','%'.$request->search.'%')
            ->orWhere('phone','like','%'.$request->search.'%')
            ->orWhere('registration_number','like','%'.$request->search.'%');
        }

        $vets = $query->get();

        return view('admin.vets.index', compact('vets'));
    }

    public function create()
    {
        return view('admin.vets.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:vets,phone',
            'email' => 'nullable|email|unique:vets,email',
            'registration_number' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
        ]);

        Vet::create($validated);

        return redirect('/admin/vets');
    }
}
