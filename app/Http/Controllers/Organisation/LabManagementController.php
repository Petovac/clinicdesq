<?php

namespace App\Http\Controllers\Organisation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExternalLab;
use App\Models\ExternalLabTest;
use App\Models\LabTestCatalog;
use App\Models\LabUser;
use App\Models\Clinic;
use Illuminate\Support\Facades\Hash;

class LabManagementController extends Controller
{
    // ─────────────────────────────────────────
    // LAB TEST CATALOG (org-level)
    // ─────────────────────────────────────────

    public function catalogIndex()
    {
        $orgId = auth()->user()->organisation_id;

        $tests = LabTestCatalog::where('organisation_id', $orgId)
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        return view('organisation.lab-catalog.index', compact('tests'));
    }

    public function catalogCreate()
    {
        return view('organisation.lab-catalog.create');
    }

    public function catalogStore(Request $request)
    {
        $orgId = auth()->user()->organisation_id;

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'category' => 'required|string',
            'sample_type' => 'required|string',
            'parameters' => 'nullable|string', // comma-separated, we'll parse
            'estimated_time' => 'nullable|string|max:100',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
        ]);

        $data['organisation_id'] = $orgId;
        $data['parameters'] = $data['parameters']
            ? array_map('trim', explode(',', $data['parameters']))
            : null;

        LabTestCatalog::create($data);

        return redirect()->route('organisation.lab-catalog.index')
            ->with('success', 'Lab test added to catalog.');
    }

    public function catalogEdit(LabTestCatalog $test)
    {
        abort_if($test->organisation_id !== auth()->user()->organisation_id, 403);
        return view('organisation.lab-catalog.edit', compact('test'));
    }

    public function catalogUpdate(Request $request, LabTestCatalog $test)
    {
        abort_if($test->organisation_id !== auth()->user()->organisation_id, 403);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'category' => 'required|string',
            'sample_type' => 'required|string',
            'parameters' => 'nullable|string',
            'estimated_time' => 'nullable|string|max:100',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $data['parameters'] = $data['parameters']
            ? array_map('trim', explode(',', $data['parameters']))
            : null;
        $data['is_active'] = $request->boolean('is_active', true);

        $test->update($data);

        return redirect()->route('organisation.lab-catalog.index')
            ->with('success', 'Lab test updated.');
    }

    public function catalogDestroy(LabTestCatalog $test)
    {
        abort_if($test->organisation_id !== auth()->user()->organisation_id, 403);
        $test->delete();

        return redirect()->route('organisation.lab-catalog.index')
            ->with('success', 'Lab test removed from catalog.');
    }

    // ─────────────────────────────────────────
    // EXTERNAL LABS (onboarding & tie-ups)
    // ─────────────────────────────────────────

    public function labsIndex()
    {
        $orgId = auth()->user()->organisation_id;

        $tiedUpLabs = ExternalLab::whereHas('organisations', function ($q) use ($orgId) {
            $q->where('organisation_id', $orgId);
        })->with(['testOfferings' => function ($q) use ($orgId) {
            $q->where('organisation_id', $orgId);
        }])->get();

        return view('organisation.labs.index', compact('tiedUpLabs'));
    }

    /**
     * Search registered labs (AJAX).
     */
    public function labsSearch(Request $request)
    {
        $orgId = auth()->user()->organisation_id;
        $q = $request->get('q', '');

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $labs = ExternalLab::where('is_active', true)
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('city', 'like', "%{$q}%");
            })
            // Exclude already tied-up labs
            ->whereDoesntHave('organisations', function ($query) use ($orgId) {
                $query->where('organisation_id', $orgId);
            })
            ->limit(15)
            ->get(['id', 'name', 'city', 'state', 'phone', 'email']);

        return response()->json($labs);
    }

    /**
     * Onboard (link) an existing lab to this org.
     */
    public function labsOnboard(Request $request)
    {
        $orgId = auth()->user()->organisation_id;

        $request->validate([
            'lab_id' => 'required|integer|exists:external_labs,id',
        ]);

        $lab = ExternalLab::findOrFail($request->lab_id);

        // Check not already tied up
        if ($lab->organisations()->where('organisation_id', $orgId)->exists()) {
            return back()->with('error', 'This lab is already onboarded.');
        }

        $lab->organisations()->attach($orgId, ['is_active' => true]);

        return redirect()->route('organisation.labs.edit', $lab)
            ->with('success', "Lab '{$lab->name}' onboarded! You can now import their tests and set your pricing.");
    }

    /**
     * Import test offerings from an external lab (copy lab's tests as ExternalLabTest with org pricing).
     */
    public function labsImportTests(ExternalLab $lab)
    {
        $orgId = auth()->user()->organisation_id;
        abort_unless($lab->organisations()->where('organisation_id', $orgId)->exists(), 403);

        // Get lab's tests that don't have org-specific pricing yet
        $existingTestNames = ExternalLabTest::where('external_lab_id', $lab->id)
            ->where('organisation_id', $orgId)
            ->pluck('test_name')
            ->toArray();

        $labTests = ExternalLabTest::where('external_lab_id', $lab->id)
            ->whereNull('organisation_id')
            ->whereNotIn('test_name', $existingTestNames)
            ->get();

        $imported = 0;
        foreach ($labTests as $test) {
            ExternalLabTest::create([
                'external_lab_id' => $lab->id,
                'organisation_id' => $orgId,
                'test_name' => $test->test_name,
                'test_code' => $test->test_code,
                'category' => $test->category,
                'sample_type' => $test->sample_type,
                'parameters' => $test->parameters,
                'estimated_time' => $test->estimated_time,
                'b2b_price' => $test->b2b_price,
                'org_selling_price' => $test->b2b_price, // default: same as B2B, org can adjust
            ]);
            $imported++;
        }

        return back()->with('success', "Imported {$imported} tests from {$lab->name}.");
    }

    /**
     * Update org selling price for an imported test.
     */
    public function labTestUpdatePrice(Request $request, ExternalLabTest $test)
    {
        $orgId = auth()->user()->organisation_id;
        abort_if($test->organisation_id !== $orgId, 403);

        $request->validate([
            'org_selling_price' => 'required|numeric|min:0',
        ]);

        $test->update(['org_selling_price' => $request->org_selling_price]);

        return back()->with('success', "Selling price updated for {$test->test_name}.");
    }

    /**
     * Remove tie-up with an external lab.
     */
    public function labsDetach(ExternalLab $lab)
    {
        $orgId = auth()->user()->organisation_id;
        $lab->organisations()->detach($orgId);

        // Remove org-specific test pricing
        ExternalLabTest::where('external_lab_id', $lab->id)
            ->where('organisation_id', $orgId)
            ->delete();

        return redirect()->route('organisation.labs.index')
            ->with('success', "Lab '{$lab->name}' removed from your organisation.");
    }

    public function labsEdit(ExternalLab $lab)
    {
        $orgId = auth()->user()->organisation_id;
        abort_unless($lab->organisations()->where('organisation_id', $orgId)->exists(), 403);

        // Only load org-specific imported tests (not the lab's master catalog)
        $lab->load(['testOfferings' => function ($q) use ($orgId) {
            $q->where('organisation_id', $orgId);
        }, 'users']);

        // Count how many master tests the lab has (to show "X tests available to import")
        $masterTestCount = ExternalLabTest::where('external_lab_id', $lab->id)
            ->whereNull('organisation_id')
            ->count();

        return view('organisation.labs.edit', compact('lab', 'masterTestCount'));
    }

    public function labsUpdate(Request $request, ExternalLab $lab)
    {
        $orgId = auth()->user()->organisation_id;
        abort_unless($lab->organisations()->where('organisation_id', $orgId)->exists(), 403);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:10',
        ]);

        $lab->update($data);

        return redirect()->route('organisation.labs.index')
            ->with('success', 'External lab updated.');
    }

    /**
     * Store a test offering for an external lab (with org-specific pricing).
     */
    public function labTestStore(Request $request, ExternalLab $lab)
    {
        $orgId = auth()->user()->organisation_id;
        abort_unless($lab->organisations()->where('organisation_id', $orgId)->exists(), 403);

        $data = $request->validate([
            'test_name' => 'required|string|max:255',
            'test_code' => 'nullable|string|max:50',
            'category' => 'nullable|string|max:100',
            'sample_type' => 'nullable|string|max:50',
            'parameters' => 'nullable|string',
            'estimated_time' => 'nullable|string|max:100',
            'b2b_price' => 'required|numeric|min:0',
            'org_selling_price' => 'nullable|numeric|min:0',
        ]);

        $data['external_lab_id'] = $lab->id;
        $data['organisation_id'] = $orgId;
        $data['parameters'] = $data['parameters']
            ? array_map('trim', explode(',', $data['parameters']))
            : null;

        ExternalLabTest::create($data);

        return back()->with('success', 'Test added to lab offerings.');
    }

    // ─────────────────────────────────────────
    // LAB TECH CREATION (in-house)
    // ─────────────────────────────────────────

    public function labTechIndex()
    {
        $orgId = auth()->user()->organisation_id;
        $clinics = Clinic::where('organisation_id', $orgId)->get();

        $labTechs = LabUser::where('organisation_id', $orgId)
            ->with('clinic')
            ->orderBy('name')
            ->get();

        return view('organisation.labs.lab-techs', compact('labTechs', 'clinics'));
    }

    public function labTechStore(Request $request)
    {
        $orgId = auth()->user()->organisation_id;

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:lab_users,email',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:20',
            'clinic_id' => 'required|exists:clinics,id',
            'role' => 'required|in:lab_tech,lab_admin',
        ]);

        // Verify clinic belongs to org
        $clinic = Clinic::where('id', $data['clinic_id'])
            ->where('organisation_id', $orgId)
            ->firstOrFail();

        LabUser::create([
            'organisation_id' => $orgId,
            'clinic_id' => $clinic->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'] ?? null,
            'role' => $data['role'],
        ]);

        return back()->with('success', 'Lab technician created.');
    }

    public function labTechToggle(LabUser $labUser)
    {
        abort_if($labUser->organisation_id !== auth()->user()->organisation_id, 403);

        $labUser->update(['is_active' => !$labUser->is_active]);

        return back()->with('success', $labUser->is_active ? 'Lab tech activated.' : 'Lab tech deactivated.');
    }
}
