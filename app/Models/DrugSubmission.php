<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DrugSubmission extends Model
{
    protected $guarded = [];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'strength_value' => 'decimal:2',
        'pack_size' => 'decimal:2',
    ];

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function generic()
    {
        return $this->belongsTo(DrugGeneric::class, 'drug_generic_id');
    }

    public function createdGeneric()
    {
        return $this->belongsTo(DrugGeneric::class, 'created_generic_id');
    }

    public function createdBrand()
    {
        return $this->belongsTo(DrugBrand::class, 'created_brand_id');
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }
}
