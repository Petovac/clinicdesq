<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ClinicReview extends Model
{
    protected $guarded = [];

    protected $casts = [
        'would_recommend' => 'boolean',
        'gmb_link_sent' => 'boolean',
        'submitted_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($review) {
            if (!$review->token) {
                $review->token = Str::random(48);
            }
        });
    }

    public function clinic()
    {
        return $this->belongsTo(Clinic::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function petParent()
    {
        return $this->belongsTo(PetParent::class);
    }

    public function vet()
    {
        return $this->belongsTo(Vet::class);
    }

    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }

    public function getReviewUrl(): string
    {
        return url('/review/' . $this->token);
    }

    /**
     * Average of all sub-ratings
     */
    public function getAverageRatingAttribute(): ?float
    {
        $ratings = array_filter([
            $this->overall_rating,
            $this->staff_rating,
            $this->cleanliness_rating,
            $this->wait_time_rating,
            $this->doctor_rating,
        ]);

        return count($ratings) > 0 ? round(array_sum($ratings) / count($ratings), 1) : null;
    }
}
