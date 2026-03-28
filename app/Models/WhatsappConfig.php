<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class WhatsappConfig extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'send_case_sheet' => 'boolean',
        'send_prescription' => 'boolean',
        'send_bill' => 'boolean',
        'send_lab_report' => 'boolean',
    ];

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    // Encrypt API key on set
    public function setApiKeyAttribute($value)
    {
        $this->attributes['api_key'] = $value ? Crypt::encryptString($value) : null;
    }

    // Decrypt API key on get
    public function getApiKeyAttribute($value)
    {
        try {
            return $value ? Crypt::decryptString($value) : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function isConfigured(): bool
    {
        return $this->is_active && $this->api_key && $this->integrated_number_id;
    }
}
