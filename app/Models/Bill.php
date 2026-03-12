<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $fillable = [
        'appointment_id',
        'clinic_id',
        'created_by',
        'total_amount',
        'payment_status',
        'status',
        'notes',
    ];

    public function items()
    {
        return $this->hasMany(BillItem::class);
    }

    public function approvedItems()
    {
        return $this->hasMany(BillItem::class)->where('status', 'approved');
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function recalculateTotal(): void
    {
        $total = $this->items()->where('status', 'approved')->sum('total');
        $this->update(['total_amount' => $total]);
    }

    public function isDraft(): bool     { return $this->status === 'draft'; }
    public function isConfirmed(): bool { return $this->status === 'confirmed'; }
}
