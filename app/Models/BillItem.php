<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillItem extends Model
{
    protected $fillable = [
        'bill_id',
        'price_list_item_id',
        'prescription_item_id',
        'quantity',
        'price',
        'total',
        'source',
        'status',
        'description',
    ];

    public function priceItem()
    {
        return $this->belongsTo(PriceListItem::class, 'price_list_item_id');
    }

    public function prescriptionItem()
    {
        return $this->belongsTo(PrescriptionItem::class);
    }

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }
}
