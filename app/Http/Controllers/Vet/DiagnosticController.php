<?php

namespace App\Http\Controllers\Vet;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Services\AiClinicalService;
use Illuminate\Support\Facades\Storage;
use App\Models\DiagnosticReport;
use App\Models\DiagnosticFile;
use Illuminate\Support\Facades\DB;

class DiagnosticController extends Controller
{
    /* =========================
     | CREATE
     ========================= */
     public function create(Appointment $appointment, Request $request)
     {
         $type = $request->query('type');
     
         $report = DiagnosticReport::with('files')
             ->where('appointment_id', $appointment->id)
             ->where('type', $type)
             ->first();
     
         return view('vet.diagnostics.create', [
             'appointment' => $appointment,
             'type'        => $type,
             'report'      => $report, // 🔑 existing report (if any)
         ]);
     }

    /* =========================
     | AI EXTRACT
     ========================= */
    public function extract(Appointment $appointment, Request $request)
    {
        $request->validate([
            'report_files' => 'required|array',
            'report_files.*' => 'file|mimes:pdf|max:10240',
        ]);

        $file = $request->file('report_files')[0];

        $path = $file->store('temp_diagnostics', 'local');
        $fullPath = Storage::disk('local')->path($path);

        $summary = app(AiClinicalService::class)
            ->extractClinicalTextFromFile($fullPath);

        return response()->json([
            'files' => [
                [
                    'name' => $file->getClientOriginalName(),
                    'summary' => $summary,
                ]
            ]
        ]);
    }

    /* =========================
     | STORE
     ========================= */
     public function store(Request $request, Appointment $appointment)
     {
         $clinicId = session('active_clinic_id');
         $vetId    = auth('vet')->id();
     
         abort_if(!$clinicId, 403);
         abort_if($appointment->clinic_id !== $clinicId, 403);
         abort_if($appointment->vet_id !== $vetId, 403);
     
         $request->validate([
            'type' => 'required|in:lab,radiology',
            'reports' => 'required|array',
            'reports.*.file' => 'required|file|mimes:pdf|max:10240',
            'reports.*.ai_summary' => 'nullable|string',
            'reports.*.display_name' => 'nullable|string',
            'title' => 'nullable|string',
        ]);
     
         DB::beginTransaction();
     
         try {
             /**
              * 🔑 SINGLE diagnostic report per appointment + type
              */
              $diagnosticReport = DiagnosticReport::firstOrCreate(
                [
                    'appointment_id' => $appointment->id,
                    'type'           => $request->type,
                ],
                [
                    'pet_id'      => $appointment->pet_id,
                    'clinic_id'   => $appointment->clinic_id,
                    'vet_id'      => $vetId,
                    'title'       => $request->input('title'),
                    'report_date' => now(),
                ]
            );
     
             /**
              * 📎 Attach files (ONLY create DiagnosticFile rows)
              */
             foreach ($request->reports as $report) {
                 $file = $report['file'];
     
                 $path = $file->store(
                     "diagnostics/{$appointment->id}",
                     'private'
                 );
     
                 DiagnosticFile::create([
                    'diagnostic_report_id' => $diagnosticReport->id,
                
                    // 🔑 human-entered name (NOT pdf filename)
                    'display_name' => $report['display_name'] ?? null,
                
                    // keep pdf filename ONLY for storage/debug
                    'original_filename' => $file->getClientOriginalName(),
                
                    'storage_path' => $path,
                    'mime_type'    => $file->getClientMimeType(),
                    'file_size'    => $file->getSize(),
                
                    // 🧠 AI-only, file-specific findings
                    'extracted_text' => $report['ai_summary'] ?? null,
                    'ai_summary'     => $report['ai_summary'] ?? null,
                
                    'status' => 'extracted',
                ]);
             }
     
             DB::commit();
     
             return response()->json([
                 'success' => true,
                 'message' => 'Diagnostic report saved successfully',
                 'report_id' => $diagnosticReport->id,
             ]);
         } catch (\Throwable $e) {
             DB::rollBack();
     
             return response()->json([
                 'success' => false,
                 'message' => 'Failed to save diagnostic report',
                 'error'   => $e->getMessage(),
             ], 500);
         }
     }

    /* =========================
     | DOWNLOAD
     ========================= */
    public function download(DiagnosticFile $file)
    {
        $clinicId = session('active_clinic_id');
        $report = $file->diagnosticReport;

        abort_if(!$clinicId, 403);
        abort_if($report->appointment->clinic_id !== $clinicId, 403);

        return Storage::disk('private')->download(
            $file->storage_path,
            $file->original_filename
        );
    }

    /* =========================
     | EDIT
     ========================= */
    public function edit(DiagnosticReport $report)
    {
        $clinicId = session('active_clinic_id');
        $vetId    = auth('vet')->id();

        abort_if(!$clinicId, 403);
        abort_if($report->appointment->status === 'completed', 403);
        abort_if($report->appointment->clinic_id !== $clinicId, 403);
        abort_if($report->appointment->vet_id !== $vetId, 403);

        $report->load('files', 'appointment.pet');

        return view('vet.diagnostics.edit', [
            'report' => $report,
            'appointment' => $report->appointment,
        ]);
    }

