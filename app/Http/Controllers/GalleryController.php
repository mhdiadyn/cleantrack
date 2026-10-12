<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\CleaningMonitoring;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GalleryController extends Controller
{
    /**
     * Display a comprehensive facility media gallery.
     */
    public function index(Request $request): View
    {
        $areas = Area::orderBy('name')->get();
        $selectedAreaId = $request->input('area_id');
        $selectedType = $request->input('type', 'all'); // 'all', '360', 'comparison', 'monitorings'
        $selectedStatus = $request->input('status');
        $search = $request->input('search');

        // Base query for monitorings with any photos
        $query = CleaningMonitoring::with(['area', 'user'])
            ->where(function ($q) {
                $q->whereNotNull('photo_before')
                    ->orWhereNotNull('photo_after')
                    ->orWhereNotNull('photo_360');
            })
            ->latest('check_date');

        if ($selectedAreaId) {
            $query->where('area_id', $selectedAreaId);
        }

        if ($selectedStatus) {
            $query->where('status', $selectedStatus);
        }

        if ($selectedType === '360') {
            $query->whereNotNull('photo_360')->where(function ($q) {
                $q->where('photo_360_type', 'panorama_360')->orWhereNull('photo_360_type');
            });
        } elseif ($selectedType === 'standard') {
            $query->whereNotNull('photo_360')->where('photo_360_type', 'standard');
        } elseif ($selectedType === 'comparison') {
            $query->whereNotNull('photo_before')->whereNotNull('photo_after');
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('area', function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                })->orWhere('notes', 'like', "%{$search}%");
            });
        }

        $monitorings = $query->paginate(12)->withQueryString();

        // Also fetch areas that have dedicated 360 panoramic views or standard facility photos
        $areasWithPhotosQuery = Area::whereNotNull('photo_360');
        if ($selectedType === '360') {
            $areasWithPhotosQuery->where(function ($q) {
                $q->where('photo_type', 'panorama_360')->orWhereNull('photo_type');
            });
        } elseif ($selectedType === 'standard') {
            $areasWithPhotosQuery->where('photo_type', 'standard');
        }
        $areasWith360 = $areasWithPhotosQuery->get();

        // Statistics for summary badges
        $totalPhotos = CleaningMonitoring::whereNotNull('photo_before')->count() +
                       CleaningMonitoring::whereNotNull('photo_after')->count() +
                       CleaningMonitoring::whereNotNull('photo_360')->count();
        $total360 = Area::whereNotNull('photo_360')->where(function ($q) {
            $q->where('photo_type', 'panorama_360')->orWhereNull('photo_type');
        })->count();
        $totalStandard = Area::whereNotNull('photo_360')->where('photo_type', 'standard')->count();
        $totalComparisons = CleaningMonitoring::whereNotNull('photo_before')->whereNotNull('photo_after')->count();

        return view('gallery.index', compact(
            'monitorings',
            'areas',
            'areasWith360',
            'selectedAreaId',
            'selectedType',
            'selectedStatus',
            'search',
            'totalPhotos',
            'total360',
            'totalStandard',
            'totalComparisons'
        ));
    }
}
