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

        // Pending connection requests from orgs
        $pendingRequests = \DB::table('organisation_lab')
            ->join('organisations', 'organisations.id', '=', 'organisation_lab.organisation_id')
            ->where('organisation_lab.external_lab_id', $labId)
            ->where('organisation_lab.status', 'pending')
            ->select('organisation_lab.*', 'organisations.name as org_name', 'organisations.primary_phone', 'organisations.primary_email')
            ->get();

        return view('lab.dashboard', compact('counts', 'recentOrders', 'pendingRequests'));
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

        // Test availability — lab tech picks from master directory
        $clinicTests = \DB::table('clinic_lab_tests')
            ->where('clinic_id', $clinicId)
            ->get()
            ->keyBy('test_code');

        $allDirectoryTests = \DB::table('lab_test_directory')
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        return view('lab.dashboard', compact('counts', 'recentOrders', 'clinicTests', 'allDirectoryTests'));
    }

    /**
     * Lab tech toggles test availability.
     */
    public function toggleAvailability(\Illuminate\Http\Request $request)
    {
        $user = auth('lab')->user();
        abort_if(!$user->isInHouse(), 403);

        $clinicId = $user->clinic_id;

        // Handle custom test
        if ($request->action === 'custom') {
            $request->validate([
                'custom_name' => 'required|string|max:255',
                'price' => 'required|numeric|min:0',
                'custom_category' => 'nullable|string',
                'custom_sample' => 'nullable|string',
                'parameters' => 'nullable|string|max:2000',
            ]);

            $code = 'CUST-' . strtoupper(substr(md5($request->custom_name . $clinicId), 0, 6));

            \DB::table('lab_test_directory')->insertOrIgnore([
                'code' => $code,
                'name' => $request->custom_name,
                'category' => $request->custom_category ?? 'other',
                'sample_type' => $request->custom_sample ?? 'other',
                'aliases' => json_encode([$request->custom_name]),
                'default_parameters' => $request->parameters ? json_encode(array_map('trim', explode(',', $request->parameters))) : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            \DB::table('clinic_lab_tests')->insertOrIgnore([
                'clinic_id' => $clinicId,
                'test_code' => $code,
                'price' => $request->price,
                'parameters' => $request->parameters ? json_encode(array_map('trim', explode(',', $request->parameters))) : null,
                'is_available' => true,
                'updated_by' => $user->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return back()->with('success', "Custom test '{$request->custom_name}' added.");
        }

        $request->validate([
            'test_code' => 'required|string|exists:lab_test_directory,code',
            'action' => 'required|in:enable,disable,set_price,set_params',
            'price' => 'nullable|numeric|min:0',
            'reason' => 'nullable|string|max:255',
            'parameters' => 'nullable|string|max:2000',
        ]);

        if ($request->action === 'enable') {
            \DB::table('clinic_lab_tests')->updateOrInsert(
                ['clinic_id' => $clinicId, 'test_code' => $request->test_code],
                [
                    'is_available' => true,
                    'price' => $request->price ?? 0,
                    'unavailable_reason' => null,
                    'updated_by' => $user->id,
                    'updated_at' => now(),
                    'created_at' => \DB::raw('COALESCE(created_at, NOW())'),
                ]
            );
        } elseif ($request->action === 'disable') {
            \DB::table('clinic_lab_tests')
                ->where('clinic_id', $clinicId)
                ->where('test_code', $request->test_code)
                ->update([
                    'is_available' => false,
                    'unavailable_reason' => $request->reason,
                    'updated_by' => $user->id,
                    'updated_at' => now(),
                ]);
        } elseif ($request->action === 'set_price') {
            \DB::table('clinic_lab_tests')
                ->where('clinic_id', $clinicId)
                ->where('test_code', $request->test_code)
                ->update([
                    'price' => $request->price,
                    'updated_by' => $user->id,
                    'updated_at' => now(),
                ]);
        } elseif ($request->action === 'set_params') {
            $params = $request->parameters
                ? json_encode(array_map('trim', explode(',', $request->parameters)))
                : null;
            \DB::table('clinic_lab_tests')
                ->where('clinic_id', $clinicId)
                ->where('test_code', $request->test_code)
                ->update([
                    'parameters' => $params,
                    'updated_by' => $user->id,
                    'updated_at' => now(),
                ]);
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Test updated.');
    }

    /**
     * Accept org connection request
     */
    public function acceptOrgRequest(\Illuminate\Http\Request $request)
    {
        $user = auth('lab')->user();
        abort_if(!$user->isExternalLab(), 403);

        \DB::table('organisation_lab')
            ->where('external_lab_id', $user->external_lab_id)
            ->where('organisation_id', $request->organisation_id)
            ->where('status', 'pending')
            ->update([
                'status' => 'accepted',
                'is_active' => true,
                'responded_at' => now(),
            ]);

        return back()->with('success', 'Connection request accepted.');
    }

    /**
     * Reject org connection request
     */
    public function rejectOrgRequest(\Illuminate\Http\Request $request)
    {
        $user = auth('lab')->user();
        abort_if(!$user->isExternalLab(), 403);

        \DB::table('organisation_lab')
            ->where('external_lab_id', $user->external_lab_id)
            ->where('organisation_id', $request->organisation_id)
            ->where('status', 'pending')
            ->update([
                'status' => 'rejected',
                'responded_at' => now(),
            ]);

        return back()->with('success', 'Connection request declined.');
    }
}
