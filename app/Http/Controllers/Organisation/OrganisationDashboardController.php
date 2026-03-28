<?php

namespace App\Http\Controllers\Organisation;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use App\Models\Organisation;
use App\Models\Clinic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OrganisationDashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $organisation = Organisation::findOrFail($user->organisation_id);
        $clinicIds = Clinic::where('organisation_id', $organisation->id)->pluck('id');

        $days = (int) ($request->period ?? 30);
        if (!in_array($days, [7, 30, 90, 365])) $days = 30;

        // KPIs with trend comparison
        $kpis = AnalyticsService::getKPIs($clinicIds, $days);

        // Chart data
        $revenueTrend = AnalyticsService::getRevenueTrend($clinicIds, $days);
        $appointmentTrend = AnalyticsService::getAppointmentTrend($clinicIds, $days);
        $revenueBySource = AnalyticsService::getRevenueBySource($clinicIds, $days);
        $clinicComparison = AnalyticsService::getClinicComparison($clinicIds, $days);
        $vetLeaderboard = AnalyticsService::getVetLeaderboard($clinicIds, $days);
        $speciesBreakdown = AnalyticsService::getSpeciesBreakdown($clinicIds);
        $topDiagnoses = AnalyticsService::getTopDiagnoses($clinicIds, $days, 8);
        $peakHours = AnalyticsService::getPeakHours($clinicIds, $days);
        $clientRetention = AnalyticsService::getClientRetention($clinicIds, $days);
        $inventoryAlerts = AnalyticsService::getInventoryAlerts($clinicIds);
        $cancellation = AnalyticsService::getCancellationRate($clinicIds, $days);
        $insights = AnalyticsService::generateInsights($clinicIds, $days);

        return view('organisation.dashboard', compact(
            'organisation', 'user', 'days',
            'kpis', 'revenueTrend', 'appointmentTrend',
            'revenueBySource', 'clinicComparison', 'vetLeaderboard',
            'speciesBreakdown', 'topDiagnoses', 'peakHours',
            'clientRetention', 'inventoryAlerts', 'cancellation', 'insights'
        ));
    }
}
