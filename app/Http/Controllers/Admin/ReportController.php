<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\CleaningMonitoring;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Display the filterable report page.
     */
    public function index(Request $request): View
    {
        $startDate = $request->input('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());

        $query = CleaningMonitoring::with(['user', 'area'])
            ->whereBetween('check_date', [$startDate, $endDate]);

        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('condition')) {
            $query->where('cleanliness_condition', $request->condition);
        }

        $allResults = (clone $query)->get();

        // Calculate summary metrics based on filtered results
        $totalPemeriksaan = $allResults->count();
        $totalBersih = $allResults->where('cleanliness_condition', 'Bersih')->count();
        $totalKotor = $allResults->whereIn('cleanliness_condition', ['Kotor', 'Sangat Kotor'])->count();
        $totalPerluDibersihkan = $allResults->where('status', 'Perlu Dibersihkan')->count();

        $monitorings = $query->latest('check_date')
            ->latest('check_time')
            ->paginate(15)
            ->withQueryString();

        $areas = Area::orderBy('name')->get();
        $petugasList = User::where('role', 'petugas')->orderBy('name')->get();

        return view('admin.reports.index', compact(
            'monitorings',
            'totalPemeriksaan',
            'totalBersih',
            'totalKotor',
            'totalPerluDibersihkan',
            'startDate',
            'endDate',
            'areas',
            'petugasList'
        ));
    }

    /**
     * Printable view for official cleaning monitoring report.
     */
    public function print(Request $request): View
    {
        $startDate = $request->input('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());

        $query = CleaningMonitoring::with(['user', 'area'])
            ->whereBetween('check_date', [$startDate, $endDate]);

        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('condition')) {
            $query->where('cleanliness_condition', $request->condition);
        }

        $monitorings = $query->latest('check_date')->latest('check_time')->get();

        $totalPemeriksaan = $monitorings->count();
        $totalBersih = $monitorings->where('cleanliness_condition', 'Bersih')->count();
        $totalKotor = $monitorings->whereIn('cleanliness_condition', ['Kotor', 'Sangat Kotor'])->count();
        $totalPerluDibersihkan = $monitorings->where('status', 'Perlu Dibersihkan')->count();

        $selectedArea = $request->filled('area_id') ? Area::find($request->area_id) : null;
        $selectedPetugas = $request->filled('user_id') ? User::find($request->user_id) : null;

        return view('admin.reports.print', compact(
            'monitorings',
            'totalPemeriksaan',
            'totalBersih',
            'totalKotor',
            'totalPerluDibersihkan',
            'startDate',
            'endDate',
            'selectedArea',
            'selectedPetugas'
        ));
    }

    /**
     * Export report data to Microsoft Excel format (.xls).
     */
    public function exportExcel(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());

        $query = CleaningMonitoring::with(['user', 'area'])
            ->whereBetween('check_date', [$startDate, $endDate]);

        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('condition')) {
            $query->where('cleanliness_condition', $request->condition);
        }

        $monitorings = $query->latest('check_date')->latest('check_time')->get();

        $filename = 'Laporan_Kebersihan_CleanTrack_'.Carbon::parse($startDate)->format('Ymd').'_'.Carbon::parse($endDate)->format('Ymd').'.xls';

        return response()->view('admin.reports.export_excel', compact(
            'monitorings',
            'startDate',
            'endDate'
        ))->header('Content-Type', 'application/vnd.ms-excel; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"')
            ->header('Cache-Control', 'max-age=0');
    }

    /**
     * Export report data to Microsoft Word format (.doc).
     */
    public function exportWord(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());

        $query = CleaningMonitoring::with(['user', 'area'])
            ->whereBetween('check_date', [$startDate, $endDate]);

        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('condition')) {
            $query->where('cleanliness_condition', $request->condition);
        }

        $monitorings = $query->latest('check_date')->latest('check_time')->get();

        $totalPemeriksaan = $monitorings->count();
        $totalBersih = $monitorings->where('cleanliness_condition', 'Bersih')->count();
        $totalKotor = $monitorings->whereIn('cleanliness_condition', ['Kotor', 'Sangat Kotor'])->count();
        $totalPerluDibersihkan = $monitorings->where('status', 'Perlu Dibersihkan')->count();

        $filename = 'Laporan_Kebersihan_CleanTrack_'.Carbon::parse($startDate)->format('Ymd').'_'.Carbon::parse($endDate)->format('Ymd').'.doc';

        return response()->view('admin.reports.export_word', compact(
            'monitorings',
            'totalPemeriksaan',
            'totalBersih',
            'totalKotor',
            'totalPerluDibersihkan',
            'startDate',
            'endDate'
        ))->header('Content-Type', 'application/msword; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"')
            ->header('Cache-Control', 'max-age=0');
    }
}
