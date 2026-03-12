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
        'payment_status'
    ];

    public function items()
    {
        return $this->hasMany(BillItem::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}