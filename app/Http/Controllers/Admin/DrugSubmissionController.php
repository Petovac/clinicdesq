<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DrugSubmission;
use App\Models\DrugGeneric;
use App\Models\DrugBrand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DrugSubmissionController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');

        $submissions = DrugSubmission::with(['organisation', 'submittedBy', 'generic'])
            ->when($status !== 'all', fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(30);

        $counts = [
            'pending'  => DrugSubmission::where('status', 'pending')->count(),
            'approved' => DrugSubmission::where('status', 'approved')->count(),
            'rejected' => DrugSubmission::where('status', 'rejected')->count(),
        ];

        return view('admin.drug-submissions.index', compact('submissions', 'status', 'counts'));
    }

    public function show(DrugSubmission $submission)
    {
        $submission->load(['organisation', 'submittedBy', 'generic', 'reviewedBy']);

        // If it's a brand submission with no generic linked, search for possible matches
        $possibleGenerics = collect();
        if ($submission->type === 'brand' && !$submission->drug_generic_id && $submission->submitted_generic_name) {
            $possibleGenerics = DrugGeneric::where('name', 'like', '%' . $submission->submitted_generic_name . '%')
                ->limit(10)
                ->get();
        }

        // Check for duplicate brands
        $duplicateBrands = collect();
        if ($submission->brand_name) {
            $duplicateBrands = DrugBrand::where('brand_name', 'like', '%' . $submission->brand_name . '%')
                ->with('generic')
                ->limit(5)
                ->get();
        }

        return view('admin.drug-submissions.show', compact('submission', 'possibleGenerics', 'duplicateBrands'));
    }

    public function approve(Request $request, DrugSubmission $submission)
    {
        abort_if(!$submission->isPending(), 422, 'Submission already reviewed.');

        $request->validate([
            'generic_id' => 'nullable|exists:drug_generics,id',
            'new_generic_name' => 'nullable|string|max:255',
            'drug_class' => 'nullable|string|max:100',
            'brand_name' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request, $submission) {
            $genericId = $request->generic_id ?: $submission->drug_generic_id;

            // If we need to create a new generic
            if (!$genericId && ($request->new_generic_name || $submission->submitted_generic_name)) {
                $generic = DrugGeneric::create([
                    'name' => $request->new_generic_name ?: $submission->submitted_generic_name,
                    'drug_class' => $request->drug_class ?: $submission->drug_class ?: 'Uncategorised',
                    'default_dose_unit' => $submission->default_dose_unit ?: 'mg/kg',
                ]);
                $genericId = $generic->id;
                $submission->created_generic_id = $generic->id;
            }

            // Create the brand in KB
            if ($submission->type === 'brand' && $genericId) {
                $brand = DrugBrand::create([
                    'generic_id' => $genericId,
                    'brand_name' => $request->brand_name ?: $submission->brand_name,
                    'manufacturer' => $request->manufacturer ?: $submission->manufacturer,
                    'form' => $submission->form,
                    'strength_value' => $submission->strength_value,
                    'strength_unit' => $submission->strength_unit,
                    'pack_size' => $submission->pack_size,
                    'pack_unit' => $submission->pack_unit,
                ]);
                $submission->created_brand_id = $brand->id;
            }

            $submission->update([
                'status' => 'approved',
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'review_notes' => $request->review_notes,
                'created_generic_id' => $submission->created_generic_id,
                'created_brand_id' => $submission->created_brand_id,
            ]);
        });

        return redirect()->route('admin.drug-submissions.index')
            ->with('success', 'Submission approved and added to Knowledge Base.');
    }

    public function reject(Request $request, DrugSubmission $submission)
    {
        abort_if(!$submission->isPending(), 422, 'Submission already reviewed.');

        $submission->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'review_notes' => $request->review_notes ?: 'Rejected by admin.',
        ]);

        return redirect()->route('admin.drug-submissions.index')
            ->with('success', 'Submission rejected.');
    }
}
