<?php

namespace App\Http\Controllers\Vet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\LabOrder;
use App\Models\LabOrderTest;
use App\Models\LabResult;
use App\Models\LabTestCatalog;
use App\Models\DiagnosticReport;
use App\Models\DiagnosticFile;

class LabOrderController extends Controller
{
    public function index(Request $request)
    {
        $vetId = auth('vet')->id();
        $clinicId = session('active_clinic_id');
        $status = $request->get('status');

        $query = LabOrder::where('vet_id', $vetId)
            ->with(['pet', 'clinic', 'tests', 'lab', 'results'])
            ->orderByDesc('created_at');

        if ($clinicId) {
            $query->where('clinic_id', $clinicId);
        }

        if ($status) {
            $query->where('status', $status);
        }

        $orders = $query->paginate(25);

        // Count orders needing review
        $reviewCount = LabOrder::where('vet_id', $vetId)
            ->whereIn('status', ['results_uploaded', 'vet_review'])
            ->count();

        return view('vet.lab-orders.index', compact('orders', 'status', 'reviewCount'));
    }

    public function store(Request $request, Appointment $appointment)
    {
        $vetId = auth('vet')->id();
        $clinicId = session('active_clinic_id');

        abort_if(!$clinicId, 403);
        abort_if($appointment->clinic_id !== (int) $clinicId, 403);
        abort_if($appointment->vet_id !== $vetId, 403);

        $request->validate([
            'tests' => 'required|array|min:1',
            'tests.*.name' => 'required|string|max:255',
            'tests.*.catalog_id' => 'nullable|integer',
            'priority' => 'required|in:routine,urgent',
            'notes' => 'nullable|string|max:2000',
        ]);

        $order = LabOrder::create([
            'order_number' => LabOrder::generateOrderNumber(),
            'appointment_id' => $appointment->id,
            'pet_id' => $appointment->pet_id,
            'clinic_id' => $appointment->clinic_id,
            'vet_id' => $vetId,
            'status' => 'ordered',
            'priority' => $request->priority,
            'notes' => $request->notes,
        ]);

        foreach ($request->tests as $test) {
            LabOrderTest::create([
                'lab_order_id' => $order->id,
                'lab_test_catalog_id' => $test['catalog_id'] ?? null,
                'test_name' => $test['name'],
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'order' => $order->load('tests'),
            ]);
        }

        return redirect()->route('vet.lab-orders.show', $order)
            ->with('success', 'Lab order created: ' . $order->order_number);
    }

    public function show(LabOrder $order)
    {
        $vetId = auth('vet')->id();
        abort_if($order->vet_id !== $vetId, 403);

        $order->load(['pet', 'clinic', 'vet', 'lab', 'tests.results', 'results', 'appointment']);

        return view('vet.lab-orders.show', compact('order'));
    }

    public function approve(Request $request, LabOrder $order)
    {
        $vetId = auth('vet')->id();
        abort_if($order->vet_id !== $vetId, 403);
        abort_if(!in_array($order->status, ['results_uploaded', 'vet_review']), 422);

        $request->validate([
            'vet_notes' => 'nullable|string|max:2000',
        ]);

        // Approve all results
        $order->results()->update([
            'vet_approved' => true,
            'vet_approved_at' => now(),
            'vet_notes' => $request->vet_notes,
            'visible_to_client' => true,
        ]);

        $order->update(['status' => 'approved', 'completed_at' => now()]);

        // Auto-create DiagnosticReport so results appear in pet history
        $this->createDiagnosticFromLabOrder($order);

        return back()->with('success', 'Lab results approved and visible to client.');
    }

    public function requestRetest(Request $request, LabOrder $order)
    {
        $vetId = auth('vet')->id();
        abort_if($order->vet_id !== $vetId, 403);
        abort_if(!in_array($order->status, ['results_uploaded', 'vet_review']), 422);

        $request->validate([
            'retest_reason' => 'required|string|max:2000',
        ]);

        $order->results()->update([
            'retest_requested' => true,
            'retest_reason' => $request->retest_reason,
        ]);

        $order->update(['status' => 'retest_requested']);

        return back()->with('success', 'Retest requested. Lab has been notified.');
    }

    /**
     * Search lab test catalog for autocomplete.
     */
    public function searchTests(Request $request)
    {
        $clinicId = session('active_clinic_id');
        abort_if(!$clinicId, 403);

        $q = $request->get('q', '');

        $tests = LabTestCatalog::where('clinic_id', $clinicId)
            ->where('is_active', true)
            ->where('name', 'like', "%{$q}%")
            ->limit(20)
            ->get(['id', 'name', 'code', 'category', 'sample_type']);

        return response()->json($tests);
    }

    /**
     * Download a lab result file.
     */
    public function downloadResult(LabResult $result)
    {
        $vetId = auth('vet')->id();
        abort_if($result->labOrder->vet_id !== $vetId, 403);

        return response()->download(
            storage_path('app/private/' . $result->file_path),
            $result->original_filename
        );
    }

    /**
     * Create a DiagnosticReport from approved lab order (for pet history integration).
     */
    private function createDiagnosticFromLabOrder(LabOrder $order): void
    {
        $order->load(['tests', 'results']);

        $testNames = $order->tests->pluck('test_name')->implode(', ');

        $report = DiagnosticReport::create([
            'appointment_id' => $order->appointment_id,
            'pet_id' => $order->pet_id,
            'clinic_id' => $order->clinic_id,
            'vet_id' => $order->vet_id,
            'type' => 'lab',
            'title' => 'Lab Order ' . $order->order_number . ': ' . $testNames,
            'report_date' => now(),
            'lab_or_center' => $order->lab ? $order->lab->name : 'In-house',
            'summary' => $order->results->pluck('summary')->filter()->implode("\n"),
        ]);

        foreach ($order->results as $result) {
            if ($result->file_path) {
                DiagnosticFile::create([
                    'diagnostic_report_id' => $report->id,
                    'original_filename' => $result->original_filename,
                    'display_name' => $result->original_filename,
                    'storage_path' => $result->file_path,
                    'mime_type' => $result->mime_type,
                    'file_size' => $result->file_size,
                    'extracted_text' => $result->extracted_text,
                    'ai_summary' => $result->summary,
                    'status' => 'uploaded',
                ]);
            }
        }
    }
}
