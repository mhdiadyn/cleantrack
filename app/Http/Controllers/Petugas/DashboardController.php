<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\CleaningMonitoring;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the petugas dashboard.
     */
    public function index(): View
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();

        // All active areas
        $allActiveAreas = Area::where('is_active', true)->get();
        $totalAreasToInspect = $allActiveAreas->count();

        // Areas inspected today (by anyone or all records today)
        $inspectedAreaIdsToday = CleaningMonitoring::whereDate('check_date', $today)
            ->pluck('area_id')
            ->unique()
            ->toArray();

        $areasInspectedCount = count($inspectedAreaIdsToday);
        $areasNotInspectedCount = max(0, $totalAreasToInspect - $areasInspectedCount);

        // Areas marked as needing cleaning today
        $areasNeedCleaningCount = CleaningMonitoring::whereDate('check_date', $today)
            ->where('status', 'Perlu Dibersihkan')
            ->distinct('area_id')
            ->count('area_id');

        // Today's area status overview
        $areasStatus = $allActiveAreas->map(function ($area) use ($today) {
            $latestToday = CleaningMonitoring::with('user')
                ->where('area_id', $area->id)
                ->whereDate('check_date', $today)
                ->latest('check_time')
                ->latest('id')
                ->first();

            $area->latest_today = $latestToday;

            return $area;
        });

        // Recent inspections by this logged in petugas
        $myRecentMonitorings = CleaningMonitoring::with('area')
            ->where('user_id', $user->id)
            ->latest('check_date')
            ->latest('check_time')
            ->latest('id')
            ->take(5)
            ->get();

        return view('petugas.dashboard', compact(
            'totalAreasToInspect',
            'areasInspectedCount',
            'areasNotInspectedCount',
            'areasNeedCleaningCount',
            'areasStatus',
            'myRecentMonitorings'
        ));
    }
}