    public function view(DiagnosticFile $file)
{
    $clinicId = session('active_clinic_id');
    $report = $file->diagnosticReport;

    abort_if(!$clinicId, 403);
    abort_if($report->appointment->clinic_id !== $clinicId, 403);

    return Storage::disk('private')->response(
        $file->storage_path,
        $file->original_filename,
        [
            'Content-Type' => $file->mime_type,
            'Content-Disposition' => 'inline',
            'X-Frame-Options' => 'SAMEORIGIN',
            'Content-Security-Policy' => "frame-ancestors 'self'",
        ]
    );
}

    /* =========================
     | UPDATE
     ========================= */
     public function update(Request $request, DiagnosticReport $report)
     {
         $clinicId = session('active_clinic_id');
         $vetId    = auth('vet')->id();
     
         abort_if(!$clinicId, 403);
         abort_if($report->appointment->status === 'completed', 403);
         abort_if($report->appointment->clinic_id !== $clinicId, 403);
         abort_if($report->appointment->vet_id !== $vetId, 403);
     
         DB::beginTransaction();
     
         try {
             // ----------------------------------
             // 1️⃣ Update HUMAN-edited fields
             // ----------------------------------
             $report->update([
                 'title'         => $request->input('title'),
             ]);

     
             // ----------------------------------
             // 2️⃣ Add new files if uploaded
             // ----------------------------------
             if ($request->hasFile('new_files')) {
                 foreach ($request->file('new_files') as $file) {
                     $path = $file->store(
                         "diagnostics/{$report->appointment_id}",
                         'private'
                     );
     
                     DiagnosticFile::create([
                         'diagnostic_report_id' => $report->id,
                         'original_filename'    => $file->getClientOriginalName(),
                         'storage_path'         => $path,
                         'mime_type'            => $file->getClientMimeType(),
                         'file_size'            => $file->getSize(),
                         'status'               => 'uploaded',
                     ]);
                 }
             }
     
             DB::commit();
     
             return redirect()
                 ->route('vet.appointments.case', $report->appointment_id)
                 ->with('success', 'Diagnostic report updated and AI summary refreshed.');
         } catch (\Throwable $e) {
             DB::rollBack();
             throw $e;
         }
     }

     
     public function updateFileSummary(Request $request, DiagnosticFile $file)
    {
        $request->validate([
            'ai_summary' => 'required|string',
        ]);

        $file->update([
            'ai_summary' => trim($request->ai_summary),
            'status'     => 'human_verified',
        ]);

        return back()->with('success', 'Findings saved and verified.');
    }

    /* =========================
     | DELETE SINGLE FILE
     ========================= */
    public function destroyFile(DiagnosticFile $file)
    {
        $clinicId = session('active_clinic_id');
        $vetId    = auth('vet')->id();
        $report   = $file->diagnosticReport;

        abort_if(!$clinicId, 403);
        abort_if($report->appointment->status === 'completed', 403);
        abort_if($report->appointment->clinic_id !== $clinicId, 403);
        abort_if($report->appointment->vet_id !== $vetId, 403);

        Storage::disk('private')->delete($file->storage_path);
        $file->delete();

        if ($report->files()->count() === 0) {
            $report->update(['summary' => null]);
        }

        return back()->with('success', 'File removed.');
    }


    public function verifyFile(DiagnosticFile $file)
    {
        $clinicId = session('active_clinic_id');
        $vetId    = auth('vet')->id();
        $report   = $file->diagnosticReport;

        abort_if(!$clinicId, 403);
        abort_if($report->appointment->clinic_id !== $clinicId, 403);
        abort_if($report->appointment->vet_id !== $vetId, 403);

        $file->update([
            'status' => 'human_verified',
        ]);

        return back()->with('success', 'Diagnostic findings verified.');
    }

    /* =========================
     | DELETE REPORT
     ========================= */
    public function destroy(DiagnosticReport $report)
    {
        $clinicId = session('active_clinic_id');
        $vetId    = auth('vet')->id();

        abort_if(!$clinicId, 403);
        abort_if($report->appointment->status === 'completed', 403);
        abort_if($report->appointment->clinic_id !== $clinicId, 403);
        abort_if($report->appointment->vet_id !== $vetId, 403);

        DB::transaction(function () use ($report) {
            foreach ($report->files as $file) {
                Storage::disk('private')->delete($file->storage_path);
                $file->delete();
            }
            $report->delete();
        });

        return redirect()
            ->route('vet.appointments.case', $report->appointment_id)
            ->with('success', 'Diagnostic report deleted.');
    }

    public function embed(\App\Models\DiagnosticFile $file)
    {
        return response()->view('vet.diagnostics.embed', [
            'file' => $file
        ]);
    }
}