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
            'price' => 199,
            'per_credit' => '3.98',
        ],
        'standard' => [
            'name' => 'Standard Pack',
            'credits' => 150,
            'price' => 449,
            'per_credit' => '2.99',
        ],
        'pro' => [
            'name' => 'Pro Pack',
            'credits' => 600,
            'price' => 1499,
            'per_credit' => '2.50',
        ],
    ];
}
