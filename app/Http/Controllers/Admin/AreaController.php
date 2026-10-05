<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Area;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
            'is_active' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Nama area wajib diisi.',
            'name.unique' => 'Nama area sudah ada, silakan gunakan nama lain.',
            'name.max' => 'Nama area maksimal 255 karakter.',
            'location.required' => 'Lokasi area wajib diisi.',
            'location.max' => 'Lokasi area maksimal 255 karakter.',
            'description.max' => 'Keterangan maksimal 1000 karakter.',
        ]);

        $validated['is_active'] = $request->has('is_active') ? (bool) $request->is_active : true;

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
            'is_active' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'Nama area wajib diisi.',
            'name.unique' => 'Nama area sudah digunakan.',
            'name.max' => 'Nama area maksimal 255 karakter.',
            'location.required' => 'Lokasi area wajib diisi.',
            'location.max' => 'Lokasi area maksimal 255 karakter.',
            'description.max' => 'Keterangan maksimal 1000 karakter.',
        ]);

        $validated['is_active'] = $request->has('is_active') ? (bool) $request->is_active : false;

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
}
