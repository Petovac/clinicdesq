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

        // Auto-link to standard test code from directory
        if (!empty($data['code'])) {
            $match = \DB::table('lab_test_directory')
                ->where('code', strtoupper($data['code']))
                ->first();
            if ($match) {
                $data['standard_test_code'] = $match->code;
            }
        }

        // If no code match, try matching by name/aliases
        if (empty($data['standard_test_code'])) {
            $match = \DB::table('lab_test_directory')
                ->where('name', 'like', '%' . $data['name'] . '%')
                ->orWhere('aliases', 'like', '%' . $data['name'] . '%')
                ->first();
            if ($match) {
                $data['standard_test_code'] = $match->code;
                if (empty($data['code'])) {
                    $data['code'] = $match->code;
                }
            }
        }

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

    public function labsIndex(Request $request)
    {
        $orgId = auth()->user()->organisation_id;

        // Get org's clinic cities for matching
        $orgCities = Clinic::where('organisation_id', $orgId)
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->pluck('city')
            ->unique()
            ->values();

        // Tied-up labs (accepted)
        $tiedUpLabs = ExternalLab::whereHas('organisations', function ($q) use ($orgId) {
            $q->where('organisation_id', $orgId)->where('organisation_lab.status', 'accepted');
        })->with(['testOfferings' => function ($q) use ($orgId) {
            $q->where('organisation_id', $orgId);
        }])->get();

        // Pending requests (sent by org, waiting lab acceptance)
        $pendingLabs = ExternalLab::whereHas('organisations', function ($q) use ($orgId) {
            $q->where('organisation_id', $orgId)->where('organisation_lab.status', 'pending');
        })->get();

        // Available labs in same cities (not yet connected)
        $search = $request->get('q', '');
        $availableLabs = ExternalLab::where('is_active', true)
            ->whereDoesntHave('organisations', fn($q) => $q->where('organisation_id', $orgId))
            ->when($search, fn($q) => $q->where(fn($q2) => $q2->where('name', 'like', "%{$search}%")->orWhere('city', 'like', "%{$search}%")))
            ->when(!$search && $orgCities->isNotEmpty(), fn($q) => $q->whereIn('city', $orgCities))
            ->orderBy('name')
            ->limit(50)
            ->get();

        return view('organisation.labs.index', compact('tiedUpLabs', 'pendingLabs', 'availableLabs', 'search', 'orgCities'));
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

        // Check not already tied up or pending
        if ($lab->organisations()->where('organisation_id', $orgId)->exists()) {
            return back()->with('error', 'A request to this lab already exists.');
        }

        $lab->organisations()->attach($orgId, [
            'is_active' => false,
            'status' => 'pending',
            'requested_at' => now(),
        ]);

        return redirect()->route('organisation.labs.index')
            ->with('success', "Request sent to '{$lab->name}'. They will review and accept your request.");
    }

    /**
     * Import test offerings from an external lab (copy lab's offerings as ExternalLabTest with org pricing).
     */
    public function labsImportTests(ExternalLab $lab)
    {
        $orgId = auth()->user()->organisation_id;
        abort_unless($lab->organisations()->where('organisation_id', $orgId)->exists(), 403);

        // Get already-imported test codes for this org
        $existingTestCodes = ExternalLabTest::where('external_lab_id', $lab->id)
            ->where('organisation_id', $orgId)
            ->pluck('test_code')
            ->toArray();

        // Read from lab's offerings (external_lab_offerings) — joined with directory for metadata
        $offerings = \DB::table('external_lab_offerings')
            ->leftJoin('lab_test_directory', 'external_lab_offerings.test_code', '=', 'lab_test_directory.code')
            ->where('external_lab_offerings.external_lab_id', $lab->id)
            ->where('external_lab_offerings.is_active', true)
            ->whereNotIn('external_lab_offerings.test_code', $existingTestCodes)
            ->select(
                'external_lab_offerings.test_code',
                'external_lab_offerings.b2b_price',
                'external_lab_offerings.estimated_time',
                'external_lab_offerings.parameters',
                'lab_test_directory.name as dir_name',
                'lab_test_directory.category as dir_category',
                'lab_test_directory.sample_type as dir_sample_type'
            )
            ->get();

        $imported = 0;
        foreach ($offerings as $off) {
            ExternalLabTest::create([
                'external_lab_id' => $lab->id,
                'organisation_id' => $orgId,
                'test_name' => $off->dir_name ?? $off->test_code,
                'test_code' => $off->test_code,
                'category' => $off->dir_category ?? 'other',
                'sample_type' => $off->dir_sample_type ?? 'blood',
                'parameters' => $off->parameters,
                'estimated_time' => $off->estimated_time,
                'b2b_price' => $off->b2b_price,
                'org_selling_price' => $off->b2b_price, // default: same as B2B, org can adjust
                'is_active' => true,
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

        // Count how many tests the lab offers (from external_lab_offerings)
        $masterTestCount = \DB::table('external_lab_offerings')
            ->where('external_lab_id', $lab->id)
            ->where('is_active', true)
            ->count();

        // Clinics for assignment
        $clinics = Clinic::where('organisation_id', $orgId)->orderBy('name')->get();
        $assignedClinicIds = \DB::table('clinic_external_lab')
            ->where('external_lab_id', $lab->id)
            ->where('is_active', true)
            ->pluck('clinic_id')
            ->toArray();

        return view('organisation.labs.edit', compact('lab', 'masterTestCount', 'clinics', 'assignedClinicIds'));
    }

    /**
     * Assign/update which clinics can use this external lab.
     */
    public function labsAssignClinics(Request $request, ExternalLab $lab)
    {
        $orgId = auth()->user()->organisation_id;
        abort_unless($lab->organisations()->where('organisation_id', $orgId)->exists(), 403);

        $selectedClinicIds = $request->input('clinic_ids', []);

        // Get all org clinics
        $allClinicIds = Clinic::where('organisation_id', $orgId)->pluck('id')->toArray();

        // Sync: remove unselected, add selected
        foreach ($allClinicIds as $clinicId) {
            if (in_array($clinicId, $selectedClinicIds)) {
                \DB::table('clinic_external_lab')->updateOrInsert(
                    ['clinic_id' => $clinicId, 'external_lab_id' => $lab->id],
                    ['is_active' => true, 'updated_at' => now()]
                );
            } else {
                \DB::table('clinic_external_lab')
                    ->where('clinic_id', $clinicId)
                    ->where('external_lab_id', $lab->id)
                    ->update(['is_active' => false, 'updated_at' => now()]);
            }
        }

        return back()->with('success', 'Clinic assignments updated for ' . $lab->name);
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

    public function labTechUpdate(Request $request, LabUser $labUser)
    {
        $orgId = auth()->user()->organisation_id;
        abort_if($labUser->organisation_id !== $orgId, 403);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:lab_users,email,' . $labUser->id,
            'phone' => 'nullable|string|max:20',
            'clinic_id' => 'required|exists:clinics,id',
            'role' => 'required|in:lab_tech,lab_admin',
            'password' => 'nullable|string|min:6',
        ]);

        // Verify clinic belongs to org
        Clinic::where('id', $data['clinic_id'])
            ->where('organisation_id', $orgId)
            ->firstOrFail();

        $labUser->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'clinic_id' => $data['clinic_id'],
            'role' => $data['role'],
        ]);

        if (!empty($data['password'])) {
            $labUser->update(['password' => Hash::make($data['password'])]);
        }

        return back()->with('success', 'Lab technician updated.');
    }
}
