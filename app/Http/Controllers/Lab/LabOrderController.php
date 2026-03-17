<?php

namespace App\Http\Controllers\Lab;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LabOrder;
use App\Models\LabOrderTest;
use App\Models\LabResult;

class LabOrderController extends Controller
{
    public function index(Request $request)
    {
        $labId = auth('lab')->user()->external_lab_id;
        $status = $request->get('status');

        $query = LabOrder::where('lab_id', $labId)
            ->with(['pet', 'clinic', 'vet', 'tests'])
            ->orderByDesc('created_at');

        if ($status) {
            $query->where('status', $status);
        }

        $orders = $query->paginate(25);

        return view('lab.orders.index', compact('orders', 'status'));
    }

    public function show(LabOrder $order)
    {
        $labId = auth('lab')->user()->external_lab_id;
        abort_if($order->lab_id !== $labId, 403);

        $order->load(['pet', 'clinic', 'vet', 'tests.results', 'results']);

        return view('lab.orders.show', compact('order'));
    }

    public function startProcessing(LabOrder $order)
    {
        $labId = auth('lab')->user()->external_lab_id;
        abort_if($order->lab_id !== $labId, 403);
        abort_if(!in_array($order->status, ['routed', 'retest_requested']), 422);

        $order->update(['status' => 'processing']);
        $order->tests()->update(['status' => 'processing']);

        return back()->with('success', 'Order marked as processing.');
    }

    public function uploadResult(Request $request, LabOrder $order, LabOrderTest $test)
    {
        $labId = auth('lab')->user()->external_lab_id;
        abort_if($order->lab_id !== $labId, 403);
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
            'uploaded_by_lab_id' => auth('lab')->id(),
            'summary' => $request->notes,
        ]);

        $test->update(['status' => 'completed']);

        return back()->with('success', "Result uploaded for {$test->test_name}.");
    }

    public function markComplete(LabOrder $order)
    {
        $labId = auth('lab')->user()->external_lab_id;
        abort_if($order->lab_id !== $labId, 403);

        $order->update([
            'status' => 'results_uploaded',
            'completed_at' => now(),
        ]);

        return back()->with('success', 'All results submitted. Awaiting vet review.');
    }
}
