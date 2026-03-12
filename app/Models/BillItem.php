<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillItem extends Model
{
    protected $fillable = [
        'bill_id',
        'price_list_item_id',
        'quantity',
        'price',
        'total'
    ];

    public function priceItem()
    {
        return $this->belongsTo(PriceListItem::class,'price_list_item_id');
    }
}