<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LabDirectoryController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q', '');
        $cat = $request->get('category', '');

        $tests = DB::table('lab_test_directory')
            ->when($q, fn($query) => $query->where('name', 'like', "%{$q}%")->orWhere('code', 'like', "%{$q}%"))
            ->when($cat, fn($query) => $query->where('category', $cat))
            ->orderBy('category')
            ->orderBy('name')
            ->paginate(50);

        $categories = DB::table('lab_test_directory')
            ->select('category', DB::raw('count(*) as cnt'))
            ->groupBy('category')
            ->orderBy('category')
            ->get();

        $totalTests = DB::table('lab_test_directory')->count();
        $customTests = DB::table('lab_test_directory')->where('code', 'like', 'CUST-%')->count();

        return view('admin.lab-directory.index', compact('tests', 'categories', 'q', 'cat', 'totalTests', 'customTests'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:20|unique:lab_test_directory,code',
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:50',
            'sample_type' => 'required|string|max:50',
            'aliases' => 'nullable|string',
            'default_parameters' => 'nullable|string',
            'preferred_sample' => 'nullable|string|max:255',
            'tat' => 'nullable|string|max:100',
        ]);

        DB::table('lab_test_directory')->insert([
            'code' => strtoupper($request->code),
            'name' => $request->name,
            'category' => $request->category,
            'sample_type' => $request->sample_type,
            'aliases' => $request->aliases ? json_encode(array_map('trim', explode(',', $request->aliases))) : json_encode([$request->name]),
            'default_parameters' => $request->default_parameters ? json_encode(array_map('trim', explode(',', $request->default_parameters))) : null,
            'preferred_sample' => $request->preferred_sample,
            'tat' => $request->tat,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', "Test '{$request->name}' ({$request->code}) added to directory.");
    }

    public function update(Request $request, string $code)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:50',
            'sample_type' => 'required|string|max:50',
            'aliases' => 'nullable|string',
            'default_parameters' => 'nullable|string',
            'preferred_sample' => 'nullable|string|max:255',
            'tat' => 'nullable|string|max:100',
        ]);

        DB::table('lab_test_directory')->where('code', $code)->update([
            'name' => $request->name,
            'category' => $request->category,
            'sample_type' => $request->sample_type,
            'aliases' => $request->aliases ? json_encode(array_map('trim', explode(',', $request->aliases))) : null,
            'default_parameters' => $request->default_parameters ? json_encode(array_map('trim', explode(',', $request->default_parameters))) : null,
            'preferred_sample' => $request->preferred_sample,
            'tat' => $request->tat,
            'updated_at' => now(),
        ]);

        return back()->with('success', "Test '{$code}' updated.");
    }

    public function destroy(string $code)
    {
        // Don't delete if any lab/clinic is using it
        $inUse = DB::table('clinic_lab_tests')->where('test_code', $code)->exists()
              || DB::table('external_lab_offerings')->where('test_code', $code)->exists();

        if ($inUse) {
            return back()->with('error', "Cannot delete '{$code}' — it's being used by labs/clinics.");
        }

        DB::table('lab_test_directory')->where('code', $code)->delete();
        return back()->with('success', "Test '{$code}' deleted from directory.");
    }
}
