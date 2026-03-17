<?php

namespace App\Http\Controllers\Lab;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExternalLabTest;

class LabCatalogController extends Controller
{
    public function index()
    {
        $user = auth('lab')->user();
        abort_if(!$user->isExternalLab(), 403);

        $tests = ExternalLabTest::where('external_lab_id', $user->external_lab_id)
            ->whereNull('organisation_id') // only the lab's own master catalog
            ->orderBy('category')
            ->orderBy('test_name')
            ->get();

        return view('lab.catalog.index', compact('tests'));
    }

    public function store(Request $request)
    {
        $user = auth('lab')->user();
        abort_if(!$user->isExternalLab(), 403);

        $data = $request->validate([
            'test_name' => 'required|string|max:255',
            'test_code' => 'nullable|string|max:50',
            'category' => 'nullable|string|max:100',
            'sample_type' => 'nullable|string|max:50',
            'parameters' => 'nullable|string',
            'estimated_time' => 'nullable|string|max:100',
            'b2b_price' => 'required|numeric|min:0',
        ]);

        $data['external_lab_id'] = $user->external_lab_id;
        $data['parameters'] = $data['parameters']
            ? array_map('trim', explode(',', $data['parameters']))
            : null;

        ExternalLabTest::create($data);

        return back()->with('success', 'Test added to your catalog.');
    }

    public function update(Request $request, ExternalLabTest $test)
    {
        $user = auth('lab')->user();
        abort_if($test->external_lab_id !== $user->external_lab_id, 403);
        abort_if($test->organisation_id !== null, 403); // can only edit master catalog

        $data = $request->validate([
            'test_name' => 'required|string|max:255',
            'test_code' => 'nullable|string|max:50',
            'category' => 'nullable|string|max:100',
            'sample_type' => 'nullable|string|max:50',
            'parameters' => 'nullable|string',
            'estimated_time' => 'nullable|string|max:100',
            'b2b_price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $data['parameters'] = $data['parameters']
            ? array_map('trim', explode(',', $data['parameters']))
            : null;
        $data['is_active'] = $request->boolean('is_active', true);

        $test->update($data);

        return back()->with('success', 'Test updated.');
    }

    public function destroy(ExternalLabTest $test)
    {
        $user = auth('lab')->user();
        abort_if($test->external_lab_id !== $user->external_lab_id, 403);
        abort_if($test->organisation_id !== null, 403);

        $test->delete();

        return back()->with('success', 'Test removed from catalog.');
    }
}
