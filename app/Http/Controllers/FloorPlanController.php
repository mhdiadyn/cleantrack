<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FloorPlanController extends Controller
{
    /**
     * Display the interactive floor plan view.
     */
    public function index(Request $request): View
    {
        $currentFloor = (int) $request->input('floor', 1);
        $today = Carbon::today()->toDateString();

        // Get all active areas on the current floor with their latest monitoring record today
        $areas = Area::where('is_active', true)
            ->where('floor', $currentFloor)
            ->with(['latestMonitoring' => function ($query) use ($today) {
                $query->whereDate('check_date', $today)->with('user');
            }])
            ->get();

        // Calculate statistics for the current floor
        $totalRooms = $areas->count();
        $cleanCount = 0;
        $inProgressCount = 0;
        $needCleaningCount = 0;
        $notInspectedCount = 0;

        foreach ($areas as $area) {
            $status = $area->current_status;
            if ($status === 'bersih') {
                $cleanCount++;
            } elseif ($status === 'perlu_tindakan') {
                $inProgressCount++;
            } elseif ($status === 'kotor') {
                $needCleaningCount++;
            } else {
                $notInspectedCount++;
            }
        }

        $stats = [
            'bersih' => $cleanCount,
            'perlu_tindakan' => $inProgressCount,
            'kotor' => $needCleaningCount,
            'belum_dimonitor' => $notInspectedCount,
        ];

        $floors = Area::where('is_active', true)
            ->select('floor')
            ->distinct()
            ->orderBy('floor')
            ->pluck('floor')
            ->toArray();

        if (empty($floors)) {
            $floors = [1, 2];
        }

        return view('floorplan.index', compact(
            'areas',
            'currentFloor',
            'floors',
            'stats',
            'totalRooms',
            'today'
        ));
    }

    /**
     * Return JSON data for real-time map updates.
     */
    public function getMapData(Request $request): JsonResponse
    {
        $currentFloor = (int) $request->input('floor', 1);
        $today = Carbon::today()->toDateString();

        $areas = Area::where('is_active', true)
            ->where('floor', $currentFloor)
            ->with(['latestMonitoring' => function ($query) use ($today) {
                $query->whereDate('check_date', $today)->with('user');
            }])
            ->get()
            ->map(function ($area) {
                $latest = $area->latestMonitoring;

                return [
                    'id' => $area->id,
                    'code' => $area->code,
                    'name' => $area->name,
                    'location' => $area->location,
                    'floor' => $area->floor,
                    'pos_x' => $area->pos_x ?? 50,
                    'pos_y' => $area->pos_y ?? 50,
                    'description' => $area->description,
                    'photo_360_url' => $area->photo_360_url,
                    'status' => $area->current_status,
                    'cleanliness_condition' => $latest ? $latest->cleanliness_condition : null,
                    'inspector_name' => $latest && $latest->user ? $latest->user->name : null,
                    'check_time' => $latest ? substr($latest->check_time, 0, 5).' WIB' : null,
                    'notes' => $latest ? $latest->notes : null,
                    'photo_before_url' => $latest ? $latest->photo_before_url : null,
                    'photo_after_url' => $latest ? $latest->photo_after_url : null,
                    'monitoring_id' => $latest ? $latest->id : null,
                ];
            });

        $cleanCount = $areas->where('status', 'bersih')->count();
        $perluTindakanCount = $areas->where('status', 'perlu_tindakan')->count();
        $kotorCount = $areas->where('status', 'kotor')->count();
        $belumCount = $areas->where('status', 'belum_dimonitor')->count();

        return response()->json([
            'success' => true,
            'floor' => $currentFloor,
            'areas' => $areas,
            'stats' => [
                'bersih' => $cleanCount,
                'perlu_tindakan' => $perluTindakanCount,
                'kotor' => $kotorCount,
                'belum_dimonitor' => $belumCount,
            ],
        ]);
    }
}
