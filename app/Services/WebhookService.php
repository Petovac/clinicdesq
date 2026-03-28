<?php

namespace App\Services;

use App\Models\WebhookEndpoint;
use App\Models\WebhookDelivery;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WebhookService
{
    /**
     * Supported webhook events:
     *
     * case_sheet.saved       — Case sheet created or updated
     * prescription.created   — Prescription saved
     * prescription.updated   — Prescription modified
     * bill.confirmed         — Bill confirmed & inventory deducted
     * lab_report.uploaded     — Lab/diagnostic report uploaded
     * lab_result.approved     — Lab result approved by vet
     * appointment.created     — New appointment booked
     * appointment.completed   — Appointment marked complete
     * pet.created            — New pet registered
     * pet_parent.created     — New pet parent registered
     */

    /**
     * Dispatch a webhook event to all subscribed endpoints for this org
     */
    public static function dispatch(int $organisationId, string $event, array $data): void
    {
        $endpoints = WebhookEndpoint::where('organisation_id', $organisationId)
            ->where('is_active', true)
            ->where('failure_count', '<', 10) // Auto-disable after 10 consecutive failures
            ->get();

        foreach ($endpoints as $endpoint) {
            if (!$endpoint->subscribesTo($event)) {
                continue;
            }

            self::deliver($endpoint, $event, $data);
        }
    }

    /**
     * Deliver webhook payload to a single endpoint
     */
    private static function deliver(WebhookEndpoint $endpoint, string $event, array $data): void
    {
        $payload = [
            'event' => $event,
            'timestamp' => now()->toIso8601String(),
            'webhook_id' => Str::uuid()->toString(),
            'organisation_id' => $endpoint->organisation_id,
            'data' => $data,
        ];

        $payloadJson = json_encode($payload);

        // Generate HMAC signature for verification
        $signature = hash_hmac('sha256', $payloadJson, $endpoint->secret);

        $delivery = WebhookDelivery::create([
            'webhook_endpoint_id' => $endpoint->id,
            'event' => $event,
            'payload' => $payload,
            'status' => 'pending',
        ]);

        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'X-Webhook-Signature' => $signature,
                    'X-Webhook-Event' => $event,
                    'X-Webhook-Id' => $payload['webhook_id'],
                    'User-Agent' => 'ClinicDesq-Webhook/1.0',
                ])
                ->withBody($payloadJson, 'application/json')
                ->post($endpoint->url);

            if ($response->successful()) {
                $delivery->update([
                    'status' => 'success',
                    'http_status' => $response->status(),
                    'response_body' => Str::limit($response->body(), 1000),
                ]);

                // Reset failure count on success
                $endpoint->update([
                    'failure_count' => 0,
                    'last_triggered_at' => now(),
                ]);
            } else {
                $delivery->update([
                    'status' => 'failed',
                    'http_status' => $response->status(),
                    'response_body' => Str::limit($response->body(), 1000),
                    'error_message' => 'HTTP ' . $response->status(),
                ]);

                $endpoint->increment('failure_count');
                $endpoint->update(['last_triggered_at' => now()]);

                Log::warning('Webhook delivery failed', [
                    'endpoint_id' => $endpoint->id,
                    'event' => $event,
                    'http_status' => $response->status(),
                ]);
            }
        } catch (\Exception $e) {
            $delivery->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            $endpoint->increment('failure_count');

            Log::error('Webhook delivery exception', [
                'endpoint_id' => $endpoint->id,
                'event' => $event,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Generate a random webhook secret
     */
    public static function generateSecret(): string
    {
        return 'whsec_' . bin2hex(random_bytes(24));
    }

    /**
     * List of all available webhook events
     */
    public static function availableEvents(): array
    {
        return [
            'case_sheet.saved' => 'Case sheet created or updated',
            'prescription.created' => 'Prescription saved',
            'bill.confirmed' => 'Bill confirmed',
            'lab_report.uploaded' => 'Lab/diagnostic report uploaded',
            'lab_result.approved' => 'Lab result approved by vet',
            'appointment.created' => 'New appointment booked',
            'appointment.completed' => 'Appointment marked complete',
            'pet.created' => 'New pet registered',
            'pet_parent.created' => 'New pet parent registered',
        ];
    }
}
