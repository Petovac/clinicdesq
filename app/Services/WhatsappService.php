<?php

namespace App\Services;

use App\Models\WhatsappConfig;
use App\Models\WhatsappMessage;
use App\Models\Organisation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class WhatsappService
{
    // MSG91 WhatsApp API base URL
    const MSG91_BASE = 'https://control.msg91.com/api/v5/whatsapp';

    /**
     * Send a document (PDF) via WhatsApp using MSG91
     */
    public static function sendDocument(
        int $organisationId,
        string $recipientPhone,
        string $recipientName,
        string $templateName,
        string $messageType,
        string $filePath,
        array $templateVariables = [],
        ?int $clinicId = null,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?int $sentBy = null
    ): WhatsappMessage {

        $config = WhatsappConfig::where('organisation_id', $organisationId)->first();

        if (!$config || !$config->isConfigured()) {
            return self::createFailedMessage(
                $organisationId, $recipientPhone, $recipientName,
                $templateName, $messageType, $filePath,
                'WhatsApp not configured for this organisation',
                $clinicId, $referenceType, $referenceId, $sentBy
            );
        }

        // Format phone number for India (add 91 prefix)
        $phone = self::formatIndianPhone($recipientPhone);

        // Generate public URL for the PDF
        $fileUrl = self::getPublicUrl($filePath);

        // Build MSG91 payload
        $payload = [
            'integrated_number' => $config->integrated_number_id,
            'content_type' => 'template',
            'payload' => [
                'messaging_product' => 'whatsapp',
                'type' => 'template',
                'template' => [
                    'name' => $templateName,
                    'language' => [
                        'code' => 'en',
                        'policy' => 'deterministic',
                    ],
                    'to_and_components' => [
                        [
                            'to' => [$phone],
                            'components' => self::buildComponents($templateVariables, $fileUrl),
                        ],
                    ],
                ],
            ],
        ];

        // Create message record
        $message = WhatsappMessage::create([
            'organisation_id' => $organisationId,
            'clinic_id' => $clinicId,
            'recipient_phone' => $phone,
            'recipient_name' => $recipientName,
            'template_name' => $templateName,
            'message_type' => $messageType,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'file_path' => $filePath,
            'file_url' => $fileUrl,
            'status' => 'queued',
            'sent_by' => $sentBy,
        ]);

        try {
            $response = Http::withHeaders([
                'authkey' => $config->api_key,
                'Content-Type' => 'application/json',
            ])->post(self::MSG91_BASE . '/template/send', $payload);

            if ($response->successful()) {
                $data = $response->json();
                $message->update([
                    'status' => 'sent',
                    'provider_request_id' => $data['request_id'] ?? $data['id'] ?? null,
                ]);
            } else {
                $message->update([
                    'status' => 'failed',
                    'error_message' => $response->body(),
                ]);
                Log::error('MSG91 WhatsApp send failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'org_id' => $organisationId,
                ]);
            }
        } catch (\Exception $e) {
            $message->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
            Log::error('MSG91 WhatsApp exception', [
                'error' => $e->getMessage(),
                'org_id' => $organisationId,
            ]);
        }

        return $message;
    }

    /**
     * Send a simple text template (no document) — e.g., appointment reminders
     */
    public static function sendTemplate(
        int $organisationId,
        string $recipientPhone,
        string $recipientName,
        string $templateName,
        string $messageType,
        array $templateVariables = [],
        ?int $clinicId = null,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?int $sentBy = null
    ): WhatsappMessage {

        $config = WhatsappConfig::where('organisation_id', $organisationId)->first();

        if (!$config || !$config->isConfigured()) {
            return self::createFailedMessage(
                $organisationId, $recipientPhone, $recipientName,
                $templateName, $messageType, null,
                'WhatsApp not configured for this organisation',
                $clinicId, $referenceType, $referenceId, $sentBy
            );
        }

        $phone = self::formatIndianPhone($recipientPhone);

        $payload = [
            'integrated_number' => $config->integrated_number_id,
            'content_type' => 'template',
            'payload' => [
                'messaging_product' => 'whatsapp',
                'type' => 'template',
                'template' => [
                    'name' => $templateName,
                    'language' => [
                        'code' => 'en',
                        'policy' => 'deterministic',
                    ],
                    'to_and_components' => [
                        [
                            'to' => [$phone],
                            'components' => self::buildTextComponents($templateVariables),
                        ],
                    ],
                ],
            ],
        ];

        $message = WhatsappMessage::create([
            'organisation_id' => $organisationId,
            'clinic_id' => $clinicId,
            'recipient_phone' => $phone,
            'recipient_name' => $recipientName,
            'template_name' => $templateName,
            'message_type' => $messageType,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'status' => 'queued',
            'sent_by' => $sentBy,
        ]);

        try {
            $response = Http::withHeaders([
                'authkey' => $config->api_key,
                'Content-Type' => 'application/json',
            ])->post(self::MSG91_BASE . '/template/send', $payload);

            if ($response->successful()) {
                $data = $response->json();
                $message->update([
                    'status' => 'sent',
                    'provider_request_id' => $data['request_id'] ?? $data['id'] ?? null,
                ]);
            } else {
                $message->update([
                    'status' => 'failed',
                    'error_message' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            $message->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);
        }

        return $message;
    }

    /**
     * Build components for template with document header
     */
    private static function buildComponents(array $variables, string $fileUrl): array
    {
        $components = [];

        // Header component with document
        $components[] = [
            'type' => 'header',
            'parameters' => [
                [
                    'type' => 'document',
                    'document' => [
                        'link' => $fileUrl,
                        'filename' => $variables['filename'] ?? 'document.pdf',
                    ],
                ],
            ],
        ];

        // Body component with text variables
        if (!empty($variables['body'])) {
            $params = [];
            foreach ($variables['body'] as $val) {
                $params[] = ['type' => 'text', 'text' => $val];
            }
            $components[] = [
                'type' => 'body',
                'parameters' => $params,
            ];
        }

        return $components;
    }

    /**
     * Build components for text-only template
     */
    private static function buildTextComponents(array $variables): array
    {
        $components = [];

        if (!empty($variables['body'])) {
            $params = [];
            foreach ($variables['body'] as $val) {
                $params[] = ['type' => 'text', 'text' => $val];
            }
            $components[] = [
                'type' => 'body',
                'parameters' => $params,
            ];
        }

        return $components;
    }

    /**
     * Format Indian phone: strip +, ensure 91 prefix
     */
    public static function formatIndianPhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (strlen($phone) === 10) {
            $phone = '91' . $phone;
        }

        if (strlen($phone) === 12 && str_starts_with($phone, '91')) {
            return $phone;
        }

        // Return as is if it doesn't match expected format
        return $phone;
    }

    /**
     * Get public URL for a stored file
     */
    private static function getPublicUrl(string $path): string
    {
        // If it's already a full URL, return as is
        if (str_starts_with($path, 'http')) {
            return $path;
        }

        // Generate from storage
        return asset('storage/' . $path);
    }

    /**
     * Create a failed message record
     */
    private static function createFailedMessage(
        int $organisationId,
        string $recipientPhone,
        string $recipientName,
        string $templateName,
        string $messageType,
        ?string $filePath,
        string $error,
        ?int $clinicId,
        ?string $referenceType,
        ?int $referenceId,
        ?int $sentBy
    ): WhatsappMessage {
        return WhatsappMessage::create([
            'organisation_id' => $organisationId,
            'clinic_id' => $clinicId,
            'recipient_phone' => $recipientPhone,
            'recipient_name' => $recipientName,
            'template_name' => $templateName,
            'message_type' => $messageType,
            'reference_type' => $referenceType,
            'reference_id' => $referenceId,
            'file_path' => $filePath,
            'status' => 'failed',
            'error_message' => $error,
            'sent_by' => $sentBy,
        ]);
    }

    /**
     * Handle MSG91 webhook for delivery status updates
     */
    public static function handleWebhook(array $data): void
    {
        $requestId = $data['request_id'] ?? $data['id'] ?? null;
        if (!$requestId) return;

        $message = WhatsappMessage::where('provider_request_id', $requestId)->first();
        if (!$message) return;

        $statusMap = [
            'sent' => 'sent',
            'delivered' => 'delivered',
            'read' => 'read',
            'failed' => 'failed',
        ];

        $newStatus = $statusMap[$data['status'] ?? ''] ?? null;
        if ($newStatus) {
            $message->update([
                'status' => $newStatus,
                'error_message' => $data['reason'] ?? $message->error_message,
            ]);
        }
    }
}
