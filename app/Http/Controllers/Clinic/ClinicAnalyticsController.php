<?php

namespace App\Http\Controllers\Clinic;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use App\Models\Clinic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ClinicAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get clinics this user is assigned to
        $assignedClinicIds = DB::table('clinic_user_assignments')
            ->where('user_id', $user->id)
            ->pluck('clinic_id');

        // Fallback: if user has a direct clinic_id
        if ($assignedClinicIds->isEmpty() && $user->clinic_id) {
            $assignedClinicIds = collect([$user->clinic_id]);
        }

        // If still empty, use session active clinic
        if ($assignedClinicIds->isEmpty() && session('active_clinic_id')) {
            $assignedClinicIds = collect([session('active_clinic_id')]);
        }

        $clinics = Clinic::whereIn('id', $assignedClinicIds)->get();
        $days = (int) ($request->period ?? 30);
        if (!in_array($days, [7, 30, 90, 365])) $days = 30;

        // Filter to specific clinic if requested
        $filterClinicId = $request->clinic;
        if ($filterClinicId && $assignedClinicIds->contains($filterClinicId)) {
            $activeClinicIds = collect([(int) $filterClinicId]);
        } else {
            $activeClinicIds = $assignedClinicIds;
        }

        // Get org-level clinic IDs for benchmark comparison
        $orgId = $user->organisation_id;
        $orgClinicIds = $orgId ? Clinic::where('organisation_id', $orgId)->pluck('id') : $assignedClinicIds;

        // Analytics
        $kpis = AnalyticsService::getKPIs($activeClinicIds, $days);
        $orgKpis = ($orgClinicIds->count() > $activeClinicIds->count())
            ? AnalyticsService::getKPIs($orgClinicIds, $days)
            : null;

        $revenueTrend = AnalyticsService::getRevenueTrend($activeClinicIds, $days);
        $appointmentTrend = AnalyticsService::getAppointmentTrend($activeClinicIds, $days);
        $revenueBySource = AnalyticsService::getRevenueBySource($activeClinicIds, $days);
        $vetLeaderboard = AnalyticsService::getVetLeaderboard($activeClinicIds, $days);
        $speciesBreakdown = AnalyticsService::getSpeciesBreakdown($activeClinicIds);
        $topDiagnoses = AnalyticsService::getTopDiagnoses($activeClinicIds, $days, 8);
        $clientRetention = AnalyticsService::getClientRetention($activeClinicIds, $days);
        $inventoryAlerts = AnalyticsService::getInventoryAlerts($activeClinicIds);
        $insights = AnalyticsService::generateInsights($activeClinicIds, $days);

        // Clinic comparison if user has multiple clinics
        $clinicComparison = $assignedClinicIds->count() > 1
            ? AnalyticsService::getClinicComparison($assignedClinicIds, $days)
            : [];

        return view('clinic.analytics', compact(
            'clinics', 'days', 'filterClinicId',
            'kpis', 'orgKpis',
            'revenueTrend', 'appointmentTrend', 'revenueBySource',
            'vetLeaderboard', 'speciesBreakdown', 'topDiagnoses',
            'clientRetention', 'inventoryAlerts', 'insights', 'clinicComparison'
        ));
    }
}
