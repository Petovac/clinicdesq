<?php

namespace App\Http\Controllers\Organisation;

use App\Http\Controllers\Controller;
use App\Models\WebhookEndpoint;
use App\Models\WebhookDelivery;
use App\Services\WebhookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebhookController extends Controller
{
    public function index()
    {
        $orgId = Auth::user()->organisation_id;

        $endpoints = WebhookEndpoint::where('organisation_id', $orgId)
            ->withCount('deliveries')
            ->latest()
            ->get();

        $availableEvents = WebhookService::availableEvents();

        return view('organisation.webhooks.index', compact('endpoints', 'availableEvents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'url' => 'required|url|max:500',
            'label' => 'nullable|string|max:100',
            'events' => 'required|array|min:1',
            'events.*' => 'in:' . implode(',', array_keys(WebhookService::availableEvents())) . ',*',
        ]);

        $orgId = Auth::user()->organisation_id;

        $endpoint = WebhookEndpoint::create([
            'organisation_id' => $orgId,
            'url' => $request->url,
            'label' => $request->label,
            'secret' => WebhookService::generateSecret(),
            'events' => $request->events,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Webhook endpoint created. Copy your signing secret — it won\'t be shown again.');
    }

    public function toggle(WebhookEndpoint $endpoint)
    {
        abort_if($endpoint->organisation_id !== Auth::user()->organisation_id, 403);

        $endpoint->update([
            'is_active' => !$endpoint->is_active,
            'failure_count' => 0, // Reset failures on re-enable
        ]);

        return redirect()->back()->with('success', 'Webhook ' . ($endpoint->is_active ? 'enabled' : 'disabled') . '.');
    }

    public function destroy(WebhookEndpoint $endpoint)
    {
        abort_if($endpoint->organisation_id !== Auth::user()->organisation_id, 403);

        $endpoint->delete();

        return redirect()->back()->with('success', 'Webhook endpoint deleted.');
    }

    public function deliveries(WebhookEndpoint $endpoint)
    {
        abort_if($endpoint->organisation_id !== Auth::user()->organisation_id, 403);

        $deliveries = $endpoint->deliveries()
            ->latest()
            ->paginate(30);

        return view('organisation.webhooks.deliveries', compact('endpoint', 'deliveries'));
    }

    /**
     * Test webhook by sending a ping event
     */
    public function test(WebhookEndpoint $endpoint)
    {
        abort_if($endpoint->organisation_id !== Auth::user()->organisation_id, 403);

        WebhookService::dispatch($endpoint->organisation_id, 'ping', [
            'message' => 'This is a test webhook from ClinicDesq',
            'timestamp' => now()->toIso8601String(),
        ]);

        return redirect()->back()->with('success', 'Test webhook sent! Check your endpoint for the ping event.');
    }
}
