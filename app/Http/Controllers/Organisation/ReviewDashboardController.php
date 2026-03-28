<?php

namespace App\Http\Controllers\Organisation;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Models\ClinicReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $orgId = $user->organisation_id;
        $clinicIds = Clinic::where('organisation_id', $orgId)->pluck('id');
        $clinics = Clinic::where('organisation_id', $orgId)->get();

        $filterClinic = $request->clinic;
        $filterIds = $filterClinic ? collect([(int) $filterClinic]) : $clinicIds;

        // Stats
        $totalReviews = ClinicReview::whereIn('clinic_id', $filterIds)->where('status', 'submitted')->count();
        $avgRating = ClinicReview::whereIn('clinic_id', $filterIds)->where('status', 'submitted')->avg('overall_rating');
        $recommendPct = $totalReviews > 0
            ? round(ClinicReview::whereIn('clinic_id', $filterIds)->where('status', 'submitted')->where('would_recommend', true)->count() / $totalReviews * 100)
            : 0;
        $pendingReviews = ClinicReview::whereIn('clinic_id', $filterIds)->where('status', 'pending')->count();

        // Rating distribution
        $distribution = [];
        for ($r = 5; $r >= 1; $r--) {
            $count = ClinicReview::whereIn('clinic_id', $filterIds)->where('status', 'submitted')->where('overall_rating', $r)->count();
            $distribution[$r] = $count;
        }

        // Sub-rating averages
        $subRatings = [
            'Overall' => $avgRating,
            'Doctor' => ClinicReview::whereIn('clinic_id', $filterIds)->where('status', 'submitted')->avg('doctor_rating'),
            'Staff' => ClinicReview::whereIn('clinic_id', $filterIds)->where('status', 'submitted')->avg('staff_rating'),
            'Cleanliness' => ClinicReview::whereIn('clinic_id', $filterIds)->where('status', 'submitted')->avg('cleanliness_rating'),
            'Wait Time' => ClinicReview::whereIn('clinic_id', $filterIds)->where('status', 'submitted')->avg('wait_time_rating'),
        ];

        // Per-clinic ratings
        $clinicRatings = [];
        foreach ($clinics as $clinic) {
            $cAvg = ClinicReview::where('clinic_id', $clinic->id)->where('status', 'submitted')->avg('overall_rating');
            $cCount = ClinicReview::where('clinic_id', $clinic->id)->where('status', 'submitted')->count();
            $clinicRatings[] = [
                'clinic' => $clinic,
                'avg_rating' => $cAvg ? round($cAvg, 1) : null,
                'count' => $cCount,
            ];
        }
        usort($clinicRatings, fn($a, $b) => ($b['avg_rating'] ?? 0) <=> ($a['avg_rating'] ?? 0));

        // Per-vet ratings
        $vetRatings = DB::table('clinic_reviews as cr')
            ->join('vets as v', 'v.id', '=', 'cr.vet_id')
            ->whereIn('cr.clinic_id', $filterIds)
            ->where('cr.status', 'submitted')
            ->whereNotNull('cr.doctor_rating')
            ->select('v.id', 'v.name', DB::raw('AVG(cr.doctor_rating) as avg_rating'), DB::raw('COUNT(*) as review_count'))
            ->groupBy('v.id', 'v.name')
            ->orderByDesc('avg_rating')
            ->get();

        // Recent reviews
        $reviews = ClinicReview::whereIn('clinic_id', $filterIds)
            ->where('status', 'submitted')
            ->with(['clinic', 'petParent', 'vet', 'appointment.pet'])
            ->latest('submitted_at')
            ->paginate(15);

        // GMB conversion rate
        $gmbSent = ClinicReview::whereIn('clinic_id', $filterIds)->where('gmb_link_sent', true)->count();

        return view('organisation.reviews.index', compact(
            'clinics', 'filterClinic',
            'totalReviews', 'avgRating', 'recommendPct', 'pendingReviews',
            'distribution', 'subRatings', 'clinicRatings', 'vetRatings',
            'reviews', 'gmbSent'
        ));
    }

    /**
     * Flag a review
     */
    public function flag(ClinicReview $review)
    {
        $review->update(['status' => 'flagged']);
        return redirect()->back()->with('success', 'Review flagged.');
    }
}
