<?php

namespace App\Http\Controllers\Organisation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ModuleSettingsController extends Controller
{
    private function resolveOrg()
    {
        $user = auth()->user();
        // Try direct relationship first, then derive from clinic
        $org = $user->organisation;
        if (!$org && $user->clinic) {
            $org = $user->clinic->organisation;
        }
        return $org;
    }

    public function index()
    {
        $org = $this->resolveOrg();
        abort_if(!$org, 404, 'Organisation not found.');

        $modules = $org->modules ?? [
            'inventory' => true,
            'billing'   => true,
            'lab'       => true,
        ];

        return view('organisation.settings.modules', compact('org', 'modules'));
    }

    public function update(Request $request)
    {
        $org = $this->resolveOrg();
        abort_if(!$org, 404, 'Organisation not found.');

        $modules = [
            'inventory' => (bool) $request->input('inventory', false),
            'billing'   => (bool) $request->input('billing', false),
            'lab'       => (bool) $request->input('lab', false),
        ];

        $org->update(['modules' => $modules]);

        return response()->json(['success' => true, 'modules' => $modules]);
    }
}
