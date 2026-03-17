<?php

namespace App\Http\Controllers\Lab;

use App\Http\Controllers\Controller;
use App\Models\LabOrder;

class LabDashboardController extends Controller
{
    public function index()
    {
        $labId = auth('lab')->user()->external_lab_id;

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
}
