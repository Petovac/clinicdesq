<?php

namespace App\Services;

use App\Models\Bill;
use App\Models\CaseSheet;
use App\Models\Organisation;
use App\Models\Prescription;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class DocumentService
{
    /**
     * Render a prescription as HTML view or PDF download.
     */
    public function renderPrescription(Prescription $prescription, string $mode = 'html')
    {
        $prescription->load('items', 'appointment.pet.petParent', 'appointment.vet', 'appointment.clinic.organisation');
        $context = $this->buildPrescriptionContext($prescription);
        $template = $this->resolveTemplate('prescription', $context['org']);

        return $this->output($template, $context, $mode, "Prescription-{$prescription->id}");
    }

    /**
     * Render a case sheet as HTML view or PDF download.
     */
    public function renderCaseSheet(CaseSheet $caseSheet, string $mode = 'html')
    {
        $caseSheet->load('appointment.pet.petParent', 'appointment.vet', 'appointment.clinic.organisation', 'appointment.treatments.drugGeneric', 'appointment.treatments.priceItem');
        $context = $this->buildCaseSheetContext($caseSheet);
        $template = $this->resolveTemplate('casesheet', $context['org']);

        return $this->output($template, $context, $mode, "CaseSheet-{$caseSheet->id}");
    }

    /**
     * Render a bill as HTML view or PDF download.
     */
    public function renderBill(Bill $bill, string $mode = 'html')
    {
        $bill->load('items', 'appointment.pet.petParent', 'appointment.vet', 'appointment.clinic.organisation');
        $context = $this->buildBillContext($bill);
        $template = $this->resolveTemplate('bill', $context['org']);

        return $this->output($template, $context, $mode, "Bill-{$bill->id}");
    }

    /* ── Private helpers ── */

    private function resolveTemplate(string $documentType, Organisation $org): string
    {
        $field = "template_{$documentType}";
        $templateName = $org->{$field} ?? 'classic';

        // Verify the template exists, fallback to classic
        $viewPath = "documents.{$documentType}.{$templateName}";
        if (!view()->exists($viewPath)) {
            $viewPath = "documents.{$documentType}.classic";
        }

        return $viewPath;
    }

    private function buildBaseContext($appointment): array
    {
        $clinic = $appointment->clinic;
        $org    = $clinic->organisation;
        $vet    = $appointment->vet;

        return [
            'org'             => $org,
            'logoUrl'         => $org->logo_path ? asset('storage/' . $org->logo_path) : null,
            'clinic'          => $clinic,
            'pet'             => $appointment->pet,
            'parent'          => $appointment->pet->petParent,
            'vet'             => $vet,
            'appointment'     => $appointment,
            'date'            => $appointment->scheduled_at?->format('d M Y'),
            'vetSignatureUrl' => $vet && $vet->signature_path ? asset('storage/' . $vet->signature_path) : null,
        ];
    }

    private function buildPrescriptionContext(Prescription $prescription): array
    {
        $ctx = $this->buildBaseContext($prescription->appointment);
        $ctx['prescription'] = $prescription;
        $ctx['items'] = $prescription->items;
        return $ctx;
    }

    private function buildCaseSheetContext(CaseSheet $caseSheet): array
    {
        $ctx = $this->buildBaseContext($caseSheet->appointment);
        $ctx['caseSheet'] = $caseSheet;
        $ctx['drugTreatments'] = $caseSheet->appointment->treatments->filter(fn($t) => $t->drug_generic_id);
        $ctx['procedures'] = $caseSheet->appointment->treatments->filter(fn($t) => !$t->drug_generic_id);
        return $ctx;
    }

    private function buildBillContext(Bill $bill): array
    {
        $ctx = $this->buildBaseContext($bill->appointment);
        $ctx['bill'] = $bill;
        $ctx['items'] = $bill->items->where('status', 'approved');
        $ctx['gstNumber'] = $bill->clinic->gst_number ?: $ctx['org']->gst_number;

        // Calculate GST (assuming inclusive or add-on — we'll show GSTIN info)
        $ctx['subtotal'] = $ctx['items']->sum('total');
        $ctx['totalAmount'] = $bill->total_amount;

        return $ctx;
    }

    private function output(string $template, array $context, string $mode, string $filename)
    {
        if ($mode === 'pdf') {
            $pdf = Pdf::loadView($template, $context);
            $pdf->setPaper('a4', 'portrait');
            return $pdf->download("{$filename}.pdf");
        }

        // HTML mode — render a standalone print-friendly page
        return view($template, $context);
    }
}
