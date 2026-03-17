<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IpdNote extends Model
{
    protected $fillable = [
        'ipd_admission_id', 'noted_by_type', 'noted_by_id',
        'note_type', 'content',
    ];

    public function admission()
    {
        return $this->belongsTo(IpdAdmission::class, 'ipd_admission_id');
    }

    public function getNotedByNameAttribute(): string
    {
        if ($this->noted_by_type === 'vet') {
            return Vet::find($this->noted_by_id)?->name ?? '—';
        }
        return User::find($this->noted_by_id)?->name ?? '—';
    }
}
