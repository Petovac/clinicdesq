<?php

namespace App\Http\Controllers\Organisation;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Models\JobApplication;
use App\Models\JobPosting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobController extends Controller
{
    public function index()
    {
        $orgId = Auth::user()->organisation_id;
        $jobs = JobPosting::where('organisation_id', $orgId)
            ->withCount('applications')
            ->latest()
            ->get();

        return view('organisation.jobs.index', compact('jobs'));
    }

    public function create()
    {
        $clinics = Clinic::where('organisation_id', Auth::user()->organisation_id)->get();
        return view('organisation.jobs.create', compact('clinics'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'role_type' => 'required|in:vet,vet_surgeon,vet_specialist,vet_intern',
            'employment_type' => 'required|in:full_time,part_time,locum,contract',
            'description' => 'nullable|string',
            'requirements' => 'nullable|string',
            'city' => 'nullable|string',
            'state' => 'nullable|string',
        ]);

        $job = JobPosting::create([
            'organisation_id' => Auth::user()->organisation_id,
            'clinic_id' => $request->clinic_id ?: null,
            'title' => $request->title,
            'role_type' => $request->role_type,
            'employment_type' => $request->employment_type,
            'description' => $request->description,
            'requirements' => $request->requirements,
            'specialization_required' => $request->specialization_required,
            'min_experience_years' => $request->min_experience_years,
            'salary_min' => $request->salary_min,
            'salary_max' => $request->salary_max,
            'city' => $request->city,
            'state' => $request->state,
            'status' => $request->publish ? 'active' : 'draft',
            'published_at' => $request->publish ? now() : null,
            'closes_at' => $request->closes_at,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('organisation.jobs.index')
            ->with('success', 'Job posting created' . ($request->publish ? ' and published.' : '.'));
    }

    public function show(JobPosting $job)
    {
        abort_if($job->organisation_id !== Auth::user()->organisation_id, 403);

        $applications = $job->applications()
            ->with('vet')
            ->latest()
            ->get();

        return view('organisation.jobs.show', compact('job', 'applications'));
    }

    public function toggleStatus(Request $request, JobPosting $job)
    {
        abort_if($job->organisation_id !== Auth::user()->organisation_id, 403);

        $newStatus = $request->status;
        $job->update([
            'status' => $newStatus,
            'published_at' => $newStatus === 'active' ? ($job->published_at ?? now()) : $job->published_at,
        ]);

        return redirect()->back()->with('success', 'Job status updated to ' . $newStatus . '.');
    }

    /**
     * View applicant profile with full analytics (only after they applied)
     */
    public function viewApplicant(JobPosting $job, JobApplication $application)
    {
        abort_if($job->organisation_id !== Auth::user()->organisation_id, 403);
        abort_if($application->job_posting_id !== $job->id, 404);

        $application->load('vet');
        $analytics = $application->getVetAnalytics();

        return view('organisation.jobs.applicant', compact('job', 'application', 'analytics'));
    }

    /**
     * Update applicant status (shortlist, reject, etc)
     */
    public function updateApplicant(Request $request, JobPosting $job, JobApplication $application)
    {
        abort_if($job->organisation_id !== Auth::user()->organisation_id, 403);

        $application->update([
            'status' => $request->status,
            'org_notes' => $request->org_notes ?? $application->org_notes,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Applicant status updated.');
    }
}
