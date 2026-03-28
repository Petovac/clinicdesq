<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappMessage extends Model
{
    protected $guarded = [];

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function sentBy()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function reference()
    {
        return $this->morphTo();
    }
}
