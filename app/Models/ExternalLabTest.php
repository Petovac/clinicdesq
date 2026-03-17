<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExternalLabTest extends Model
{
    protected $fillable = [
        'external_lab_id', 'test_name', 'test_code', 'category', 'sample_type',
        'parameters', 'estimated_time', 'b2b_price', 'org_selling_price',
        'organisation_id', 'is_active',
    ];

    protected $casts = [
        'parameters' => 'array',
        'b2b_price' => 'decimal:2',
        'org_selling_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function lab()
    {
        return $this->belongsTo(ExternalLab::class, 'external_lab_id');
    }

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }
}
