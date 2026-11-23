<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Area;
use App\Models\CleaningMonitoring;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReportController extends Controller
{
    /**
     * Display a listing of cleaning reports for the logged-in petugas.
     */
    public function index(Request $request): View
    {
        $userId = Auth::id();

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = CleaningMonitoring::with(['area', 'user'])
            ->where('user_id', $userId);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('check_date', [$startDate, $endDate]);
        } elseif ($request->filled('start_date')) {
            $query->whereDate('check_date', '>=', $startDate);
        } elseif ($request->filled('end_date')) {
            $query->whereDate('check_date', '<=', $endDate);
        }

        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('condition')) {
            $query->where('cleanliness_condition', $request->condition);
        }

        // Summary metrics based on filtered results for the authenticated petugas
        $allResults = (clone $query)->get();

        $totalPemeriksaan = $allResults->count();
        $totalSelesai = $allResults->where('status', 'Sudah Dibersihkan')->count();
        $totalProses = $allResults->where('status', 'Sedang Dibersihkan')->count();
        $totalPerluTindakan = $allResults->whereIn('status', ['Perlu Dibersihkan', 'Belum Diperiksa'])->count();

        $monitorings = $query->latest('check_date')
            ->latest('check_time')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $areas = Area::where('is_active', true)->orderBy('name')->get();

        return view('petugas.reports.index', compact(
            'monitorings',
            'totalPemeriksaan',
            'totalSelesai',
            'totalProses',
            'totalPerluTindakan',
            'areas',
            'startDate',
            'endDate'
        ));
    }

    public function print(Request $request): View
    {
        [$startDate, $endDate, $query] = $this->filteredReportQuery($request);
        $monitorings = $query->latest('check_date')->latest('check_time')->latest('id')->get();

        $totalPemeriksaan = $monitorings->count();
        $totalBersih = $monitorings->where('cleanliness_condition', 'Bersih')->count();
        $totalKotor = $monitorings->whereIn('cleanliness_condition', ['Kotor', 'Sangat Kotor'])->count();
        $totalPerluDibersihkan = $monitorings->where('status', 'Perlu Dibersihkan')->count();
        $selectedArea = $request->filled('area_id') ? Area::find($request->area_id) : null;
        $selectedPetugas = null;

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

    public function exportExcel(Request $request)
    {
        [$startDate, $endDate, $query] = $this->filteredReportQuery($request);
        $monitorings = $query->latest('check_date')->latest('check_time')->latest('id')->get();

        $filename = 'Laporan_Kebersihan_Petugas_CleanTrack_'.Carbon::parse($startDate)->format('Ymd').'_'.Carbon::parse($endDate)->format('Ymd').'.xls';

        return response()->view('admin.reports.export_excel', compact('monitorings', 'startDate', 'endDate'))
            ->header('Content-Type', 'application/vnd.ms-excel; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"')
            ->header('Cache-Control', 'max-age=0');
    }

    public function exportWord(Request $request)
    {
        [$startDate, $endDate, $query] = $this->filteredReportQuery($request);
        $monitorings = $query->latest('check_date')->latest('check_time')->latest('id')->get();

        $totalPemeriksaan = $monitorings->count();
        $totalBersih = $monitorings->where('cleanliness_condition', 'Bersih')->count();
        $totalKotor = $monitorings->whereIn('cleanliness_condition', ['Kotor', 'Sangat Kotor'])->count();
        $totalPerluDibersihkan = $monitorings->where('status', 'Perlu Dibersihkan')->count();
        $filename = 'Laporan_Kebersihan_Petugas_CleanTrack_'.Carbon::parse($startDate)->format('Ymd').'_'.Carbon::parse($endDate)->format('Ymd').'.doc';

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

    private function filteredReportQuery(Request $request): array
    {
        $startDate = $request->input('start_date', Carbon::today()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());

        $query = CleaningMonitoring::with(['area', 'user'])
            ->where('user_id', Auth::id())
            ->whereBetween('check_date', [$startDate, $endDate]);

        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('condition')) {
            $query->where('cleanliness_condition', $request->condition);
        }

        return [$startDate, $endDate, $query];
    }
}
