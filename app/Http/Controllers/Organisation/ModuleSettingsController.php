<?php

namespace App\Http\Controllers\Organisation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ModuleSettingsController extends Controller
{
    public function index()
    {
        $org = auth()->user()->organisation;
        $modules = $org->modules ?? [
            'inventory' => true,
            'billing'   => true,
            'lab'       => true,
        ];

        return view('organisation.settings.modules', compact('org', 'modules'));
    }

    public function update(Request $request)
    {
        $org = auth()->user()->organisation;

        $modules = [
            'inventory' => (bool) $request->input('inventory', false),
            'billing'   => (bool) $request->input('billing', false),
            'lab'       => (bool) $request->input('lab', false),
        ];

        $org->update(['modules' => $modules]);

        return response()->json(['success' => true, 'modules' => $modules]);
    }
}
