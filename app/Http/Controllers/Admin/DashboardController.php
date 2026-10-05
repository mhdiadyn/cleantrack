<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\CleaningMonitoring;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(): View
    {
        $today = Carbon::today()->toDateString();

        // Total active areas
        $totalAreas = Area::where('is_active', true)->count();

        // Total checks today
        $totalChecksToday = CleaningMonitoring::whereDate('check_date', $today)->count();

        // Area counts by today's latest monitoring or total today's status
        $areaSudahBersih = CleaningMonitoring::whereDate('check_date', $today)
            ->where('status', 'Sudah Dibersihkan')
            ->distinct('area_id')
            ->count('area_id');

        $areaPerluDibersihkan = CleaningMonitoring::whereDate('check_date', $today)
            ->where('status', 'Perlu Dibersihkan')
            ->distinct('area_id')
            ->count('area_id');

        $areaSedangDibersihkan = CleaningMonitoring::whereDate('check_date', $today)
            ->where('status', 'Sedang Dibersihkan')
            ->distinct('area_id')
            ->count('area_id');

        // Recent monitoring records (10 latest)
        $recentMonitorings = CleaningMonitoring::with(['user', 'area'])
            ->latest('check_date')
            ->latest('check_time')
            ->latest('id')
            ->take(10)
            ->get();

        // Status counts for Chart (All time or this month)
        $statusCounts = [
            'Sudah Dibersihkan' => CleaningMonitoring::where('status', 'Sudah Dibersihkan')->count(),
            'Sedang Dibersihkan' => CleaningMonitoring::where('status', 'Sedang Dibersihkan')->count(),
            'Perlu Dibersihkan' => CleaningMonitoring::where('status', 'Perlu Dibersihkan')->count(),
            'Belum Diperiksa' => CleaningMonitoring::where('status', 'Belum Diperiksa')->count(),
        ];

        // Condition counts for secondary chart/metrics
        $conditionCounts = [
            'Bersih' => CleaningMonitoring::where('cleanliness_condition', 'Bersih')->count(),
            'Cukup Bersih' => CleaningMonitoring::where('cleanliness_condition', 'Cukup Bersih')->count(),
            'Kotor' => CleaningMonitoring::where('cleanliness_condition', 'Kotor')->count(),
            'Sangat Kotor' => CleaningMonitoring::where('cleanliness_condition', 'Sangat Kotor')->count(),
        ];

        return view('admin.dashboard', compact(
            'totalAreas',
            'areaSudahBersih',
            'areaPerluDibersihkan',
            'areaSedangDibersihkan',
            'totalChecksToday',
            'recentMonitorings',
            'statusCounts',
            'conditionCounts'
        ));
    }
}
