<?php

namespace App\Http\Controllers;

use App\Models\ClinicReview;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Public review form — accessed via token link sent to pet parent
     */
    public function show(string $token)
    {
        $review = ClinicReview::where('token', $token)
            ->with(['clinic.organisation', 'appointment.pet', 'vet'])
            ->firstOrFail();

        if ($review->isSubmitted()) {
            return view('reviews.thankyou', compact('review'));
        }

        return view('reviews.form', compact('review'));
    }

    /**
     * Submit review
     */
    public function submit(Request $request, string $token)
    {
        $review = ClinicReview::where('token', $token)->firstOrFail();

        if ($review->isSubmitted()) {
            return redirect()->back()->with('info', 'You have already submitted your review.');
        }

        $request->validate([
            'overall_rating' => 'required|integer|min:1|max:5',
            'staff_rating' => 'nullable|integer|min:1|max:5',
            'cleanliness_rating' => 'nullable|integer|min:1|max:5',
            'wait_time_rating' => 'nullable|integer|min:1|max:5',
            'doctor_rating' => 'nullable|integer|min:1|max:5',
            'feedback' => 'nullable|string|max:2000',
            'would_recommend' => 'nullable|boolean',
        ]);

        $review->update([
            'overall_rating' => $request->overall_rating,
            'staff_rating' => $request->staff_rating,
            'cleanliness_rating' => $request->cleanliness_rating,
            'wait_time_rating' => $request->wait_time_rating,
            'doctor_rating' => $request->doctor_rating,
            'feedback' => $request->feedback,
            'would_recommend' => $request->boolean('would_recommend'),
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        // If good rating (4-5) and clinic has GMB URL, show GMB prompt
        $showGmb = $review->overall_rating >= 4 && $review->clinic->gmb_review_url;

        if ($showGmb) {
            $review->update(['gmb_link_sent' => true]);
        }

        return view('reviews.thankyou', [
            'review' => $review,
            'showGmb' => $showGmb,
            'gmbUrl' => $review->clinic->gmb_review_url,
        ]);
    }
}
