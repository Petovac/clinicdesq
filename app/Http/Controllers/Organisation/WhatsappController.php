<?php

namespace App\Http\Controllers\Organisation;

use App\Http\Controllers\Controller;
use App\Models\WhatsappConfig;
use App\Models\WhatsappMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WhatsappController extends Controller
{
    /**
     * WhatsApp settings page
     */
    public function settings()
    {
        $orgId = Auth::user()->organisation_id;
        $config = WhatsappConfig::firstOrNew(['organisation_id' => $orgId]);

        $recentMessages = WhatsappMessage::where('organisation_id', $orgId)
            ->latest()
            ->limit(20)
            ->get();

        $stats = [
            'total' => WhatsappMessage::where('organisation_id', $orgId)->count(),
            'sent' => WhatsappMessage::where('organisation_id', $orgId)->where('status', 'sent')->count(),
            'delivered' => WhatsappMessage::where('organisation_id', $orgId)->where('status', 'delivered')->count(),
            'failed' => WhatsappMessage::where('organisation_id', $orgId)->where('status', 'failed')->count(),
        ];

        return view('organisation.whatsapp.settings', compact('config', 'recentMessages', 'stats'));
    }

    /**
     * Save WhatsApp config
     */
    public function saveSettings(Request $request)
    {
        $request->validate([
            'api_key' => 'required|string',
            'integrated_number_id' => 'required|string',
            'whatsapp_number' => 'required|string|max:20',
        ]);

        $orgId = Auth::user()->organisation_id;

        WhatsappConfig::updateOrCreate(
            ['organisation_id' => $orgId],
            [
                'provider' => 'msg91',
                'api_key' => $request->api_key,
                'integrated_number_id' => $request->integrated_number_id,
                'whatsapp_number' => $request->whatsapp_number,
                'is_active' => $request->boolean('is_active', true),
                'send_case_sheet' => $request->boolean('send_case_sheet', true),
                'send_prescription' => $request->boolean('send_prescription', true),
                'send_bill' => $request->boolean('send_bill', true),
                'send_lab_report' => $request->boolean('send_lab_report', true),
            ]
        );

        return redirect()->back()->with('success', 'WhatsApp settings saved successfully.');
    }

    /**
     * Message log
     */
    public function messages(Request $request)
    {
        $orgId = Auth::user()->organisation_id;

        $messages = WhatsappMessage::where('organisation_id', $orgId)
            ->when($request->type, fn($q) => $q->where('message_type', $request->type))
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(30);

        return view('organisation.whatsapp.messages', compact('messages'));
    }
}
