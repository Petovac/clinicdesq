<?php

namespace App\Http\Controllers\Vet;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\LabOrder;
use App\Models\LabOrderTest;
use App\Models\LabResult;
use App\Models\LabTestCatalog;
use App\Models\ExternalLabTest;
use App\Models\ExternalLab;
use App\Models\DiagnosticReport;
use App\Models\DiagnosticFile;
use App\Models\Clinic;

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

        $reviewCount = LabOrder::where('vet_id', $vetId)
            ->whereIn('status', ['results_uploaded', 'vet_review'])
            ->count();

        return view('vet.lab-orders.index', compact('orders', 'status', 'reviewCount'));
    }

    /**
     * Get available tests for the doctor dropdown.
     * Returns in-house tests (filtered by availability) + external lab tests (city-matched).
     */
    public function availableTests(Request $request)
    {
        $clinicId = session('active_clinic_id');
        abort_if(!$clinicId, 403);

        $clinic = Clinic::with('organisation')->findOrFail($clinicId);
        $orgId = $clinic->organisation_id;
        $q = trim($request->get('q', ''));
        if (strlen($q) < 1) return response()->json(['tests' => [], 'vet_can_select_lab' => false]);

        // 1. Search org's lab test catalog by name or code
        $catalogTests = LabTestCatalog::where('organisation_id', $orgId)
            ->where('is_active', true)
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                      ->orWhere('code', 'like', "%{$q}%");
            })
            ->orderBy('category')
            ->orderBy('name')
            ->limit(30)
            ->get();

        // 2. Check in-house availability for this clinic
        $catalogIds = $catalogTests->pluck('id')->toArray();
        $availability = \DB::table('lab_test_availability')
            ->where('clinic_id', $clinicId)
            ->where('is_available', true)
            ->whereIn('lab_test_catalog_id', $catalogIds)
            ->pluck('lab_test_catalog_id')
            ->toArray();

        // 3. Get connected external labs (org-level)
        $connectedLabIds = \DB::table('organisation_lab')
            ->where('organisation_id', $orgId)
            ->where('is_active', true)
            ->pluck('external_lab_id')
            ->toArray();

        // 4. Get external lab test offerings matching search
        $extOfferings = collect();
        if (!empty($connectedLabIds)) {
            $extOfferings = ExternalLabTest::whereIn('external_lab_id', $connectedLabIds)
                ->where('is_active', true)
                ->where(function ($query) use ($q) {
                    $query->where('test_name', 'like', "%{$q}%")
                          ->orWhere('test_code', 'like', "%{$q}%");
                })
                ->with('lab:id,name')
                ->limit(30)
                ->get();
        }

        // 5. Build unified results
        $unified = [];

        // In-house tests from catalog
        foreach ($catalogTests as $test) {
            $isAvailable = in_array($test->id, $availability);
            if (!$isAvailable) continue;

            $params = $test->parameters ? json_decode($test->parameters, true) : null;
            $code = $test->code ?: 'CAT-' . $test->id;

            // Check if already in unified
            if (!isset($unified[$code])) {
                $unified[$code] = [
                    'name' => $test->name,
                    'code' => $code,
                    'category' => $test->category,
                    'sample_type' => $test->sample_type,
                    'labs' => [],
                ];
            }

            $unified[$code]['labs'][] = [
                'id' => $test->id,
                'type' => 'in_house',
                'lab_name' => 'In-house',
                'lab_id' => null,
                'price' => (float) $test->price,
                'available' => true,
                'parameters' => $params,
            ];
        }

        // External lab offerings
        foreach ($extOfferings as $ext) {
            $code = $ext->test_code ?: 'EXT-' . $ext->id;
            $params = $ext->parameters ? json_decode($ext->parameters, true) : null;
            $price = $ext->org_selling_price ?? $ext->b2b_price;

            if (!isset($unified[$code])) {
                $unified[$code] = [
                    'name' => $ext->test_name,
                    'code' => $code,
                    'category' => $ext->category,
                    'sample_type' => $ext->sample_type,
                    'labs' => [],
                ];
            }

            $unified[$code]['labs'][] = [
                'id' => $ext->id,
                'type' => 'external',
                'lab_name' => $ext->lab->name ?? 'External Lab',
                'lab_id' => $ext->external_lab_id,
                'price' => (float) $price,
                'available' => true,
                'parameters' => $params,
            ];
        }

        $vetCanSelectLab = $clinic->organisation->vet_can_select_lab ?? false;

        return response()->json([
            'tests' => array_values($unified),
            'vet_can_select_lab' => $vetCanSelectLab,
        ]);
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
            'tests.*.type' => 'required|in:in_house,external',
            'tests.*.catalog_id' => 'nullable|integer',
            'tests.*.external_test_id' => 'nullable|integer',
            'tests.*.parameters' => 'nullable|array',
            'tests.*.price' => 'nullable|numeric',
            'tests.*.lab_id' => 'nullable|integer',
            'priority' => 'required|in:routine,urgent,stat',
            'notes' => 'nullable|string|max:2000',
            'lab_id' => 'nullable|integer|exists:external_labs,id',
            'routing' => 'nullable|in:pending,in_house,external',
        ]);

        // Determine routing based on tests
        $labId = $request->lab_id ?? null;
        $tests = collect($request->tests);

        // Check if all tests are in-house or all for one external lab
        $allInHouse = $tests->every(fn($t) => ($t['type'] ?? 'in_house') === 'in_house');
        $allExternal = $tests->every(fn($t) => ($t['type'] ?? 'in_house') === 'external');

        if ($allInHouse) {
            $routing = 'in_house';
        } elseif ($allExternal && $labId) {
            $routing = 'external';
        } elseif ($labId) {
            $routing = 'external';
        } else {
            $routing = 'pending';
        }

        $order = LabOrder::create([
            'order_number' => LabOrder::generateOrderNumber(),
            'appointment_id' => $appointment->id,
            'pet_id' => $appointment->pet_id,
            'clinic_id' => $appointment->clinic_id,
            'vet_id' => $vetId,
            'lab_id' => $labId,
            'routing' => $routing,
            'status' => $routing !== 'pending' ? 'routed' : 'ordered',
            'priority' => $request->priority,
            'notes' => $request->notes,
            'routed_at' => $routing !== 'pending' ? now() : null,
        ]);

        foreach ($request->tests as $test) {
            LabOrderTest::create([
                'lab_order_id' => $order->id,
                'lab_test_catalog_id' => $test['catalog_id'] ?? null,
                'external_lab_test_id' => $test['external_test_id'] ?? null,
                'external_lab_id' => $test['external_lab_id'] ?? null,
                'test_name' => $test['name'],
                'parameters' => $test['parameters'] ?? null,
                'price' => $test['price'] ?? 0,
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

        $order->results()->update([
            'vet_approved' => true,
            'vet_approved_at' => now(),
            'vet_notes' => $request->vet_notes,
            'visible_to_client' => true,
        ]);

        $order->update(['status' => 'approved', 'completed_at' => now()]);

        // Auto-create DiagnosticReport so results show in case sheet + pet history
        $this->createDiagnosticFromLabOrder($order);

        return back()->with('success', 'Lab results approved and visible on case sheet and pet profile.');
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

        return back()->with('success', 'Retest requested.');
    }

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
     * Create DiagnosticReport from approved lab order → integrates with case sheet + pet history.
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
            'title' => 'Lab: ' . $testNames,
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
