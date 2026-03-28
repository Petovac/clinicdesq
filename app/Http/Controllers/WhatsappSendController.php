<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Bill;
use App\Models\DiagnosticReport;
use App\Services\PdfService;
use App\Services\WhatsappService;
use Illuminate\Http\Request;

class WhatsappSendController extends Controller
{
    /**
     * Send case sheet via WhatsApp
     */
    public function sendCaseSheet(Request $request, Appointment $appointment)
    {
        $appointment->load(['pet.petParent', 'clinic.organisation', 'caseSheet', 'vet']);

        if (!$appointment->caseSheet) {
            return response()->json(['success' => false, 'message' => 'No case sheet found for this appointment.'], 422);
        }

        $parent = $appointment->pet->petParent;
        if (!$parent || !$parent->phone) {
            return response()->json(['success' => false, 'message' => 'Pet parent phone number not found.'], 422);
        }

        $orgId = $appointment->clinic->organisation_id;

        // Generate PDF
        $pdfPath = PdfService::generateCaseSheet($appointment);

        // Send via WhatsApp
        $message = WhatsappService::sendDocument(
            organisationId: $orgId,
            recipientPhone: $parent->phone,
            recipientName: $parent->name,
            templateName: 'clinicdesq_case_sheet',
            messageType: 'case_sheet',
            filePath: $pdfPath,
            templateVariables: [
                'filename' => 'CaseSheet_' . $appointment->pet->name . '.pdf',
                'body' => [
                    $parent->name,
                    $appointment->pet->name,
                    $appointment->clinic->name,
                    \Carbon\Carbon::parse($appointment->scheduled_at)->format('d M Y'),
                ],
            ],
            clinicId: $appointment->clinic_id,
            referenceType: Appointment::class,
            referenceId: $appointment->id,
            sentBy: auth()->id() ?? auth('vet')->id(),
        );

        return response()->json([
            'success' => $message->status !== 'failed',
            'message' => $message->status === 'failed'
                ? 'Failed to send: ' . $message->error_message
                : 'Case sheet sent to ' . $parent->phone . ' via WhatsApp.',
            'status' => $message->status,
        ]);
    }

    /**
     * Send prescription via WhatsApp
     */
    public function sendPrescription(Request $request, Appointment $appointment)
    {
        $appointment->load(['pet.petParent', 'clinic.organisation', 'prescription.items', 'vet']);

        if (!$appointment->prescription) {
            return response()->json(['success' => false, 'message' => 'No prescription found.'], 422);
        }

        $parent = $appointment->pet->petParent;
        if (!$parent || !$parent->phone) {
            return response()->json(['success' => false, 'message' => 'Pet parent phone number not found.'], 422);
        }

        $orgId = $appointment->clinic->organisation_id;
        $pdfPath = PdfService::generatePrescription($appointment);

        $message = WhatsappService::sendDocument(
            organisationId: $orgId,
            recipientPhone: $parent->phone,
            recipientName: $parent->name,
            templateName: 'clinicdesq_prescription',
            messageType: 'prescription',
            filePath: $pdfPath,
            templateVariables: [
                'filename' => 'Prescription_' . $appointment->pet->name . '.pdf',
                'body' => [
                    $parent->name,
                    $appointment->pet->name,
                    $appointment->vet->name ?? 'Doctor',
                    $appointment->clinic->name,
                ],
            ],
            clinicId: $appointment->clinic_id,
            referenceType: Appointment::class,
            referenceId: $appointment->id,
            sentBy: auth()->id() ?? auth('vet')->id(),
        );

        return response()->json([
            'success' => $message->status !== 'failed',
            'message' => $message->status === 'failed'
                ? 'Failed: ' . $message->error_message
                : 'Prescription sent to ' . $parent->phone . ' via WhatsApp.',
        ]);
    }

    /**
     * Send bill via WhatsApp
     */
    public function sendBill(Request $request, Bill $bill)
    {
        $bill->load(['appointment.pet.petParent', 'clinic.organisation']);

        $parent = $bill->appointment->pet->petParent ?? null;
        if (!$parent || !$parent->phone) {
            return response()->json(['success' => false, 'message' => 'Pet parent phone number not found.'], 422);
        }

        $orgId = $bill->clinic->organisation_id;
        $pdfPath = PdfService::generateBill($bill);

        $message = WhatsappService::sendDocument(
            organisationId: $orgId,
            recipientPhone: $parent->phone,
            recipientName: $parent->name,
            templateName: 'clinicdesq_bill',
            messageType: 'bill',
            filePath: $pdfPath,
            templateVariables: [
                'filename' => 'Invoice_' . $bill->id . '.pdf',
                'body' => [
                    $parent->name,
                    $bill->appointment->pet->name,
                    '₹' . number_format($bill->total_amount, 2),
                    $bill->clinic->name,
                ],
            ],
            clinicId: $bill->clinic_id,
            referenceType: Bill::class,
            referenceId: $bill->id,
            sentBy: auth()->id(),
        );

        return response()->json([
            'success' => $message->status !== 'failed',
            'message' => $message->status === 'failed'
                ? 'Failed: ' . $message->error_message
                : 'Bill sent to ' . $parent->phone . ' via WhatsApp.',
        ]);
    }

    /**
     * Send lab/diagnostic report via WhatsApp
     */
    public function sendLabReport(Request $request, DiagnosticReport $report)
    {
        $report->load(['appointment.pet.petParent', 'appointment.clinic.organisation', 'files']);

        $parent = $report->appointment->pet->petParent ?? null;
        if (!$parent || !$parent->phone) {
            return response()->json(['success' => false, 'message' => 'Pet parent phone number not found.'], 422);
        }

        $orgId = $report->appointment->clinic->organisation_id;
        $pdfPath = PdfService::getLabReportPath($report);

        if (!$pdfPath) {
            return response()->json(['success' => false, 'message' => 'No report file found.'], 422);
        }

        $message = WhatsappService::sendDocument(
            organisationId: $orgId,
            recipientPhone: $parent->phone,
            recipientName: $parent->name,
            templateName: 'clinicdesq_lab_report',
            messageType: 'lab_report',
            filePath: $pdfPath,
            templateVariables: [
                'filename' => 'LabReport_' . $report->title . '.pdf',
                'body' => [
                    $parent->name,
                    $report->appointment->pet->name,
                    $report->title,
                    $report->appointment->clinic->name,
                ],
            ],
            clinicId: $report->appointment->clinic_id,
            referenceType: DiagnosticReport::class,
            referenceId: $report->id,
            sentBy: auth()->id() ?? auth('vet')->id(),
        );

        return response()->json([
            'success' => $message->status !== 'failed',
            'message' => $message->status === 'failed'
                ? 'Failed: ' . $message->error_message
                : 'Lab report sent to ' . $parent->phone . ' via WhatsApp.',
        ]);
    }

    /**
     * MSG91 delivery webhook
     */
    public function webhook(Request $request)
    {
        WhatsappService::handleWebhook($request->all());
        return response()->json(['status' => 'ok']);
    }
}
