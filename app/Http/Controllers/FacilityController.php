<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\CleaningMonitoring;
use App\Models\FloorPlan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class FacilityController extends Controller
{
    private const FLOORS = [0, 1, 2, 3, 4];

    public function index(Request $request): View
    {
        $currentFloor = $request->integer('floor', 1);
        abort_unless(in_array($currentFloor, self::FLOORS, true), 404);

        $areas = Area::query()
            ->where('floor', $currentFloor)
            ->where('is_active', true)
            ->orderBy('name')
            ->select('areas.*')
            ->selectSub(
                CleaningMonitoring::query()
                    ->select('id')
                    ->whereColumn('area_id', 'areas.id')
                    ->orderByDesc('check_date')
                    ->orderByDesc('check_time')
                    ->orderByDesc('id')
                    ->limit(1),
                'latest_monitoring_id'
            )
            ->get();

        $latestMonitorings = CleaningMonitoring::query()
            ->with('user:id,name')
            ->whereIn('id', $areas->pluck('latest_monitoring_id')->filter())
            ->get()
            ->keyBy('id');

        $roomSlots = collect(config("facility_demo.floors.{$currentFloor}", []));
        $unusedAreas = $areas->keyBy('id');
        $exactAreasBySlot = $roomSlots->map(
            fn (array $slot) => $areas->firstWhere('name', $slot['name'])
        );
        $diagramRooms = [];
        $roomDetails = [];

        foreach ($roomSlots as $slotIndex => $slot) {
            $area = $exactAreasBySlot->get($slotIndex);

            if ($area) {
                $unusedAreas->forget($area->id);
            }

            $monitoring = $area
                ? $latestMonitorings->get($area->latest_monitoring_id)
                : null;
            $status = $this->roomStatus($monitoring);
            $roomKey = $area ? 'area-'.$area->id : "demo-{$currentFloor}-{$slotIndex}";
            $roomName = $area?->name ?? $slot['name'];
            $roomDetails[$roomKey] = [
                'name' => $roomName,
                'code' => $area?->code ?? 'DEMO-L'.$currentFloor.'-'.str_pad((string) ($slotIndex + 1), 2, '0', STR_PAD_LEFT),
                'floor' => config('facility_demo.floor_labels.'.$currentFloor),
                'category' => $area ? 'Belum ditentukan' : $slot['category'],
                'description' => $area
                    ? ($area->description ?: 'Deskripsi ruangan belum tersedia.')
                    : $slot['description'],
                'status' => $status['key'],
                'status_label' => $status['label'],
                'last_checked' => $monitoring
                    ? $monitoring->check_date->format('d/m/Y').' '.substr((string) $monitoring->check_time, 0, 5)
                    : 'Belum ada pemeriksaan',
                'officer' => $monitoring?->user?->name ?? 'Belum tersedia',
                'photos' => $this->roomPhotos($area, $monitoring),
                'is_demo' => ! $area,
            ];

            $diagramRooms[] = [
                ...$slot,
                'key' => $roomKey,
                'name' => $roomName,
                'status' => $status['key'],
                'status_label' => $status['label'],
                'is_demo' => ! $area,
            ];
        }

        foreach ($unusedAreas as $area) {
            $extraIndex = count($diagramRooms) - $roomSlots->count();
            $row = intdiv($extraIndex, 4);
            $column = $extraIndex % 4;
            $monitoring = $latestMonitorings->get($area->latest_monitoring_id);
            $status = $this->roomStatus($monitoring);
            $roomKey = 'area-'.$area->id;
            $roomDetails[$roomKey] = [
                'name' => $area->name,
                'code' => $area->code,
                'floor' => config('facility_demo.floor_labels.'.$currentFloor),
                'category' => 'Belum ditentukan',
                'description' => $area->description ?: 'Deskripsi ruangan belum tersedia.',
                'status' => $status['key'],
                'status_label' => $status['label'],
                'last_checked' => $monitoring
                    ? $monitoring->check_date->format('d/m/Y').' '.substr((string) $monitoring->check_time, 0, 5)
                    : 'Belum ada pemeriksaan',
                'officer' => $monitoring?->user?->name ?? 'Belum tersedia',
                'photos' => $this->roomPhotos($area, $monitoring),
                'is_demo' => false,
            ];

            $diagramRooms[] = [
                'x' => 28 + ($column * 242),
                'y' => 620 + ($row * 142),
                'width' => 222,
                'height' => 122,
                'key' => $roomKey,
                'name' => $area->name,
                'status' => $status['key'],
                'status_label' => $status['label'],
                'is_demo' => false,
            ];
        }

        $diagramHeight = count($unusedAreas) > 0
            ? 620 + (intdiv(count($unusedAreas) - 1, 4) + 1) * 142 + 28
            : 600;

        $selectedAreaId = $request->integer('area_id');
        if (! $areas->contains('id', $selectedAreaId)) {
            $selectedAreaId = null;
        }

        $monitorings = CleaningMonitoring::query()
            ->with('area')
            ->whereIn('area_id', $areas->modelKeys())
            ->when($selectedAreaId, fn ($query) => $query->where('area_id', $selectedAreaId))
            ->where(function ($query) {
                $query->whereNotNull('photo_before')
                    ->orWhereNotNull('photo_after')
                    ->orWhereNotNull('room_photo');
            })
            ->latest('check_date')
            ->latest('check_time')
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $floorPlan = FloorPlan::query()->where('floor', $currentFloor)->first();

        return view('gallery.index', [
            'areas' => $areas,
            'currentFloor' => $currentFloor,
            'diagramHeight' => $diagramHeight,
            'diagramRooms' => $diagramRooms,
            'floors' => self::FLOORS,
            'floorLabels' => config('facility_demo.floor_labels'),
            'currentFloorLabel' => config('facility_demo.floor_labels.'.$currentFloor),
            'floorPlan' => $floorPlan,
            'monitorings' => $monitorings,
            'roomDetails' => $roomDetails,
            'selectedAreaId' => $selectedAreaId,
        ]);
    }

    /**
     * @return array{key: string, label: string}
     */
    private function roomStatus(?CleaningMonitoring $monitoring): array
    {
        if (! $monitoring || $monitoring->status === 'Belum Diperiksa') {
            return ['key' => 'uninspected', 'label' => 'Belum diperiksa'];
        }

        if ($monitoring->status === 'Sudah Dibersihkan' || $monitoring->cleanliness_condition === 'Bersih') {
            return ['key' => 'clean', 'label' => 'Bersih'];
        }

        return ['key' => 'needs-cleaning', 'label' => 'Perlu dibersihkan'];
    }

    /**
     * @return list<array{url: string, label: string}>
     */
    private function roomPhotos(?Area $area, ?CleaningMonitoring $monitoring): array
    {
        $photos = [];

        if ($area?->photo_url) {
            $photos[] = ['url' => $area->photo_url, 'label' => 'Foto ruangan'];
        }

        if ($monitoring) {
            foreach ([
                ['url' => $monitoring->photo_before_url, 'label' => 'Foto sebelum'],
                ['url' => $monitoring->photo_after_url, 'label' => 'Foto sesudah'],
                ['url' => $monitoring->room_photo_url, 'label' => 'Foto ruangan monitoring'],
            ] as $photo) {
                if ($photo['url']) {
                    $photos[] = $photo;
                }
            }
        }

        return $photos;
    }

    public function storeFloorPlan(Request $request, int $floor): RedirectResponse
    {
        abort_unless(in_array($floor, self::FLOORS, true), 404);

        $validated = $request->validate([
            'floor_plan' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:20480'],
        ], [
            'floor_plan.required' => 'Pilih file denah terlebih dahulu.',
            'floor_plan.mimes' => 'Denah harus berupa JPG, JPEG, PNG, atau PDF.',
            'floor_plan.max' => 'Ukuran denah maksimal 20 MB.',
        ]);

        $path = $validated['floor_plan']->store('floor-plans', 'public');

        if (! $path) {
            return back()
                ->withErrors(['floor_plan' => 'Denah gagal disimpan. Silakan coba lagi.'])
                ->withInput();
        }

        $floorPlan = FloorPlan::query()->firstOrNew(['floor' => $floor]);
        $previousPath = $floorPlan->file_path;
        $floorPlan->file_path = $path;
        $floorPlan->save();

        if ($previousPath && $previousPath !== $path && Storage::disk('public')->exists($previousPath)) {
            Storage::disk('public')->delete($previousPath);
        }

        return redirect()
            ->route('facility.index', ['floor' => $floor])
            ->with('success', 'Denah '.$this->floorLabel($floor).' berhasil disimpan.');
    }

    private function floorLabel(int $floor): string
    {
        return config('facility_demo.floor_labels.'.$floor, 'Lantai '.$floor);
    }
}
