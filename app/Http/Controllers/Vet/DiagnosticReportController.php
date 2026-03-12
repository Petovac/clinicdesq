<?php

namespace App\Http\Controllers\Vet;

use App\Http\Controllers\Controller;
use App\Models\DiagnosticReport;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DiagnosticReportController extends Controller
{
    public function store(Request $request, Appointment $appointment)
    {
        $request->validate([
            'reports' => 'required|array',
            'reports.*.file' => 'required|file|mimes:pdf',
            'reports.*.summary' => 'nullable|string',
            'type' => 'required|in:lab,radiology',
        ]);

        foreach ($request->reports as $report) {

            $path = $report['file']->store(
                'diagnostic_reports/' . $appointment->id,
                'public'
            );

            DiagnosticFile::create([
                'diagnostic_report_id' => $diagnosticReport->id,
                'original_filename'    => $file->getClientOriginalName(),
                'storage_path'         => $path,
                'mime_type'            => $file->getMimeType(),
                'file_size'            => $file->getSize(),
                'extracted_text'       => $report['summary'] ?? null,
                'ai_summary'           => $report['summary'] ?? null,
                'status'               => 'extracted',
            ]);
        }

        return response()->json([
            'success' => true
        ]);
    }
}