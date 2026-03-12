<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DrugDosage extends Model
{
    protected $table = 'drug_dosages';

    protected $casts = [
        'routes' => 'array',
        'frequencies' => 'array'
    ];

    protected $fillable = [
        'generic_id',
        'species',
        'dose_min',
        'dose_max',
        'dose_unit',
        'routes',
        'frequencies'
    ];

    public function generic()
    {
        return $this->belongsTo(DrugGeneric::class, 'generic_id');
    }
}