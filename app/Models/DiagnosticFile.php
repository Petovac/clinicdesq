<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiagnosticFile extends Model
{
    protected $table = 'diagnostic_files';

    protected $fillable = [
        'diagnostic_report_id',
        'original_filename',
        'display_name',
        'storage_path',
        'mime_type',
        'file_size',
        'extracted_text',
        'ai_summary',
        'status',
    ];

    public function diagnosticReport()
    {
        return $this->belongsTo(\App\Models\DiagnosticReport::class, 'diagnostic_report_id');
    }

    public static function hasVerifiedFindings(int $appointmentId): bool
    {
        return self::whereHas('diagnosticReport', function ($q) use ($appointmentId) {
                $q->where('appointment_id', $appointmentId);
            })
            ->where('status', 'human_verified')
            ->whereNotNull('ai_summary')
            ->exists();
    }

    public static function verifiedSummariesForAppointment(int $appointmentId): string
    {
        $files = self::whereHas('diagnosticReport', function ($q) use ($appointmentId) {
                $q->where('appointment_id', $appointmentId);
            })
            ->where('status', 'human_verified')
            ->whereNotNull('ai_summary')
            ->get();

        if ($files->isEmpty()) {
            return '';
        }

        return $files->map(function ($file) {
            $name = $file->display_name ?? $file->original_filename;

            return "Report: {$name}\n{$file->ai_summary}";
        })->implode("\n\n");
    }
}