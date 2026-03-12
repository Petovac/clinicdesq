<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Organisation;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class OrganisationController extends Controller
{
    /**
     * List all organisations
     */
    public function index()
    {
        $organisations = Organisation::all();
        return view('admin.organisations.index', compact('organisations'));
    }

    /**
     * Show create organisation form
     */
    public function create()
    {
        return view('admin.organisations.create');
    }

    /**
     * Store organisation + organisation owner
     */
    public function store(Request $request)
    {
        $request->validate([
            'org_name'    => 'required|string|max:255',
            'org_type'    => 'required|in:single_clinic,corporate',
            'owner_name'  => 'required|string|max:255',
            'owner_email' => 'required|email|unique:users,email',
            'owner_phone' => 'required|string|max:20',
        ]);

        DB::transaction(function () use ($request) {

            // 1. Create Organisation
            $organisation = Organisation::create([
                'name'          => $request->org_name,
                'type'          => $request->org_type,
                'primary_email' => $request->owner_email,
                'primary_phone' => $request->owner_phone,
                'is_active'     => 1,
            ]);

            // 2. Create Organisation Owner User
            $user = User::create([
                'name'            => $request->owner_name,
                'email'           => $request->owner_email,
                'phone'           => $request->owner_phone,
                'password'        => Hash::make('changeme123'), // TEMP
                'role'            => 'organisation_owner',     // ✅ GLOBAL ROLE
                'organisation_id' => $organisation->id,        // ✅ SINGLE ORG
                'is_active'       => 1,
            ]);

            // (Optional but recommended)
            // If you later add owner_user_id column
            // $organisation->update(['owner_user_id' => $user->id]);
        });

        return redirect('/admin/organisations')
            ->with('success', 'Organisation and owner created successfully');
    }
}
