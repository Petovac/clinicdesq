<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryOrder extends Model
{
    protected $fillable = [
        'clinic_id',
        'organisation_id',
        'order_number',
        'order_type',
        'vendor_name',
        'status',
        'notes',
        'created_by',
        'approved_by',
    ];

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function items()
    {
        return $this->hasMany(InventoryOrderItem::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Generate a unique order number like ORD-20260313-001
     */
    public static function generateOrderNumber(): string
    {
        $date = now()->format('Ymd');
        $last = static::where('order_number', 'like', "ORD-{$date}-%")->count();
        return sprintf('ORD-%s-%03d', $date, $last + 1);
    }
}
