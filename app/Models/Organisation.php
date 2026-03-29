<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Organisation extends Model
{
    use HasFactory;

    protected $table = 'organisations';

    protected $fillable = [
        'name',
        'type',
        'primary_email',
        'primary_phone',
        'is_active',
        'logo_path',
        'template_prescription',
        'template_casesheet',
        'template_bill',
        'gst_number',
        'package_id',
        'trial_ends_at',
        'vet_can_select_lab',
        'modules',
    ];

    protected $casts = [
        'vet_can_select_lab' => 'boolean',
        'is_active' => 'boolean',
        'trial_ends_at' => 'datetime',
        'modules' => 'array',
    ];

    /**
     * Default modules when not configured.
     */
    protected static array $defaultModules = [
        'inventory' => true,
        'billing'   => true,
        'lab'       => true,
    ];

    /**
     * Check if an org module is enabled.
     */
    public function hasModule(string $module): bool
    {
        $modules = $this->modules ?? self::$defaultModules;
        return !empty($modules[$module]);
    }

    /**
     * Enable a module.
     */
    public function enableModule(string $module): void
    {
        $modules = $this->modules ?? self::$defaultModules;
        $modules[$module] = true;
        $this->update(['modules' => $modules]);
    }

    /**
     * Disable a module.
     */
    public function disableModule(string $module): void
    {
        $modules = $this->modules ?? self::$defaultModules;
        $modules[$module] = false;
        $this->update(['modules' => $modules]);
    }

    public function clinics()
    {
        return $this->hasMany(Clinic::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function hasFeature(string $slug): bool
    {
        if ($this->trial_ends_at && now()->lt($this->trial_ends_at)) {
            return true; // Active trial = all features enabled
        }
        if (!$this->package_id) {
            return false; // No trial, no package = no features
        }
        return $this->package && $this->package->hasFeature($slug);
    }

    public function isOnTrial(): bool
    {
        return $this->trial_ends_at && now()->lt($this->trial_ends_at);
    }

    public function trialDaysRemaining(): int
    {
        if (!$this->trial_ends_at || now()->gte($this->trial_ends_at)) {
            return 0;
        }
        return (int) now()->diffInDays($this->trial_ends_at);
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo_path ? asset('storage/' . $this->logo_path) : null;
    }

    public function externalLabs()
    {
        return $this->belongsToMany(ExternalLab::class, 'organisation_lab')
            ->withPivot('is_active', 'status', 'responded_at')
            ->withTimestamps();
    }

    public function labTestCatalog()
    {
        return $this->hasMany(LabTestCatalog::class);
    }

    public function labUsers()
    {
        return $this->hasMany(LabUser::class);
    }

    public function whatsappConfig()
    {
        return $this->hasOne(WhatsappConfig::class);
    }
}
