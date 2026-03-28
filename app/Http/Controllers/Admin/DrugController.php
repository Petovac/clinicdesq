<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DrugGeneric;
use App\Models\DrugDosage;
use App\Models\DrugBrand;

class DrugController extends Controller
{

    public function index()
    {
        $drugs = DrugGeneric::orderBy('drug_class')->orderBy('name')->get();
        $grouped = $drugs->groupBy('drug_class');

        return view('admin.drugs.index', compact('drugs', 'grouped'));
    }

    public function create()
    {
        return view('admin.drugs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        $drug = DrugGeneric::create([
            'name' => $request->name,
            'drug_class' => $request->drug_class,
            'default_dose_unit' => 'mg/kg',
            'created_by' => auth()->id()
        ]);

        return redirect('/admin/drugs/'.$drug->id.'/edit');
    }

    public function edit($id)
    {
        $drug = DrugGeneric::with(['dosages','brands'])->findOrFail($id);

        return view('admin.drugs.edit', compact('drug'));
    }


    public function storeDosage(Request $request, $id)
    {
        $request->validate([
            'dose_min' => 'required'
        ]);

        $data = [
            'dose_min' => $request->dose_min,
            'dose_max' => $request->dose_max ?: null,
            'dose_unit' => $request->dose_unit ?: 'mg/kg',
            'routes' => $request->routes ? json_encode($request->routes) : null,
            'frequencies' => $request->frequencies ? json_encode($request->frequencies) : null,
        ];

        if ($request->dosage_id) {
            // Editing existing dosage
            DrugDosage::where('id', $request->dosage_id)
                ->where('generic_id', $id)
                ->update(array_merge($data, ['species' => $request->species]));
        } else {
            // Creating new or updating by species
            DrugDosage::updateOrCreate(
                ['generic_id' => $id, 'species' => $request->species],
                $data
            );
        }

        return redirect('/admin/drugs/'.$id.'/edit');
    }


    public function deleteDosage($id, $dosageId)
    {
        DrugDosage::where('id', $dosageId)->where('generic_id', $id)->delete();
        return redirect('/admin/drugs/'.$id.'/edit');
    }

    public function storeProduct(Request $request, $id)
    {

        $request->validate([
            'form' => 'required',
            'strength_value' => 'required',
            'pack_size' => 'nullable|numeric',

        ]);

        DrugBrand::create([
            'generic_id' => $id,
            'form' => $request->form,
            'brand_name' => $request->brand_name,
            'strength_value' => $request->strength_value,
            'strength_unit' => $request->strength_unit,
            'pack_size' => $request->pack_size,
            'pack_unit' => $request->pack_unit

        ]);

        return redirect('/admin/drugs/'.$id.'/edit');
    }
}