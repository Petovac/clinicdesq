<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LabOrder;
use App\Models\LabResult;
use App\Models\LabOrderTest;
use App\Models\ExternalLab;

class LabOrderController extends Controller
{
    public function index(Request $request)
    {
        $clinicId = session('active_clinic_id');
        abort_if(!$clinicId, 403);

        $status = $request->get('status');

        $query = LabOrder::where('clinic_id', $clinicId)
            ->with(['pet', 'vet', 'tests', 'lab'])
            ->orderByDesc('created_at');

        if ($status) {
            $query->where('status', $status);
        }

        $orders = $query->paginate(25);

        $pendingCount = LabOrder::where('clinic_id', $clinicId)
            ->where('status', 'ordered')
            ->count();

        $labs = ExternalLab::where(function ($q) use ($clinicId) {
            $q->whereHas('organisation', function ($q2) use ($clinicId) {
                $q2->whereHas('clinics', function ($q3) use ($clinicId) {
                    $q3->where('clinics.id', $clinicId);
                });
            })->orWhere('type', 'external');
        })->where('is_active', true)->get();

        return view('clinic.lab-orders.index', compact('orders', 'status', 'pendingCount', 'labs'));
    }

    public function route(Request $request, LabOrder $order)
    {
        $clinicId = session('active_clinic_id');
        abort_if(!$clinicId, 403);
        abort_if($order->clinic_id !== (int) $clinicId, 403);
        abort_if($order->status !== 'ordered', 422);

        $request->validate([
            'routing' => 'required|in:in_house,external',
            'lab_id' => 'required_if:routing,external|nullable|integer|exists:external_labs,id',
        ]);

        $order->update([
            'routing' => $request->routing,
            'lab_id' => $request->routing === 'external' ? $request->lab_id : null,
            'status' => 'routed',
            'routed_by' => auth()->id(),
            'routed_at' => now(),
        ]);

        return back()->with('success', 'Lab order routed successfully.');
    }

    /**
     * Upload results for in-house processing.
     */
    public function uploadInHouseResult(Request $request, LabOrder $order, LabOrderTest $test)
    {
        $clinicId = session('active_clinic_id');
        abort_if(!$clinicId, 403);
        abort_if($order->clinic_id !== (int) $clinicId, 403);
        abort_if($order->routing !== 'in_house', 422);
        abort_if($test->lab_order_id !== $order->id, 404);

        $request->validate([
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:20480',
            'notes' => 'nullable|string|max:2000',
        ]);

        $file = $request->file('file');
        $path = $file->store("lab-results/{$order->id}", 'private');

        LabResult::create([
            'lab_order_id' => $order->id,
            'lab_order_test_id' => $test->id,
            'file_path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'summary' => $request->notes,
        ]);

        $test->update(['status' => 'completed']);

        return back()->with('success', "Result uploaded for {$test->test_name}.");
    }

    public function markInHouseComplete(LabOrder $order)
    {
        $clinicId = session('active_clinic_id');
        abort_if(!$clinicId, 403);
        abort_if($order->clinic_id !== (int) $clinicId, 403);
        abort_if($order->routing !== 'in_house', 422);

        $order->update([
            'status' => 'results_uploaded',
            'completed_at' => now(),
        ]);

        return back()->with('success', 'Results submitted. Awaiting vet review.');
    }
}
