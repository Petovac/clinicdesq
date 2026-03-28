<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class JobApplication extends Model
{
    protected $guarded = [];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public function jobPosting()
    {
        return $this->belongsTo(JobPosting::class);
    }

    public function vet()
    {
        return $this->belongsTo(Vet::class);
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Vet analytics — only visible AFTER application
     */
    public function getVetAnalytics(): array
    {
        $vet = $this->vet;
        if (!$vet) return [];

        // Total cases seen across ALL clinics the vet has worked at
        $totalCases = DB::table('appointments')->where('vet_id', $vet->id)->count();
        $completedCases = DB::table('appointments')->where('vet_id', $vet->id)->where('status', 'completed')->count();

        // Clinics worked at (from clinic_vet pivot)
        $clinicsWorked = DB::table('clinic_vet')
            ->join('clinics', 'clinics.id', '=', 'clinic_vet.clinic_id')
            ->join('organisations', 'organisations.id', '=', 'clinics.organisation_id')
            ->where('clinic_vet.vet_id', $vet->id)
            ->select('clinics.name as clinic_name', 'organisations.name as org_name', 'clinics.city',
                'clinic_vet.created_at as joined_at', 'clinic_vet.offboarded_at', 'clinic_vet.is_active')
            ->get();

        // Repeat rate (pet parents who came back for 2+ appointments)
        $repeatRate = 0;
        if ($totalCases > 0) {
            $uniqueParents = DB::table('appointments')
                ->join('pets', 'pets.id', '=', 'appointments.pet_id')
                ->where('appointments.vet_id', $vet->id)
                ->distinct()
                ->count('pets.pet_parent_id');

            $repeatParents = DB::table('appointments')
                ->join('pets', 'pets.id', '=', 'appointments.pet_id')
                ->where('appointments.vet_id', $vet->id)
                ->groupBy('pets.pet_parent_id')
                ->havingRaw('COUNT(*) >= 2')
                ->count();

            $repeatRate = $uniqueParents > 0 ? round($repeatParents / $uniqueParents * 100) : 0;
        }

        // Avg rating from clinic_reviews
        $avgRating = DB::table('clinic_reviews')
            ->where('vet_id', $vet->id)
            ->where('status', 'submitted')
            ->avg('doctor_rating');

        $reviewCount = DB::table('clinic_reviews')
            ->where('vet_id', $vet->id)
            ->where('status', 'submitted')
            ->count();

        // Avg revenue per case (from bills)
        $avgRevenue = DB::table('bills')
            ->join('appointments', 'appointments.id', '=', 'bills.appointment_id')
            ->where('appointments.vet_id', $vet->id)
            ->where('bills.status', 'confirmed')
            ->avg('bills.total');

        // Experience tenure (earliest clinic_vet created_at to now)
        $earliest = DB::table('clinic_vet')->where('vet_id', $vet->id)->min('created_at');

        return [
            'total_cases' => $totalCases,
            'completed_cases' => $completedCases,
            'clinics_worked' => $clinicsWorked,
            'repeat_rate' => $repeatRate,
            'avg_rating' => $avgRating ? round($avgRating, 1) : null,
            'review_count' => $reviewCount,
            'avg_revenue_per_case' => $avgRevenue ? round($avgRevenue) : null,
            'platform_since' => $earliest ? \Carbon\Carbon::parse($earliest)->format('M Y') : null,
            'degree' => $vet->degree,
            'specialization' => $vet->specialization,
            'experience' => $vet->experience,
            'registration_number' => $vet->registration_number,
        ];
    }
}
