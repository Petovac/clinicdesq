<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DrugGeneric extends Model
{
    protected $table = 'drug_generics';

    protected $fillable = [
        'name',
        'drug_class',
        'default_dose_unit',
        'created_by'
    ];

    public function dosages()
    {
        return $this->hasMany(DrugDosage::class, 'generic_id');
    }

    public function brands()
    {
        return $this->hasMany(DrugBrand::class, 'generic_id');
    }
}