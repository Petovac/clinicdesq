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
            $q->where('organisation_id', $orgId)->orWhereNull('organisation_id');
        }])->get();

        return view('organisation.labs.index', compact('tiedUpLabs'));
    }

    public function labsCreate()
    {
        return view('organisation.labs.create');
    }

    public function labsStore(Request $request)
    {
        $orgId = auth()->user()->organisation_id;

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:10',
        ]);

        $lab = ExternalLab::create($data);

        // Create org tie-up
        $lab->organisations()->attach($orgId, ['is_active' => true]);

        return redirect()->route('organisation.labs.index')
            ->with('success', 'External lab onboarded successfully.');
    }

    public function labsEdit(ExternalLab $lab)
    {
        $orgId = auth()->user()->organisation_id;
        abort_unless($lab->organisations()->where('organisation_id', $orgId)->exists(), 403);

        $lab->load(['testOfferings' => function ($q) use ($orgId) {
            $q->where('organisation_id', $orgId)->orWhereNull('organisation_id');
        }, 'users']);

        return view('organisation.labs.edit', compact('lab'));
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
