<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{

protected $table = 'inventory_items';

protected $fillable = [

'organisation_id',

'item_type',

'generic_name',

'brand_id',

'drug_brand_id',

'name',

'unit',

'package_type',

'unit_volume_ml',

'pack_unit',

'strength_value',

'strength_unit',

'track_inventory',

'is_multi_use'

];



public function drugBrand()
{
return $this->belongsTo(DrugBrand::class);
}

public function batches()
{
return $this->hasMany(InventoryBatch::class)
            ->whereNull('clinic_id');
}

public function organisation()
{
return $this->belongsTo(Organisation::class);
}

}