<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DrugBrand extends Model
{
    protected $table = 'drug_brands';

    protected $fillable = [
        'generic_id',
        'brand_name',
        'strength_value',
        'strength_unit',
        'form',
        'pack_size',
        'pack_unit',
        'manufacturer'
    ];

    public function generic()
    {
        return $this->belongsTo(DrugGeneric::class, 'generic_id');
    }
}