<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\CaseSheet;
use App\Models\Prescription;
use App\Services\DocumentService;

class DocumentController extends Controller
{
    protected DocumentService $docs;

    public function __construct(DocumentService $docs)
    {
        $this->docs = $docs;
    }

    /* ── Prescription ── */

    public function prescriptionPrint(Prescription $prescription)
    {
        return $this->docs->renderPrescription($prescription, 'html');
    }

    public function prescriptionPdf(Prescription $prescription)
    {
        return $this->docs->renderPrescription($prescription, 'pdf');
    }

    /* ── Case Sheet ── */

    public function caseSheetPrint(CaseSheet $caseSheet)
    {
        return $this->docs->renderCaseSheet($caseSheet, 'html');
    }

    public function caseSheetPdf(CaseSheet $caseSheet)
    {
        return $this->docs->renderCaseSheet($caseSheet, 'pdf');
    }

    /* ── Bill ── */

    public function billPrint(Bill $bill)
    {
        return $this->docs->renderBill($bill, 'html');
    }

    public function billPdf(Bill $bill)
    {
        return $this->docs->renderBill($bill, 'pdf');
    }
}
