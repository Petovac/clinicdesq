<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InjectionRouteFee extends Model
{
    protected $fillable = [
        'organisation_id',
        'route_code',
        'route_name',
        'administration_fee',
        'is_active',
    ];

    protected $casts = [
        'administration_fee' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    /**
     * Get the fee for a given route code in an organisation.
     */
    public static function feeFor(int $organisationId, ?string $routeCode): float
    {
        if (!$routeCode) {
            return 0;
        }

        $record = static::where('organisation_id', $organisationId)
            ->where('route_code', strtoupper(trim($routeCode)))
            ->where('is_active', true)
            ->first();

        return $record ? (float) $record->administration_fee : 0;
    }
}
