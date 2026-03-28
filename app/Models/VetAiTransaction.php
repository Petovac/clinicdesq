<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VetAiTransaction extends Model
{
    protected $fillable = [
        'vet_id', 'type', 'credits', 'balance_after',
        'description', 'reference', 'appointment_id', 'ai_feature',
        'input_tokens', 'output_tokens', 'cost_usd',
    ];

    public function vet()
    {
        return $this->belongsTo(Vet::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public static $packs = [
        'starter' => [
            'name' => 'Starter Pack',
            'credits' => 50,
            'price' => 249,
            'per_credit' => '4.98',
        ],
        'standard' => [
            'name' => 'Standard Pack',
            'credits' => 200,
            'price' => 799,
            'per_credit' => '4.00',
        ],
        'pro' => [
            'name' => 'Pro Pack',
            'credits' => 500,
            'price' => 1499,
            'per_credit' => '3.00',
        ],
    ];
}
