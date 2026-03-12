<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceList extends Model
{
    protected $fillable = [
        'organisation_id',
        'name',
        'is_active',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function items()
    {
        return $this->hasMany(PriceListItem::class);
    }

    public function organisation()
    {
        return $this->belongsTo(\App\Models\Organisation::class);
    }
}