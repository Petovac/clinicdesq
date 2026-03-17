<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = [
        'slug', 'name', 'price_per_doctor', 'original_price',
        'trial_days', 'features', 'max_clinics', 'max_doctors',
        'is_active', 'sort_order',
    ];

    protected $casts = [
        'features' => 'array',
        'is_active' => 'boolean',
    ];

    public function organisations()
    {
        return $this->hasMany(Organisation::class);
    }

    public function hasFeature(string $slug): bool
    {
        return in_array($slug, $this->features ?? []);
    }
}
