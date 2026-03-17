<?php

namespace App\Http\Controllers\Lab;

use App\Http\Controllers\Controller;
use App\Models\LabOrder;
use App\Models\LabTestAvailability;
use App\Models\LabTestCatalog;

class LabDashboardController extends Controller
{
    public function index()
    {
        $user = auth('lab')->user();

        if ($user->isExternalLab()) {
            return $this->externalLabDashboard($user);
        }

        return $this->inHouseLabDashboard($user);
    }

    private function externalLabDashboard($user)
    {
        $labId = $user->external_lab_id;

        $counts = [
            'pending' => LabOrder::where('lab_id', $labId)->where('status', 'routed')->count(),
            'processing' => LabOrder::where('lab_id', $labId)->where('status', 'processing')->count(),
            'uploaded' => LabOrder::where('lab_id', $labId)->where('status', 'results_uploaded')->count(),
            'completed' => LabOrder::where('lab_id', $labId)->where('status', 'approved')->count(),
            'retest' => LabOrder::where('lab_id', $labId)->where('status', 'retest_requested')->count(),
        ];

        $recentOrders = LabOrder::where('lab_id', $labId)
            ->with(['pet', 'clinic', 'tests'])
            ->whereIn('status', ['routed', 'processing', 'retest_requested'])
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        return view('lab.dashboard', compact('counts', 'recentOrders'));
    }

    private function inHouseLabDashboard($user)
    {
        $clinicId = $user->clinic_id;

        $counts = [
            'pending' => LabOrder::where('clinic_id', $clinicId)->where('routing', 'in_house')->where('status', 'routed')->count(),
            'processing' => LabOrder::where('clinic_id', $clinicId)->where('routing', 'in_house')->where('status', 'processing')->count(),
            'uploaded' => LabOrder::where('clinic_id', $clinicId)->where('routing', 'in_house')->where('status', 'results_uploaded')->count(),
            'completed' => LabOrder::where('clinic_id', $clinicId)->where('routing', 'in_house')->where('status', 'approved')->count(),
            'retest' => LabOrder::where('clinic_id', $clinicId)->where('routing', 'in_house')->where('status', 'retest_requested')->count(),
        ];

        $recentOrders = LabOrder::where('clinic_id', $clinicId)
            ->where('routing', 'in_house')
            ->with(['pet', 'clinic', 'tests'])
            ->whereIn('status', ['routed', 'processing', 'retest_requested'])
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        // Test availability management for lab tech
        $orgId = $user->organisation_id;
        $allTests = LabTestCatalog::where('organisation_id', $orgId)
            ->where('is_active', true)
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        $availability = LabTestAvailability::where('clinic_id', $clinicId)
            ->pluck('is_available', 'lab_test_catalog_id');

        return view('lab.dashboard', compact('counts', 'recentOrders', 'allTests', 'availability'));
    }

    /**
     * Lab tech toggles test availability.
     */
    public function toggleAvailability(\Illuminate\Http\Request $request)
    {
        $user = auth('lab')->user();
        abort_if(!$user->isInHouse(), 403);

        $request->validate([
            'test_id' => 'required|integer|exists:lab_test_catalog,id',
            'is_available' => 'required|boolean',
            'reason' => 'nullable|string|max:255',
        ]);

        LabTestAvailability::updateOrCreate(
            [
                'lab_test_catalog_id' => $request->test_id,
                'clinic_id' => $user->clinic_id,
            ],
            [
                'is_available' => $request->is_available,
                'unavailable_reason' => $request->is_available ? null : $request->reason,
                'updated_by' => $user->id,
            ]
        );

        return back()->with('success', 'Test availability updated.');
    }
}
