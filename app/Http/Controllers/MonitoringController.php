<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\CleaningMonitoring;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MonitoringController extends Controller
{
    /**
     * Display all monitoring records (Admin & General view).
     */
    public function index(Request $request): View
    {
        $query = CleaningMonitoring::with(['user', 'area']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('area', function ($qa) use ($search) {
                    $qa->where('name', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%");
                })->orWhereHas('user', function ($qu) use ($search) {
                    $qu->where('name', 'like', "%{$search}%");
                })->orWhere('notes', 'like', "%{$search}%");
            });
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

        if ($request->filled('date')) {
            $query->whereDate('check_date', $request->date);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('check_date', [$request->start_date, $request->end_date]);
        }

        $monitorings = $query->latest('check_date')
            ->latest('check_time')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $areas = Area::where('is_active', true)->orderBy('name')->get();

        return view('monitoring.index', compact('monitorings', 'areas'));
    }

    /**
     * Display personal monitoring records for the logged-in Petugas.
     */
    public function myMonitoring(Request $request): View
    {
        $user = Auth::user();
        $query = CleaningMonitoring::with(['area'])
            ->where('user_id', $user->id);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('area', function ($qa) use ($search) {
                    $qa->where('name', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%");
                })->orWhere('notes', 'like', "%{$search}%");
            });
        }

        if ($request->filled('area_id')) {
            $query->where('area_id', $request->area_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('check_date', $request->date);
        }

        $monitorings = $query->latest('check_date')
            ->latest('check_time')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        $areas = Area::where('is_active', true)->orderBy('name')->get();

        return view('petugas.my-monitoring', compact('monitorings', 'areas'));
    }

    /**
     * Show the form for creating a new inspection.
     */
    public function create(Request $request): View
    {
        $areas = Area::where('is_active', true)->orderBy('name')->get();
        $selectedAreaId = $request->input('area_id');
        $currentTime = Carbon::now('Asia/Jakarta');

        return view('monitoring.create', compact('areas', 'selectedAreaId', 'currentTime'));
    }

    /**
     * Store a newly created inspection in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'area_id' => ['required', 'exists:areas,id'],
            'cleanliness_condition' => ['required', Rule::in(['Bersih', 'Cukup Bersih', 'Kotor', 'Sangat Kotor'])],
            'status' => ['required', Rule::in(['Belum Diperiksa', 'Perlu Dibersihkan', 'Sedang Dibersihkan', 'Sudah Dibersihkan'])],
            'start_time' => ['nullable', 'string', 'max:10'],
            'end_time' => ['nullable', 'string', 'max:10'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'photo_before' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
            'photo_after' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
            'photo_360' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:51200'],
            'photo_360_type' => ['nullable', 'string', 'in:panorama_360,standard'],
        ], [
            'area_id.required' => 'Silakan pilih area yang diperiksa.',
            'area_id.exists' => 'Area yang dipilih tidak valid.',
            'cleanliness_condition.required' => 'Kondisi kebersihan wajib dipilih.',
            'cleanliness_condition.in' => 'Kondisi kebersihan tidak valid.',
            'status.required' => 'Status kebersihan wajib dipilih.',
            'status.in' => 'Status kebersihan tidak valid.',
            'photo_before.image' => 'File foto sebelum harus berupa gambar.',
            'photo_before.max' => 'Ukuran foto sebelum maksimal 10 MB.',
            'photo_after.image' => 'File foto sesudah harus berupa gambar.',
            'photo_after.max' => 'Ukuran foto sesudah maksimal 10 MB.',
            'photo_360.image' => 'File foto 360 harus berupa gambar panorama.',
            'photo_360.max' => 'Ukuran foto 360 maksimal 50 MB.',
            'notes.max' => 'Catatan maksimal 1000 karakter.',
        ]);

        $now = Carbon::now('Asia/Jakarta');

        $startTime = $validated['start_time'] ?? null;
        $endTime = $validated['end_time'] ?? null;

        // Auto default if left blank
        if (! $startTime) {
            $startTime = $now->copy()->subMinutes(25)->format('H:i:s');
        } elseif (strlen($startTime) === 5) {
            $startTime .= ':00';
        }

        if (! $endTime) {
            $endTime = $now->format('H:i:s');
        } elseif (strlen($endTime) === 5) {
            $endTime .= ':00';
        }

        $data = [
            'user_id' => Auth::id(),
            'area_id' => $validated['area_id'],
            'cleanliness_condition' => $validated['cleanliness_condition'],
            'status' => $validated['status'],
            'start_time' => $startTime,
            'end_time' => $endTime,
            'notes' => $validated['notes'] ?? null,
            'check_date' => $now->toDateString(),
            'check_time' => $now->toTimeString(),
        ];

        // Handle photo_before upload
        if ($request->hasFile('photo_before')) {
            $file = $request->file('photo_before');
            $filename = 'before_'.time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $path = 'cleaning_photos/'.$filename;
            Storage::disk('public')->put($path, file_get_contents($file->getPathname()));
            $data['photo_before'] = $path;
        }

        // Handle photo_after upload
        if ($request->hasFile('photo_after')) {
            $file = $request->file('photo_after');
            $filename = 'after_'.time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $path = 'cleaning_photos/'.$filename;
            Storage::disk('public')->put($path, file_get_contents($file->getPathname()));
            $data['photo_after'] = $path;
        }

        // Handle photo_360 upload
        if ($request->hasFile('photo_360')) {
            $file = $request->file('photo_360');
            $filename = '360_'.time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $path = 'cleaning_photos/'.$filename;
            Storage::disk('public')->put($path, file_get_contents($file->getPathname()));
            $data['photo_360'] = $path;
            $data['photo_360_type'] = $request->input('photo_360_type', 'panorama_360');
        }

        $monitoring = CleaningMonitoring::create($data);

        $redirectRoute = Auth::user()->role === 'admin'
            ? route('monitoring.show', $monitoring->id)
            : route('petugas.my-monitoring');

        return redirect($redirectRoute)->with('success', 'Hasil monitoring kebersihan berhasil disimpan!');
    }

    /**
     * Display the specified monitoring record (Report layout).
     */
    public function show(CleaningMonitoring $monitoring): View
    {
        $monitoring->load(['user', 'area']);

        return view('monitoring.show', compact('monitoring'));
    }

    /**
     * Show the form for editing the specified monitoring record.
     */
    public function edit(CleaningMonitoring $monitoring): View|RedirectResponse
    {
        // Authorization check: Admin can edit all, Petugas can edit their own
        if (Auth::user()->role !== 'admin' && $monitoring->user_id !== Auth::id()) {
            return redirect()->route('petugas.my-monitoring')->with('error', 'Anda hanya dapat mengedit data monitoring yang Anda buat sendiri.');
        }

        $areas = Area::where('is_active', true)->orderBy('name')->get();

        return view('monitoring.edit', compact('monitoring', 'areas'));
    }

    /**
     * Update the specified monitoring record in storage.
     */
    public function update(Request $request, CleaningMonitoring $monitoring): RedirectResponse
    {
        if (Auth::user()->role !== 'admin' && $monitoring->user_id !== Auth::id()) {
            return back()->with('error', 'Anda tidak memiliki izin untuk mengubah data ini.');
        }

        $validated = $request->validate([
            'area_id' => ['required', 'exists:areas,id'],
            'cleanliness_condition' => ['required', Rule::in(['Bersih', 'Cukup Bersih', 'Kotor', 'Sangat Kotor'])],
            'status' => ['required', Rule::in(['Belum Diperiksa', 'Perlu Dibersihkan', 'Sedang Dibersihkan', 'Sudah Dibersihkan'])],
            'start_time' => ['nullable', 'string', 'max:10'],
            'end_time' => ['nullable', 'string', 'max:10'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'photo_before' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
            'photo_after' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
            'photo_360' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:51200'],
            'photo_360_type' => ['nullable', 'string', 'in:panorama_360,standard'],
        ], [
            'area_id.required' => 'Silakan pilih area.',
            'area_id.exists' => 'Area yang dipilih tidak valid.',
            'cleanliness_condition.required' => 'Kondisi kebersihan wajib dipilih.',
            'cleanliness_condition.in' => 'Kondisi kebersihan tidak valid.',
            'status.required' => 'Status kebersihan wajib dipilih.',
            'status.in' => 'Status kebersihan tidak valid.',
            'photo_before.image' => 'File foto sebelum harus berupa gambar.',
            'photo_before.max' => 'Ukuran foto sebelum maksimal 10 MB.',
            'photo_after.image' => 'File foto sesudah harus berupa gambar.',
            'photo_after.max' => 'Ukuran foto sesudah maksimal 10 MB.',
            'photo_360.image' => 'File foto ruangan harus berupa gambar valid.',
            'photo_360.max' => 'Ukuran foto ruangan maksimal 50 MB.',
            'notes.max' => 'Catatan maksimal 1000 karakter.',
        ]);

        $startTime = $validated['start_time'] ?? $monitoring->start_time;
        if ($startTime && strlen($startTime) === 5) {
            $startTime .= ':00';
        }

        $endTime = $validated['end_time'] ?? $monitoring->end_time;
        if ($endTime && strlen($endTime) === 5) {
            $endTime .= ':00';
        }

        $data = [
            'area_id' => $validated['area_id'],
            'cleanliness_condition' => $validated['cleanliness_condition'],
            'status' => $validated['status'],
            'start_time' => $startTime,
            'end_time' => $endTime,
            'notes' => $validated['notes'] ?? null,
        ];

        if ($request->filled('photo_360_type')) {
            $data['photo_360_type'] = $request->input('photo_360_type');
        }

        // Handle new photo_before
        if ($request->hasFile('photo_before')) {
            if ($monitoring->photo_before && Storage::disk('public')->exists($monitoring->photo_before)) {
                Storage::disk('public')->delete($monitoring->photo_before);
            }
            $file = $request->file('photo_before');
            $filename = 'before_'.time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $path = 'cleaning_photos/'.$filename;
            Storage::disk('public')->put($path, file_get_contents($file->getPathname()));
            $data['photo_before'] = $path;
        }

        // Handle new photo_after
        if ($request->hasFile('photo_after')) {
            if ($monitoring->photo_after && Storage::disk('public')->exists($monitoring->photo_after)) {
                Storage::disk('public')->delete($monitoring->photo_after);
            }
            $file = $request->file('photo_after');
            $filename = 'after_'.time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $path = 'cleaning_photos/'.$filename;
            Storage::disk('public')->put($path, file_get_contents($file->getPathname()));
            $data['photo_after'] = $path;
        }

        // Handle new photo_360
        if ($request->hasFile('photo_360')) {
            if ($monitoring->photo_360 && Storage::disk('public')->exists($monitoring->photo_360)) {
                Storage::disk('public')->delete($monitoring->photo_360);
            }
            $file = $request->file('photo_360');
            $filename = '360_'.time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $path = 'cleaning_photos/'.$filename;
            Storage::disk('public')->put($path, file_get_contents($file->getPathname()));
            $data['photo_360'] = $path;
            $data['photo_360_type'] = $request->input('photo_360_type', 'panorama_360');
        }

        $monitoring->update($data);

        return redirect()->route('monitoring.show', $monitoring->id)->with('success', 'Data monitoring kebersihan berhasil diperbarui!');
    }

    /**
     * Remove the specified monitoring record from storage (Admin only).
     */
    public function destroy(CleaningMonitoring $monitoring): RedirectResponse
    {
        if (Auth::user()->role !== 'admin') {
            return back()->with('error', 'Hanya Admin yang dapat menghapus data monitoring.');
        }

        if ($monitoring->photo_before && Storage::disk('public')->exists($monitoring->photo_before)) {
            Storage::disk('public')->delete($monitoring->photo_before);
        }

        if ($monitoring->photo_after && Storage::disk('public')->exists($monitoring->photo_after)) {
            Storage::disk('public')->delete($monitoring->photo_after);
        }

        if ($monitoring->photo_360 && Storage::disk('public')->exists($monitoring->photo_360)) {
            Storage::disk('public')->delete($monitoring->photo_360);
        }

        $monitoring->delete();

        return redirect()->route('monitoring.index')->with('success', 'Data monitoring berhasil dihapus.');
    }
}
