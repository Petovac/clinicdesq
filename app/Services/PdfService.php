<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Bill;
use App\Models\CaseSheet;
use App\Models\Prescription;
use App\Models\DiagnosticReport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class PdfService
{
    /**
     * Generate case sheet PDF and return stored path
     */
    public static function generateCaseSheet(Appointment $appointment): string
    {
        $appointment->load(['pet.petParent', 'caseSheet', 'treatments', 'clinic.organisation', 'vet']);

        $data = [
            'appointment' => $appointment,
            'caseSheet' => $appointment->caseSheet,
            'pet' => $appointment->pet,
            'parent' => $appointment->pet->petParent,
            'clinic' => $appointment->clinic,
            'org' => $appointment->clinic->organisation,
            'vet' => $appointment->vet,
            'treatments' => $appointment->treatments ?? collect(),
        ];

        $pdf = Pdf::loadView('pdf.case-sheet', $data)
            ->setPaper('a4')
            ->setOption('isRemoteEnabled', true);

        $filename = 'case-sheets/CS-' . $appointment->id . '-' . now()->format('YmdHis') . '.pdf';
        Storage::disk('public')->put($filename, $pdf->output());

        return $filename;
    }

    /**
     * Generate prescription PDF
     */
    public static function generatePrescription(Appointment $appointment): string
    {
        $appointment->load(['pet.petParent', 'prescription.items', 'clinic.organisation', 'vet']);

        $data = [
            'appointment' => $appointment,
            'prescription' => $appointment->prescription,
            'items' => $appointment->prescription->items ?? collect(),
            'pet' => $appointment->pet,
            'parent' => $appointment->pet->petParent,
            'clinic' => $appointment->clinic,
            'org' => $appointment->clinic->organisation,
            'vet' => $appointment->vet,
        ];

        $pdf = Pdf::loadView('pdf.prescription', $data)
            ->setPaper('a4')
            ->setOption('isRemoteEnabled', true);

        $filename = 'prescriptions/RX-' . $appointment->id . '-' . now()->format('YmdHis') . '.pdf';
        Storage::disk('public')->put($filename, $pdf->output());

        return $filename;
    }

    /**
     * Generate bill/invoice PDF
     */
    public static function generateBill(Bill $bill): string
    {
        $bill->load(['items.priceItem', 'appointment.pet.petParent', 'appointment.vet', 'clinic.organisation']);

        $data = [
            'bill' => $bill,
            'items' => $bill->items,
            'appointment' => $bill->appointment,
            'pet' => $bill->appointment->pet,
            'parent' => $bill->appointment->pet->petParent,
            'clinic' => $bill->clinic,
            'org' => $bill->clinic->organisation,
        ];

        $pdf = Pdf::loadView('pdf.bill', $data)
            ->setPaper('a4')
            ->setOption('isRemoteEnabled', true);

        $filename = 'bills/BILL-' . $bill->id . '-' . now()->format('YmdHis') . '.pdf';
        Storage::disk('public')->put($filename, $pdf->output());

        return $filename;
    }

    /**
     * Generate a combined lab report PDF (or return existing uploaded file path)
     */
    public static function getLabReportPath(DiagnosticReport $report): ?string
    {
        $report->load('files');

        // If there's an uploaded PDF file, use it directly
        $pdfFile = $report->files->first(function ($file) {
            return str_ends_with(strtolower($file->storage_path), '.pdf');
        });

        if ($pdfFile) {
            return $pdfFile->storage_path;
        }

        // Otherwise generate a basic lab report PDF
        $report->load(['appointment.pet.petParent', 'appointment.clinic.organisation']);

        $data = [
            'report' => $report,
            'files' => $report->files,
            'pet' => $report->appointment->pet ?? null,
            'parent' => $report->appointment->pet->petParent ?? null,
            'clinic' => $report->appointment->clinic ?? null,
            'org' => $report->appointment->clinic->organisation ?? null,
        ];

        $pdf = Pdf::loadView('pdf.lab-report', $data)
            ->setPaper('a4')
            ->setOption('isRemoteEnabled', true);

        $filename = 'lab-reports/LAB-' . $report->id . '-' . now()->format('YmdHis') . '.pdf';
        Storage::disk('public')->put($filename, $pdf->output());

        return $filename;
    }
}
