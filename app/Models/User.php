<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Clinic;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',             
        'password',
        'role',
        'organisation_id',
        'clinic_id',
        'is_active',
    ];
    
    

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function assignedClinics()
    {
        return $this->belongsToMany(
            Clinic::class,
            'clinic_user_assignments'
        );
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }


    /*
    |--------------------------------------------------------------------------
    | Role helpers
    |--------------------------------------------------------------------------
    */

    public function isVet(): bool
    {
        return $this->role === 'vet';
    }

    public function isClinicStaff(): bool
    {
        return in_array($this->role, [
            'clinic_manager',
            'receptionist',
            'sales',
        ]);
    }

    public function isOrgLevel(): bool
    {
        return in_array($this->role, [
            'organisation_owner',
            'regional_manager',
            'area_manager',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Data visibility rules
    |--------------------------------------------------------------------------
    */

    public function canViewPetPII(): bool
    {
        // Vets can NEVER see pet parent personal info
        return !$this->isVet();
    }

    public function hasPermission($permissionSlug)
    {
        // Org owner/admin bypass
        if (in_array($this->role, ['organisation_owner', 'organisation_admin'])) {
            return true;
        }

        $roles = \App\Models\OrganisationUserRole::where('user_id', $this->id)->pluck('role_id');

        if ($roles->isEmpty()) {
            return false;
        }

        return \DB::table('role_permissions')
            ->join('permissions', 'permissions.id', '=', 'role_permissions.permission_id')
            ->whereIn('role_permissions.role_id', $roles)
            ->where('permissions.slug', $permissionSlug)
            ->exists();
    }
    
}
