<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabOrder extends Model
{
    protected $fillable = [
        'order_number', 'appointment_id', 'pet_id', 'clinic_id', 'vet_id',
        'lab_id', 'routing', 'status', 'priority', 'notes',
        'routed_by', 'routed_at', 'completed_at',
        'result_uploaded_by', 'result_uploaded_by_type',
    ];

    protected $casts = [
        'routed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function vet()
    {
        return $this->belongsTo(Vet::class);
    }

    public function lab()
    {
        return $this->belongsTo(ExternalLab::class, 'lab_id');
    }

    public function routedByUser()
    {
        return $this->belongsTo(User::class, 'routed_by');
    }

    public function tests()
    {
        return $this->hasMany(LabOrderTest::class);
    }

    public function results()
    {
        return $this->hasMany(LabResult::class);
    }

    /**
     * Generate a unique order number.
     */
    public static function generateOrderNumber(): string
    {
        $date = now()->format('Ymd');
        $last = static::where('order_number', 'like', "LO-{$date}-%")
            ->orderByDesc('order_number')
            ->value('order_number');

        $seq = 1;
        if ($last) {
            $seq = (int) substr($last, strrpos($last, '-') + 1) + 1;
        }

        return sprintf('LO-%s-%03d', $date, $seq);
    }

    public function isPending(): bool
    {
        return $this->status === 'ordered';
    }

    public function isAwaitingReview(): bool
    {
        return in_array($this->status, ['results_uploaded', 'vet_review']);
    }

    public function totalPrice(): float
    {
        return $this->tests->sum('price');
    }
}
