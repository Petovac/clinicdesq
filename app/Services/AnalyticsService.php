<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    /**
     * Revenue trend — daily totals over period
     */
    public static function getRevenueTrend(Collection $clinicIds, int $days): array
    {
        $start = Carbon::now()->subDays($days)->startOfDay();

        return DB::table('bills')
            ->whereIn('clinic_id', $clinicIds)
            ->where('status', 'confirmed')
            ->where('created_at', '>=', $start)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as amount'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();
    }

    /**
     * Revenue for previous period (comparison)
     */
    public static function getPreviousPeriodRevenue(Collection $clinicIds, int $days): float
    {
        $end = Carbon::now()->subDays($days)->startOfDay();
        $start = Carbon::now()->subDays($days * 2)->startOfDay();

        return (float) DB::table('bills')
            ->whereIn('clinic_id', $clinicIds)
            ->where('status', 'confirmed')
            ->whereBetween('created_at', [$start, $end])
            ->sum('total_amount');
    }

    /**
     * Appointment trend — daily counts with status breakdown
     */
    public static function getAppointmentTrend(Collection $clinicIds, int $days): array
    {
        $start = Carbon::now()->subDays($days)->startOfDay();

        return DB::table('appointments')
            ->whereIn('clinic_id', $clinicIds)
            ->where('scheduled_at', '>=', $start)
            ->select(
                DB::raw('DATE(scheduled_at) as date'),
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed"),
                DB::raw("SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled"),
                DB::raw("SUM(CASE WHEN status NOT IN ('completed','cancelled') THEN 1 ELSE 0 END) as other")
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();
    }

    /**
     * Top diagnoses from case sheets
     */
    public static function getTopDiagnoses(Collection $clinicIds, int $days, int $limit = 10): array
    {
        $start = Carbon::now()->subDays($days)->startOfDay();

        return DB::table('case_sheets as cs')
            ->join('appointments as a', 'a.id', '=', 'cs.appointment_id')
            ->whereIn('a.clinic_id', $clinicIds)
            ->where('cs.created_at', '>=', $start)
            ->whereNotNull('cs.diagnosis')
            ->where('cs.diagnosis', '!=', '')
            ->select('cs.diagnosis', DB::raw('COUNT(*) as count'))
            ->groupBy('cs.diagnosis')
            ->orderByDesc('count')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Species breakdown
     */
    public static function getSpeciesBreakdown(Collection $clinicIds): array
    {
        return DB::table('appointments as a')
            ->join('pets as p', 'p.id', '=', 'a.pet_id')
            ->whereIn('a.clinic_id', $clinicIds)
            ->select('p.species', DB::raw('COUNT(DISTINCT a.pet_id) as count'))
            ->groupBy('p.species')
            ->orderByDesc('count')
            ->get()
            ->toArray();
    }

    /**
     * Revenue by source (visit_fee, injection, procedure, prescription, manual)
     */
    public static function getRevenueBySource(Collection $clinicIds, int $days): array
    {
        $start = Carbon::now()->subDays($days)->startOfDay();

        return DB::table('bill_items as bi')
            ->join('bills as b', 'b.id', '=', 'bi.bill_id')
            ->whereIn('b.clinic_id', $clinicIds)
            ->where('b.status', 'confirmed')
            ->where('b.created_at', '>=', $start)
            ->where('bi.status', 'approved')
            ->select('bi.source', DB::raw('SUM(bi.total) as amount'))
            ->groupBy('bi.source')
            ->orderByDesc('amount')
            ->get()
            ->toArray();
    }

    /**
     * Clinic comparison — revenue + appts + clients per clinic
     */
    public static function getClinicComparison(Collection $clinicIds, int $days): array
    {
        $start = Carbon::now()->subDays($days)->startOfDay();
        $clinics = DB::table('clinics')->whereIn('id', $clinicIds)->pluck('name', 'id');
        $result = [];

        foreach ($clinicIds as $cid) {
            $rev = DB::table('bills')
                ->where('clinic_id', $cid)->where('status', 'confirmed')
                ->where('created_at', '>=', $start)
                ->sum('total_amount');

            $appts = DB::table('appointments')
                ->where('clinic_id', $cid)
                ->where('scheduled_at', '>=', $start)
                ->count();

            $clients = DB::table('appointments')
                ->where('clinic_id', $cid)
                ->where('scheduled_at', '>=', $start)
                ->distinct('pet_parent_id')
                ->count('pet_parent_id');

            $result[] = (object) [
                'clinic_id' => $cid,
                'name' => $clinics[$cid] ?? "Clinic $cid",
                'revenue' => (float) $rev,
                'appointments' => $appts,
                'clients' => $clients,
            ];
        }

        usort($result, fn($a, $b) => $b->revenue <=> $a->revenue);
        return $result;
    }

    /**
     * Vet leaderboard
     */
    public static function getVetLeaderboard(Collection $clinicIds, int $days): array
    {
        $start = Carbon::now()->subDays($days)->startOfDay();

        $vets = DB::table('appointments as a')
            ->join('vets as v', 'v.id', '=', 'a.vet_id')
            ->leftJoin('bills as b', function ($j) {
                $j->on('b.appointment_id', '=', 'a.id')->where('b.status', '=', 'confirmed');
            })
            ->whereIn('a.clinic_id', $clinicIds)
            ->where('a.scheduled_at', '>=', $start)
            ->select(
                'v.id', 'v.name', 'v.specialization',
                DB::raw('COUNT(DISTINCT a.id) as appointments'),
                DB::raw("SUM(CASE WHEN a.status = 'completed' THEN 1 ELSE 0 END) as completed"),
                DB::raw('COALESCE(SUM(b.total_amount), 0) as revenue'),
                DB::raw('COUNT(DISTINCT a.pet_parent_id) as unique_clients'),
            )
            ->groupBy('v.id', 'v.name', 'v.specialization')
            ->orderByDesc('revenue')
            ->get();

        foreach ($vets as $vet) {
            $repeatCount = DB::table('appointments')
                ->whereIn('clinic_id', $clinicIds)
                ->where('vet_id', $vet->id)
                ->where('scheduled_at', '>=', $start)
                ->select('pet_parent_id')
                ->groupBy('pet_parent_id')
                ->havingRaw('COUNT(*) > 1')
                ->get()->count();

            $vet->repeat_pct = $vet->unique_clients > 0
                ? round(($repeatCount / $vet->unique_clients) * 100, 1) : 0;

            // Avg consultation time (minutes)
            $avgTime = DB::table('appointments')
                ->whereIn('clinic_id', $clinicIds)
                ->where('vet_id', $vet->id)
                ->where('scheduled_at', '>=', $start)
                ->whereNotNull('checked_in_at')
                ->whereNotNull('completed_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, checked_in_at, completed_at)) as avg_min')
                ->value('avg_min');

            $vet->avg_consult_min = $avgTime ? round($avgTime) : null;
        }

        return $vets->toArray();
    }

    /**
     * Client retention metrics
     */
    public static function getClientRetention(Collection $clinicIds, int $days): object
    {
        $start = Carbon::now()->subDays($days)->startOfDay();
        $prevStart = Carbon::now()->subDays($days * 2)->startOfDay();

        // New clients this period (first appointment ever)
        $allCurrentClients = DB::table('appointments')
            ->whereIn('clinic_id', $clinicIds)
            ->where('scheduled_at', '>=', $start)
            ->distinct('pet_parent_id')
            ->pluck('pet_parent_id');

        $newClients = 0;
        $returningClients = 0;

        foreach ($allCurrentClients as $ppId) {
            $firstAppt = DB::table('appointments')
                ->whereIn('clinic_id', $clinicIds)
                ->where('pet_parent_id', $ppId)
                ->min('scheduled_at');

            if (Carbon::parse($firstAppt)->gte($start)) {
                $newClients++;
            } else {
                $returningClients++;
            }
        }

        $total = $allCurrentClients->count();
        $retentionRate = $total > 0 ? round(($returningClients / $total) * 100, 1) : 0;

        // Previous period total for trend
        $prevTotal = DB::table('appointments')
            ->whereIn('clinic_id', $clinicIds)
            ->whereBetween('scheduled_at', [$prevStart, $start])
            ->distinct('pet_parent_id')
            ->count('pet_parent_id');

        return (object) [
            'total_clients' => $total,
            'new_clients' => $newClients,
            'returning_clients' => $returningClients,
            'retention_rate' => $retentionRate,
            'prev_total' => $prevTotal,
            'growth' => $prevTotal > 0 ? round((($total - $prevTotal) / $prevTotal) * 100, 1) : 0,
        ];
    }

    /**
     * Peak hours — appointment count by day-of-week × hour
     */
    public static function getPeakHours(Collection $clinicIds, int $days): array
    {
        $start = Carbon::now()->subDays($days)->startOfDay();

        return DB::table('appointments')
            ->whereIn('clinic_id', $clinicIds)
            ->where('scheduled_at', '>=', $start)
            ->select(
                DB::raw('DAYOFWEEK(scheduled_at) as dow'),
                DB::raw('HOUR(scheduled_at) as hour'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('dow', 'hour')
            ->get()
            ->toArray();
    }

    /**
     * Cancellation rate
     */
    public static function getCancellationRate(Collection $clinicIds, int $days): object
    {
        $start = Carbon::now()->subDays($days)->startOfDay();

        $total = DB::table('appointments')
            ->whereIn('clinic_id', $clinicIds)
            ->where('scheduled_at', '>=', $start)
            ->count();

        $cancelled = DB::table('appointments')
            ->whereIn('clinic_id', $clinicIds)
            ->where('scheduled_at', '>=', $start)
            ->where('status', 'cancelled')
            ->count();

        return (object) [
            'total' => $total,
            'cancelled' => $cancelled,
            'rate' => $total > 0 ? round(($cancelled / $total) * 100, 1) : 0,
        ];
    }

    /**
     * Inventory alerts — low stock + expiring soon
     */
    public static function getInventoryAlerts(Collection $clinicIds): object
    {
        $lowStock = DB::table('clinic_inventory as ci')
            ->join('inventory_items as ii', 'ii.id', '=', 'ci.inventory_item_id')
            ->whereIn('ci.clinic_id', $clinicIds)
            ->where('ci.stock', '<=', 5)
            ->where('ci.stock', '>', 0)
            ->select('ii.name', 'ci.stock', 'ci.clinic_id')
            ->limit(10)
            ->get();

        $outOfStock = DB::table('clinic_inventory as ci')
            ->join('inventory_items as ii', 'ii.id', '=', 'ci.inventory_item_id')
            ->whereIn('ci.clinic_id', $clinicIds)
            ->where('ci.stock', '<=', 0)
            ->count();

        $expiringSoon = DB::table('inventory_batches as ib')
            ->join('inventory_items as ii', 'ii.id', '=', 'ib.inventory_item_id')
            ->whereIn('ib.clinic_id', $clinicIds)
            ->where('ib.expiry_date', '<=', Carbon::now()->addDays(30))
            ->where('ib.expiry_date', '>', Carbon::now())
            ->where('ib.quantity', '>', 0)
            ->select('ii.name', 'ib.expiry_date', 'ib.quantity', 'ib.clinic_id')
            ->orderBy('ib.expiry_date')
            ->limit(10)
            ->get();

        return (object) [
            'low_stock' => $lowStock,
            'out_of_stock_count' => $outOfStock,
            'expiring_soon' => $expiringSoon,
        ];
    }

    /**
     * KPI summary with trend comparison
     */
    public static function getKPIs(Collection $clinicIds, int $days): object
    {
        $start = Carbon::now()->subDays($days)->startOfDay();
        $prevStart = Carbon::now()->subDays($days * 2)->startOfDay();

        // Current period
        $revenue = (float) DB::table('bills')
            ->whereIn('clinic_id', $clinicIds)->where('status', 'confirmed')
            ->where('created_at', '>=', $start)->sum('total_amount');

        $appointments = DB::table('appointments')
            ->whereIn('clinic_id', $clinicIds)
            ->where('scheduled_at', '>=', $start)->count();

        $newClients = DB::table('appointments')
            ->whereIn('clinic_id', $clinicIds)
            ->where('created_at', '>=', $start)
            ->distinct('pet_parent_id')->count('pet_parent_id');

        $cancellations = DB::table('appointments')
            ->whereIn('clinic_id', $clinicIds)
            ->where('scheduled_at', '>=', $start)
            ->where('status', 'cancelled')->count();

        // Previous period
        $prevRevenue = (float) DB::table('bills')
            ->whereIn('clinic_id', $clinicIds)->where('status', 'confirmed')
            ->whereBetween('created_at', [$prevStart, $start])->sum('total_amount');

        $prevAppointments = DB::table('appointments')
            ->whereIn('clinic_id', $clinicIds)
            ->whereBetween('scheduled_at', [$prevStart, $start])->count();

        $prevClients = DB::table('appointments')
            ->whereIn('clinic_id', $clinicIds)
            ->whereBetween('created_at', [$prevStart, $start])
            ->distinct('pet_parent_id')->count('pet_parent_id');

        // Calculate trends
        $revTrend = $prevRevenue > 0 ? round((($revenue - $prevRevenue) / $prevRevenue) * 100, 1) : 0;
        $apptTrend = $prevAppointments > 0 ? round((($appointments - $prevAppointments) / $prevAppointments) * 100, 1) : 0;
        $clientTrend = $prevClients > 0 ? round((($newClients - $prevClients) / $prevClients) * 100, 1) : 0;

        $avgRevenuePerAppt = $appointments > 0 ? round($revenue / $appointments, 0) : 0;
        $cancelRate = $appointments > 0 ? round(($cancellations / $appointments) * 100, 1) : 0;

        // Repeat rate
        $repeatCount = DB::table('appointments')
            ->whereIn('clinic_id', $clinicIds)
            ->where('scheduled_at', '>=', $start)
            ->select('pet_parent_id')
            ->groupBy('pet_parent_id')
            ->havingRaw('COUNT(*) > 1')
            ->get()->count();
        $repeatRate = $newClients > 0 ? round(($repeatCount / $newClients) * 100, 1) : 0;

        return (object) compact(
            'revenue', 'prevRevenue', 'revTrend',
            'appointments', 'prevAppointments', 'apptTrend',
            'newClients', 'prevClients', 'clientTrend',
            'avgRevenuePerAppt', 'cancelRate', 'repeatRate',
            'cancellations'
        );
    }

    /**
     * Auto-generated insights
     */
    public static function generateInsights(Collection $clinicIds, int $days): array
    {
        $insights = [];
        $kpis = self::getKPIs($clinicIds, $days);

        // Revenue trend
        if ($kpis->revTrend > 10) {
            $insights[] = ['type' => 'success', 'icon' => '📈', 'text' => "Revenue is up {$kpis->revTrend}% compared to the previous period. Keep it up!"];
        } elseif ($kpis->revTrend < -10) {
            $insights[] = ['type' => 'danger', 'icon' => '📉', 'text' => "Revenue dropped {$kpis->revTrend}% compared to previous period. Investigate appointment volume and billing."];
        }

        // Cancellation rate
        if ($kpis->cancelRate > 15) {
            $insights[] = ['type' => 'warning', 'icon' => '⚠️', 'text' => "Cancellation rate is {$kpis->cancelRate}% — higher than the 15% benchmark. Consider appointment reminders."];
        }

        // Repeat rate
        if ($kpis->repeatRate > 50) {
            $insights[] = ['type' => 'success', 'icon' => '🔄', 'text' => "Great client retention! {$kpis->repeatRate}% of clients are repeat visitors."];
        } elseif ($kpis->repeatRate < 20 && $kpis->newClients > 5) {
            $insights[] = ['type' => 'warning', 'icon' => '🔄', 'text' => "Only {$kpis->repeatRate}% repeat rate. Consider follow-up reminders and loyalty programs."];
        }

        // Clinic comparison insights
        if ($clinicIds->count() > 1) {
            $comparison = self::getClinicComparison($clinicIds, $days);
            if (count($comparison) >= 2) {
                $best = $comparison[0];
                $worst = end($comparison);
                if ($best->revenue > 0 && $worst->revenue > 0) {
                    $diff = round((($best->revenue - $worst->revenue) / $best->revenue) * 100);
                    if ($diff > 30) {
                        $insights[] = ['type' => 'info', 'icon' => '🏥', 'text' => "{$best->name} leads with ₹" . number_format($best->revenue) . " revenue. {$worst->name} is {$diff}% behind — may need attention."];
                    }
                }
            }
        }

        // Vet leaderboard insights
        $vets = self::getVetLeaderboard($clinicIds, $days);
        if (count($vets) > 0) {
            $topVet = $vets[0];
            if ($topVet->repeat_pct > 60) {
                $vetLabel = str_starts_with($topVet->name, 'Dr') ? $topVet->name : 'Dr. ' . $topVet->name;
                $insights[] = ['type' => 'success', 'icon' => '⭐', 'text' => "{$vetLabel} has the highest repeat rate ({$topVet->repeat_pct}%) — strongest client relationships."];
            }
        }

        // Peak hours
        $peaks = self::getPeakHours($clinicIds, $days);
        if (count($peaks) > 0) {
            usort($peaks, fn($a, $b) => $b->count <=> $a->count);
            $dayNames = ['', 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
            $topPeak = $peaks[0];
            $dayName = $dayNames[$topPeak->dow] ?? '';
            $hour = $topPeak->hour;
            $ampm = $hour >= 12 ? 'PM' : 'AM';
            $h12 = $hour > 12 ? $hour - 12 : ($hour == 0 ? 12 : $hour);
            $insights[] = ['type' => 'info', 'icon' => '⏰', 'text' => "Busiest time: {$dayName} at {$h12}{$ampm} ({$topPeak->count} appointments). Consider extra staffing."];
        }

        // Top diagnoses
        $diagnoses = self::getTopDiagnoses($clinicIds, $days, 3);
        if (count($diagnoses) >= 2) {
            $names = array_map(fn($d) => $d->diagnosis, array_slice($diagnoses, 0, 3));
            $shortNames = array_map(fn($n) => strlen($n) > 30 ? substr($n, 0, 30) . '...' : $n, $names);
            $insights[] = ['type' => 'info', 'icon' => '🩺', 'text' => 'Top diagnoses: ' . implode(', ', $shortNames)];
        }

        return $insights;
    }
}
