<?php

namespace App\Http\Controllers\Organisation;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Organisation;
use App\Models\Clinic;
use App\Models\Vet;
use App\Models\User;
use App\Models\Bill;
use App\Models\Appointment;
use App\Models\InventoryMovement;
use Carbon\Carbon;

class OrganisationDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $organisation = Organisation::findOrFail($user->organisation_id);

        $clinicIds = Clinic::where('organisation_id', $organisation->id)->pluck('id');
        $clinics = Clinic::where('organisation_id', $organisation->id)->get();

        // ─── Period: current month by default ───
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->endOfDay();

        // ─── Top-level metrics ───
        $totalClinics = $clinicIds->count();
        $totalVets = Vet::whereHas('clinics', fn($q) => $q->whereIn('clinics.id', $clinicIds))->count();
        $totalUsers = User::where('organisation_id', $organisation->id)->count();

        $totalRevenue = Bill::whereIn('clinic_id', $clinicIds)
            ->where('status', 'confirmed')
            ->sum('total_amount');

        $monthRevenue = Bill::whereIn('clinic_id', $clinicIds)
            ->where('status', 'confirmed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_amount');

        $totalAppointments = Appointment::whereIn('clinic_id', $clinicIds)->count();
        $monthAppointments = Appointment::whereIn('clinic_id', $clinicIds)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $completedAppointments = Appointment::whereIn('clinic_id', $clinicIds)
            ->where('status', 'completed')
            ->count();

        // Unique clients (pet parents)
        $totalClients = Appointment::whereIn('clinic_id', $clinicIds)
            ->distinct('pet_parent_id')
            ->count('pet_parent_id');

        // Repeat clients (pet parents with >1 appointment)
        $repeatClients = DB::table('appointments')
            ->whereIn('clinic_id', $clinicIds)
            ->select('pet_parent_id')
            ->groupBy('pet_parent_id')
            ->havingRaw('COUNT(*) > 1')
            ->get()
            ->count();

        $repeatPercentage = $totalClients > 0 ? round(($repeatClients / $totalClients) * 100, 1) : 0;

        // ─── Per-clinic performance ───
        $clinicPerformance = [];
        foreach ($clinics as $clinic) {
            $cRevenue = Bill::where('clinic_id', $clinic->id)
                ->where('status', 'confirmed')
                ->sum('total_amount');

            $cMonthRevenue = Bill::where('clinic_id', $clinic->id)
                ->where('status', 'confirmed')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('total_amount');

            $cAppointments = Appointment::where('clinic_id', $clinic->id)->count();
            $cMonthAppts = Appointment::where('clinic_id', $clinic->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->count();

            $cDoctors = DB::table('clinic_vet')
                ->where('clinic_id', $clinic->id)
                ->where('is_active', true)
                ->count();

            // Inventory usage value (cost of items consumed)
            $cInventoryUsed = InventoryMovement::where('clinic_id', $clinic->id)
                ->where('movement_type', 'usage')
                ->sum('quantity');

            $cClients = Appointment::where('clinic_id', $clinic->id)
                ->distinct('pet_parent_id')
                ->count('pet_parent_id');

            $clinicPerformance[] = [
                'clinic' => $clinic,
                'revenue' => $cRevenue,
                'month_revenue' => $cMonthRevenue,
                'appointments' => $cAppointments,
                'month_appointments' => $cMonthAppts,
                'doctors' => $cDoctors,
                'inventory_used' => $cInventoryUsed,
                'clients' => $cClients,
            ];
        }

        // Sort by revenue descending (rank)
        usort($clinicPerformance, fn($a, $b) => $b['revenue'] <=> $a['revenue']);

        // ─── Vet performance ───
        $vetPerformance = DB::table('appointments as a')
            ->join('vets as v', 'v.id', '=', 'a.vet_id')
            ->leftJoin('bills as b', function ($j) {
                $j->on('b.appointment_id', '=', 'a.id')
                  ->where('b.status', '=', 'confirmed');
            })
            ->whereIn('a.clinic_id', $clinicIds)
            ->select(
                'v.id',
                'v.name',
                'v.specialization',
                DB::raw('COUNT(DISTINCT a.id) as total_appointments'),
                DB::raw('SUM(CASE WHEN a.status = "completed" THEN 1 ELSE 0 END) as completed'),
                DB::raw('COALESCE(SUM(b.total_amount), 0) as revenue'),
                DB::raw('COUNT(DISTINCT a.pet_parent_id) as unique_clients'),
            )
            ->groupBy('v.id', 'v.name', 'v.specialization')
            ->orderByDesc('revenue')
            ->get()
            ->map(function ($vet) use ($clinicIds) {
                // Calculate repeat % for this vet
                $vetRepeat = DB::table('appointments')
                    ->whereIn('clinic_id', $clinicIds)
                    ->where('vet_id', $vet->id)
                    ->select('pet_parent_id')
                    ->groupBy('pet_parent_id')
                    ->havingRaw('COUNT(*) > 1')
                    ->get()
                    ->count();

                $vet->repeat_clients = $vetRepeat;
                $vet->repeat_pct = $vet->unique_clients > 0
                    ? round(($vetRepeat / $vet->unique_clients) * 100, 1)
                    : 0;

                return $vet;
            });

        return view('organisation.dashboard', [
            'organisation' => $organisation,
            'user' => $user,

            // Top metrics
            'totalClinics' => $totalClinics,
            'totalVets' => $totalVets,
            'totalUsers' => $totalUsers,
            'totalRevenue' => $totalRevenue,
            'monthRevenue' => $monthRevenue,
            'totalAppointments' => $totalAppointments,
            'monthAppointments' => $monthAppointments,
            'completedAppointments' => $completedAppointments,
            'totalClients' => $totalClients,
            'repeatClients' => $repeatClients,
            'repeatPercentage' => $repeatPercentage,

            // Per-clinic
            'clinicPerformance' => $clinicPerformance,

            // Per-vet
            'vetPerformance' => $vetPerformance,

            // Permissions
            'showBilling' => $user->hasPermission('billing.view'),
            'showInventory' => $user->hasPermission('inventory.view'),
            'showClinics' => $user->hasPermission('clinics.view'),
            'showUsers' => $user->hasPermission('users.view'),
        ]);
    }
}
