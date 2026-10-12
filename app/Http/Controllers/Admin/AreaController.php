<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AreaController extends Controller
{
    /**
     * Display a listing of areas.
     */
    public function index(Request $request): View
    {
        $query = Area::query()->withCount('cleaningMonitorings');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'aktif') {
                $query->where('is_active', true);
            } elseif ($request->status === 'nonaktif') {
                $query->where('is_active', false);
            }
        }

        $areas = $query->latest()->paginate(10)->withQueryString();

        return view('admin.areas.index', compact('areas'));
    }

    /**
     * Show the form for creating a new area.
     */
    public function create(): View
    {
        return view('admin.areas.create');
    }

    /**
     * Store a newly created area in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:areas,name'],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'floor' => ['nullable', 'integer', 'min:1', 'max:20'],
            'pos_x' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'pos_y' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'photo_360' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:51200'],
            'photo_type' => ['nullable', 'string', 'in:panorama_360,standard'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Nama area wajib diisi.',
            'name.unique' => 'Nama area sudah ada, silakan gunakan nama lain.',
            'name.max' => 'Nama area maksimal 255 karakter.',
            'location.required' => 'Lokasi area wajib diisi.',
            'location.max' => 'Lokasi area maksimal 255 karakter.',
            'description.max' => 'Keterangan maksimal 1000 karakter.',
            'photo_360.image' => 'Foto ruangan harus berupa file gambar valid.',
            'photo_360.max' => 'Ukuran foto maksimal 50 MB.',
        ]);

        $validated['is_active'] = $request->has('is_active') ? (bool) $request->is_active : true;
        $validated['floor'] = $request->input('floor', 1);
        $validated['pos_x'] = $request->input('pos_x', 50);
        $validated['pos_y'] = $request->input('pos_y', 50);
        $validated['photo_type'] = $request->input('photo_type', 'panorama_360');

        if ($request->hasFile('photo_360')) {
            $file = $request->file('photo_360');
            $filename = 'area360_'.time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $path = 'areas_360/'.$filename;
            Storage::disk('public')->put($path, file_get_contents($file->getPathname()));
            $validated['photo_360'] = $path;
        }

        Area::create($validated);

        return redirect()->route('admin.areas.index')->with('success', 'Area kebersihan berhasil ditambahkan!');
    }

    /**
     * Display the specified area.
     */
    public function show(Area $area): View
    {
        $area->loadCount('cleaningMonitorings');
        $monitorings = $area->cleaningMonitorings()->with('user')->paginate(10);

        return view('admin.areas.show', compact('area', 'monitorings'));
    }

    /**
     * Show the form for editing the specified area.
     */
    public function edit(Area $area): View
    {
        return view('admin.areas.edit', compact('area'));
    }

    /**
     * Update the specified area in storage.
     */
    public function update(Request $request, Area $area): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:areas,name,'.$area->id],
            'location' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'floor' => ['nullable', 'integer', 'min:1', 'max:20'],
            'pos_x' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'pos_y' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'photo_360' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:51200'],
            'photo_type' => ['nullable', 'string', 'in:panorama_360,standard'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Nama area wajib diisi.',
            'name.unique' => 'Nama area sudah digunakan.',
            'name.max' => 'Nama area maksimal 255 karakter.',
            'location.required' => 'Lokasi area wajib diisi.',
            'location.max' => 'Lokasi area maksimal 255 karakter.',
            'description.max' => 'Keterangan maksimal 1000 karakter.',
            'photo_360.image' => 'Foto ruangan harus berupa file gambar valid.',
            'photo_360.max' => 'Ukuran foto maksimal 50 MB.',
        ]);

        $validated['is_active'] = $request->has('is_active') ? (bool) $request->is_active : false;
        $validated['floor'] = $request->input('floor', $area->floor);
        $validated['pos_x'] = $request->input('pos_x', $area->pos_x);
        $validated['pos_y'] = $request->input('pos_y', $area->pos_y);

        if ($request->filled('photo_type')) {
            $validated['photo_type'] = $request->input('photo_type');
        }

        if ($request->hasFile('photo_360')) {
            $file = $request->file('photo_360');
            $filename = 'area360_'.time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
            $path = 'areas_360/'.$filename;
            Storage::disk('public')->put($path, file_get_contents($file->getPathname()));
            $validated['photo_360'] = $path;
        }

        $area->update($validated);

        return redirect()->route('admin.areas.index')->with('success', 'Data area berhasil diperbarui!');
    }

    /**
     * Remove the specified area from storage.
     */
    public function destroy(Area $area): RedirectResponse
    {
        $name = $area->name;
        $area->delete();

        return redirect()->route('admin.areas.index')->with('success', "Area '{$name}' berhasil dihapus.");
    }

    /**
     * Quick update for 360 photo or standard photo from gallery or area viewer.
     */
    public function updatePhoto360(Request $request, Area $area): RedirectResponse
    {
        $request->validate([
            'photo_360' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:51200'],
            'photo_type' => ['nullable', 'string', 'in:panorama_360,standard'],
        ], [
            'photo_360.required' => 'File foto wajib dipilih.',
            'photo_360.image' => 'File harus berupa gambar.',
            'photo_360.max' => 'Ukuran file foto maksimal 50 MB.',
            'photo_360.uploaded' => 'Upload gagal. Periksa batas ukuran upload PHP lalu coba lagi.',
        ]);

        $file = $request->file('photo_360');

        if (! $file || ! $file->isValid()) {
            return back()
                ->withErrors(['photo_360' => 'Upload gagal. Periksa batas ukuran upload PHP lalu coba lagi.'])
                ->withInput();
        }

        $photoType = $request->input('photo_type', 'panorama_360');
        $filename = 'area360_'.time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
        $path = 'areas_360/'.$filename;
        Storage::disk('public')->put($path, file_get_contents($file->getPathname()));

        if ($area->photo_360 && Storage::disk('public')->exists($area->photo_360)) {
            Storage::disk('public')->delete($area->photo_360);
        }

        $area->update([
            'photo_360' => $path,
            'photo_type' => $photoType,
        ]);

        $typeName = $photoType === 'standard' ? 'Foto biasa (standar)' : 'Foto panorama 360°';

        return back()->with('success', "{$typeName} untuk area '{$area->name}' berhasil diperbarui!");
    }

    /**
     * Upload photo with area_id specified in request body.
     */
    public function uploadPhoto360General(Request $request): RedirectResponse
    {
        $request->validate([
            'area_id' => ['required', 'exists:areas,id'],
            'photo_360' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:51200'],
            'photo_type' => ['nullable', 'string', 'in:panorama_360,standard'],
        ], [
            'area_id.required' => 'Silakan pilih ruangan / area terlebih dahulu.',
            'area_id.exists' => 'Area yang dipilih tidak valid.',
            'photo_360.required' => 'File foto wajib dipilih.',
            'photo_360.image' => 'File harus berupa gambar.',
            'photo_360.max' => 'Ukuran file foto maksimal 50 MB.',
        ]);

        $area = Area::findOrFail($request->input('area_id'));
        $file = $request->file('photo_360');

        if (! $file || ! $file->isValid()) {
            return back()
                ->withErrors(['photo_360' => 'Upload gagal. Periksa batas ukuran upload PHP lalu coba lagi.'])
                ->withInput();
        }

        $photoType = $request->input('photo_type', 'panorama_360');
        $filename = 'area360_'.time().'_'.uniqid().'.'.$file->getClientOriginalExtension();
        $path = 'areas_360/'.$filename;
        Storage::disk('public')->put($path, file_get_contents($file->getPathname()));

        if ($area->photo_360 && Storage::disk('public')->exists($area->photo_360)) {
            Storage::disk('public')->delete($area->photo_360);
        }

        $area->update([
            'photo_360' => $path,
            'photo_type' => $photoType,
        ]);

        $typeName = $photoType === 'standard' ? 'Foto biasa (standar)' : 'Foto panorama 360°';

        return back()->with('success', "{$typeName} untuk area '{$area->name}' berhasil diperbarui!");
    }
}
