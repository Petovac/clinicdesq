<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prescription extends Model
{
    protected $fillable = ['appointment_id', 'notes'];

    public function items()
    {
        return $this->hasMany(PrescriptionItem::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}

