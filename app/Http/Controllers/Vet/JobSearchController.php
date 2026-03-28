<?php

namespace App\Http\Controllers\Vet;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\JobPosting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobSearchController extends Controller
{
    /**
     * Browse active job postings
     */
    public function index(Request $request)
    {
        $query = JobPosting::where('status', 'active')
            ->with(['organisation', 'clinic'])
            ->withCount('applications');

        if ($request->city) {
            $query->where('city', $request->city);
        }
        if ($request->type) {
            $query->where('employment_type', $request->type);
        }
        if ($request->q) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->q . '%')
                  ->orWhereHas('organisation', fn($oq) => $oq->where('name', 'like', '%' . $request->q . '%'));
            });
        }
        if ($request->min_salary) {
            $query->where(function ($q) use ($request) {
                $q->where('salary_max', '>=', $request->min_salary)
                  ->orWhere('salary_min', '>=', $request->min_salary);
            });
        }
        if ($request->filled('max_exp')) {
            $query->where(function ($q) use ($request) {
                $q->whereNull('min_experience_years')
                  ->orWhere('min_experience_years', '<=', (int) $request->max_exp);
            });
        }

        $jobs = $query->latest('published_at')->paginate(20);

        // Get vet's applied job IDs
        $appliedIds = JobApplication::where('vet_id', Auth::guard('vet')->id())
            ->pluck('job_posting_id')
            ->toArray();

        return view('vet.jobs.index', compact('jobs', 'appliedIds'));
    }

    /**
     * View a single job posting
     */
    public function show(JobPosting $job)
    {
        abort_if(!$job->isActive(), 404);

        $job->load(['organisation', 'clinic']);
        $hasApplied = JobApplication::where('job_posting_id', $job->id)
            ->where('vet_id', Auth::guard('vet')->id())
            ->exists();
        $application = JobApplication::where('job_posting_id', $job->id)
            ->where('vet_id', Auth::guard('vet')->id())
            ->first();

        return view('vet.jobs.show', compact('job', 'hasApplied', 'application'));
    }

    /**
     * Apply to a job
     */
    public function apply(Request $request, JobPosting $job)
    {
        abort_if(!$job->isActive(), 404);

        $vetId = Auth::guard('vet')->id();

        // Check if already applied
        if (JobApplication::where('job_posting_id', $job->id)->where('vet_id', $vetId)->exists()) {
            return redirect()->back()->with('error', 'You have already applied to this position.');
        }

        JobApplication::create([
            'job_posting_id' => $job->id,
            'vet_id' => $vetId,
            'cover_note' => $request->cover_note,
            'status' => 'applied',
        ]);

        return redirect()->route('vet.jobs.show', $job)
            ->with('success', 'Application submitted successfully!');
    }

    /**
     * My applications
     */
    public function myApplications()
    {
        $applications = JobApplication::where('vet_id', Auth::guard('vet')->id())
            ->with(['jobPosting.organisation', 'jobPosting.clinic'])
            ->latest()
            ->get();

        return view('vet.jobs.my-applications', compact('applications'));
    }

    /**
     * Withdraw application
     */
    public function withdraw(JobApplication $application)
    {
        abort_if($application->vet_id !== Auth::guard('vet')->id(), 403);
        $application->update(['status' => 'withdrawn']);
        return redirect()->back()->with('success', 'Application withdrawn.');
    }
}
