<?php

namespace App\Http\Controllers\Lab;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LabCatalogController extends Controller
{
    /**
     * Show directory tests with lab's offerings marked.
     */
    public function index(Request $request)
    {
        $user = auth('lab')->user();
        abort_if(!$user->isExternalLab(), 403);

        $labId = $user->external_lab_id;

        // Get lab's current offerings
        $offerings = DB::table('external_lab_offerings')
            ->where('external_lab_id', $labId)
            ->get()
            ->keyBy('test_code');

        // Get full directory
        $allTests = DB::table('lab_test_directory')
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        return view('lab.catalog.index', compact('allTests', 'offerings'));
    }

    /**
     * Enable a test from directory (or update price).
     */
    public function toggle(Request $request)
    {
        $user = auth('lab')->user();
        abort_if(!$user->isExternalLab(), 403);

        // Handle custom test submission
        if ($request->action === 'custom') {
            $request->validate([
                'custom_name' => 'required|string|max:255',
                'b2b_price' => 'required|numeric|min:0',
                'custom_category' => 'nullable|string',
                'custom_sample' => 'nullable|string',
                'estimated_time' => 'nullable|string|max:50',
                'parameters' => 'nullable|string|max:2000',
                'container_type' => 'nullable|string|max:100',
                'sample_volume' => 'nullable|string|max:50',
            ]);

            $labId = $user->external_lab_id;

            // Generate a temp code
            $code = 'CUST-' . strtoupper(substr(md5($request->custom_name . $labId), 0, 6));

            // Add to directory with pending status (superadmin reviews)
            DB::table('lab_test_directory')->insertOrIgnore([
                'code' => $code,
                'name' => $request->custom_name,
                'category' => $request->custom_category ?? 'other',
                'sample_type' => $request->custom_sample ?? 'other',
                'aliases' => json_encode([$request->custom_name]),
                'default_parameters' => $request->parameters ? json_encode(array_map('trim', explode(',', $request->parameters))) : null,
                'collection_method' => $request->custom_sample ?? 'other',
                'container_type' => $request->container_type,
                'sample_volume' => $request->sample_volume,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Add to lab's offerings
            DB::table('external_lab_offerings')->insertOrIgnore([
                'external_lab_id' => $labId,
                'test_code' => $code,
                'b2b_price' => $request->b2b_price,
                'estimated_time' => $request->estimated_time,
                'parameters' => $request->parameters ? json_encode(array_map('trim', explode(',', $request->parameters))) : null,
                'container_type' => $request->container_type,
                'sample_volume' => $request->sample_volume,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return back()->with('success', "Custom test '{$request->custom_name}' added to your catalog.");
        }

        $request->validate([
            'test_code' => 'required|string|exists:lab_test_directory,code',
            'action' => 'required|in:enable,disable,set_price,set_params',
            'b2b_price' => 'nullable|numeric|min:0',
            'estimated_time' => 'nullable|string|max:50',
            'parameters' => 'nullable|string|max:2000',
        ]);

        $labId = $user->external_lab_id;

        if ($request->action === 'enable') {
            DB::table('external_lab_offerings')->updateOrInsert(
                ['external_lab_id' => $labId, 'test_code' => $request->test_code],
                [
                    'b2b_price' => $request->b2b_price ?? 0,
                    'estimated_time' => $request->estimated_time,
                    'is_active' => true,
                    'updated_at' => now(),
                    'created_at' => DB::raw('COALESCE(created_at, NOW())'),
                ]
            );
        } elseif ($request->action === 'disable') {
            DB::table('external_lab_offerings')
                ->where('external_lab_id', $labId)
                ->where('test_code', $request->test_code)
                ->update(['is_active' => false, 'updated_at' => now()]);
        } elseif ($request->action === 'set_price') {
            $updateData = ['updated_at' => now()];
            if ($request->has('b2b_price') && $request->b2b_price !== null) {
                $updateData['b2b_price'] = $request->b2b_price;
            }
            if ($request->has('estimated_time')) {
                $updateData['estimated_time'] = $request->estimated_time;
            }
            DB::table('external_lab_offerings')
                ->where('external_lab_id', $labId)
                ->where('test_code', $request->test_code)
                ->update($updateData);
        } elseif ($request->action === 'set_params') {
            $params = $request->parameters
                ? json_encode(array_map('trim', explode(',', $request->parameters)))
                : null;
            DB::table('external_lab_offerings')
                ->where('external_lab_id', $labId)
                ->where('test_code', $request->test_code)
                ->update([
                    'parameters' => $params,
                    'updated_at' => now(),
                ]);
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Catalog updated.');
    }
}
