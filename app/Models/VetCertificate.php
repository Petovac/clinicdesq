<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class VetCertificate extends Model
{
    protected $guarded = [];

    protected $casts = [
        'content' => 'array',
        'issued_date' => 'date',
        'valid_until' => 'date',
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function vet()
    {
        return $this->belongsTo(Vet::class);
    }

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function template()
    {
        return $this->belongsTo(CertificateTemplate::class, 'certificate_template_id');
    }

    public function isIssued(): bool
    {
        return $this->status === 'issued';
    }

    /**
     * Generate unique certificate number: CERT-YYYYMMDD-NNN
     */
    public static function generateNumber(): string
    {
        $prefix = 'CERT-' . now()->format('Ymd') . '-';
        $last = static::where('certificate_number', 'like', $prefix . '%')
            ->orderByDesc('certificate_number')
            ->value('certificate_number');

        if ($last) {
            $seq = (int) substr($last, -3) + 1;
        } else {
            $seq = 1;
        }

        return $prefix . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }
}
