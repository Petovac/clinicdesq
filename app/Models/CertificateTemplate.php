<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CertificateTemplate extends Model
{
    protected $guarded = [];

    protected $casts = [
        'content_template' => 'array',
        'is_active' => 'boolean',
    ];

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    /**
     * Get template for a specific type — org-specific first, then system default
     */
    public static function forOrg(int $orgId, ?string $type = null)
    {
        $query = static::where(function ($q) use ($orgId) {
            $q->where('organisation_id', $orgId)
              ->orWhereNull('organisation_id');
        })->where('is_active', true);

        if ($type) {
            $query->where('type', $type);
        }

        return $query->orderByRaw('organisation_id IS NULL ASC')
            ->get()
            ->unique('type'); // org-specific takes precedence
    }

    /**
     * Get the fields array from the template
     */
    public function getFields(): array
    {
        return $this->content_template['fields'] ?? [];
    }
}
