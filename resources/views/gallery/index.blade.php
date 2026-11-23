@extends('layouts.app')

@section('title', 'Denah & Galeri Ruangan - CleanTrack')

@section('content')
<div class="space-y-6">
    <header class="card-clean border-l-4 border-l-crimson p-6 sm:p-8">
        <div class="flex flex-col gap-3">
            <h1 class="flex items-center gap-3 text-2xl font-semibold tracking-tight text-neutral-900 dark:text-white sm:text-3xl">
                <i class="fa-solid fa-map-location-dot text-crimson" aria-hidden="true"></i>
                Denah & Galeri Ruangan
            </h1>
            <p class="max-w-3xl text-sm text-neutral-600 dark:text-neutral-300">
                Denah interaktif ini merupakan ilustrasi DEMO/SEMENTARA. Nama, status, dan foto yang terhubung ke Area mengikuti data CLEANTRACK; posisi ilustrasi bukan denah aktual gedung.
            </p>
        </div>
    </header>

    @if (session('success'))
        <div role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900 dark:bg-emerald-950/40 dark:text-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div role="alert" class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-900 dark:bg-red-950/40 dark:text-red-200">
            <ul class="list-inside list-disc space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <nav aria-label="Pilih lantai" class="card-clean flex flex-wrap gap-2 p-4">
        @foreach ($floors as $floor)
            <a href="{{ route('facility.index', ['floor' => $floor]) }}"
               @class([
                   'rounded-lg px-4 py-2 text-sm font-semibold transition-colors',
                   'bg-crimson text-white' => $currentFloor === $floor,
                   'bg-neutral-100 text-neutral-700 hover:bg-neutral-200 dark:bg-neutral-800 dark:text-neutral-200 dark:hover:bg-neutral-700' => $currentFloor !== $floor,
               ])>
                Lantai {{ $floor }}
            </a>
        @endforeach
    </nav>

    <section class="card-clean overflow-hidden" aria-labelledby="interactive-floor-plan-heading">
        <div class="flex flex-col gap-4 border-b border-border-clean p-5 sm:px-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 id="interactive-floor-plan-heading" class="text-lg font-semibold text-neutral-900 dark:text-white">
                            Denah Fasilitas Interaktif — Lantai {{ $currentFloor }}
                        </h2>
                        <span class="rounded-full border border-amber-300 bg-amber-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-amber-800 dark:border-amber-700 dark:bg-amber-950/40 dark:text-amber-200">DEMO / SEMENTARA</span>
                    </div>
                    <p class="mt-1 max-w-3xl text-sm text-neutral-500 dark:text-neutral-400">
                        Ilustrasi tata letak, bukan denah aktual. Ruangan berlabel DEMO belum terhubung ke data operasional. Klik ruangan untuk melihat detail.
                    </p>
                </div>
                <div class="flex shrink-0 items-center gap-2">
                    <button type="button" class="btn-secondary text-xs" onclick="changeFloorPlanZoom(-0.2)" aria-label="Perkecil denah">−</button>
                    <span id="floorMapZoomLevel" class="min-w-12 text-center text-xs font-medium text-neutral-600 dark:text-neutral-300">100%</span>
                    <button type="button" class="btn-secondary text-xs" onclick="changeFloorPlanZoom(0.2)" aria-label="Perbesar denah">+</button>
                    <button type="button" class="btn-secondary text-xs" onclick="resetFloorPlanZoom()">Reset</button>
                </div>
            </div>

            <div class="flex flex-wrap gap-x-5 gap-y-2 text-xs text-neutral-600 dark:text-neutral-300" aria-label="Legenda status kebersihan">
                <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-sm bg-emerald-500"></span>Bersih</span>
                <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-sm bg-orange-500"></span>Perlu dibersihkan</span>
                <span class="inline-flex items-center gap-2"><span class="h-3 w-3 rounded-sm bg-neutral-400"></span>Belum diperiksa / DEMO</span>
            </div>
        </div>

        <div class="overflow-auto bg-neutral-50 p-3 dark:bg-neutral-950 sm:p-5">
            <svg id="interactiveFloorPlan" viewBox="0 0 1000 {{ $diagramHeight }}" role="img"
                 aria-label="Denah ilustrasi interaktif Lantai {{ $currentFloor }}"
                 class="mx-auto h-auto w-full min-w-[760px] transition-[width] duration-150">
                <rect x="14" y="14" width="972" height="{{ $diagramHeight - 28 }}" rx="14"
                      class="fill-white stroke-neutral-500 dark:fill-neutral-900 dark:stroke-neutral-500" stroke-width="8" />
                <rect x="28" y="258" width="944" height="100" rx="8"
                      class="fill-neutral-100 dark:fill-neutral-800" />
                <path d="M28 258H972 M28 358H972" class="stroke-neutral-400 dark:stroke-neutral-600" stroke-width="5" stroke-dasharray="14 8" />
                <text x="500" y="300" text-anchor="middle" class="fill-neutral-500 dark:fill-neutral-300" font-size="18" font-weight="700">JALUR SIRKULASI · DEMO</text>

                @foreach ($diagramRooms as $room)
                    @php
                        $roomColorClasses = match ($room['status']) {
                            'clean' => 'fill-emerald-100 stroke-emerald-600 dark:fill-emerald-950 dark:stroke-emerald-400',
                            'needs-cleaning' => 'fill-orange-100 stroke-orange-600 dark:fill-orange-950 dark:stroke-orange-400',
                            default => 'fill-neutral-100 stroke-neutral-500 dark:fill-neutral-800 dark:stroke-neutral-400',
                        };
                    @endphp
                    <g class="facility-room cursor-pointer outline-none" data-room-key="{{ $room['key'] }}"
                       role="button" tabindex="0"
                       aria-label="Pilih {{ $room['name'] }}. Status: {{ $room['status_label'] }}{{ $room['is_demo'] ? '. Data DEMO' : '' }}">
                        <title>{{ $room['name'] }} — {{ $room['status_label'] }}{{ $room['is_demo'] ? ' · DEMO / SEMENTARA' : '' }}</title>
                        <rect x="{{ $room['x'] }}" y="{{ $room['y'] }}" width="{{ $room['width'] }}" height="{{ $room['height'] }}"
                              rx="8" stroke-width="4" class="{{ $roomColorClasses }} transition-[fill,stroke] hover:brightness-95" />
                        <foreignObject x="{{ $room['x'] + 8 }}" y="{{ $room['y'] + 8 }}" width="{{ $room['width'] - 16 }}" height="{{ $room['height'] - 16 }}">
                            <div xmlns="http://www.w3.org/1999/xhtml" class="flex h-full flex-col items-center justify-center gap-2 overflow-hidden rounded-md px-2 text-center">
                                @if ($room['is_demo'])
                                    <span class="rounded-full bg-amber-100 px-2 py-0.5 text-[9px] font-bold uppercase tracking-wide text-amber-900 dark:bg-amber-900 dark:text-amber-100">DEMO</span>
                                @endif
                                <span class="line-clamp-2 text-sm font-bold text-neutral-900 dark:text-white sm:text-base">{{ $room['name'] }}</span>
                                <span class="text-[10px] font-semibold uppercase tracking-wide text-neutral-700 dark:text-neutral-200">{{ $room['status_label'] }}</span>
                            </div>
                        </foreignObject>
                    </g>
                @endforeach
            </svg>
        </div>
        <p class="border-t border-border-clean px-5 py-3 text-xs text-neutral-500 dark:text-neutral-400">
            Ruangan ilustrasi berlabel DEMO belum terhubung ke Area. Area operasional yang tidak cocok dengan nama contoh ditampilkan terpisah di bagian bawah diagram, dengan status dari data monitoring sebenarnya.
        </p>
    </section>

    <section class="card-clean overflow-hidden" aria-labelledby="floor-plan-heading">
        <div class="flex flex-col gap-4 border-b border-border-clean p-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>
                <h2 id="floor-plan-heading" class="text-lg font-semibold text-neutral-900 dark:text-white">Dokumen Denah Referensi — Lantai {{ $currentFloor }}</h2>
                <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Dokumen yang diunggah Admin. Denah interaktif di atas masih berupa ilustrasi DEMO.</p>
            </div>

            @if (auth()->user()->role === 'admin')
                <form action="{{ route('admin.floor-plans.store', ['floor' => $currentFloor]) }}" method="POST" enctype="multipart/form-data" class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    @csrf
                    <label class="sr-only" for="floor_plan">Pilih file denah Lantai {{ $currentFloor }}</label>
                    <input id="floor_plan" name="floor_plan" type="file" accept=".jpg,.jpeg,.png,.pdf" required
                           class="block w-full max-w-sm text-xs text-neutral-600 file:mr-3 file:rounded-lg file:border-0 file:bg-neutral-100 file:px-3 file:py-2 file:text-xs file:font-semibold dark:text-neutral-300 dark:file:bg-neutral-800">
                    <button type="submit" class="btn-primary shrink-0">
                        {{ $floorPlan ? 'Ganti Denah' : 'Unggah Denah' }}
                    </button>
                </form>
            @endif
        </div>

        @if ($floorPlan)
            <div class="space-y-4 p-4 sm:p-6">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <p class="text-xs text-neutral-500 dark:text-neutral-400">
                        {{ $floorPlan->is_pdf ? 'Dokumen PDF' : 'Gambar denah' }} · Lantai {{ $currentFloor }}
                    </p>
                    @unless ($floorPlan->is_pdf)
                        <div class="flex items-center gap-2">
                            <button type="button" class="btn-secondary text-xs" onclick="changeFloorPlanZoom(-0.2)" aria-label="Perkecil denah">−</button>
                            <span id="floorPlanZoomLevel" class="min-w-12 text-center text-xs font-medium text-neutral-600 dark:text-neutral-300">100%</span>
                            <button type="button" class="btn-secondary text-xs" onclick="changeFloorPlanZoom(0.2)" aria-label="Perbesar denah">+</button>
                            <button type="button" class="btn-secondary text-xs" onclick="resetFloorPlanZoom()">Reset</button>
                        </div>
                    @endunless
                </div>

                @if ($floorPlan->is_pdf)
                    <div class="overflow-hidden rounded-xl border border-border-clean bg-neutral-100 dark:bg-neutral-900">
                        <iframe src="{{ $floorPlan->file_url }}" title="Pratinjau denah Lantai {{ $currentFloor }}" class="h-[65vh] min-h-96 w-full"></iframe>
                    </div>
                    <a href="{{ $floorPlan->file_url }}" target="_blank" rel="noopener" class="text-sm font-semibold text-crimson hover:underline">Buka dokumen denah di tab baru</a>
                @else
                    <div class="max-h-[70vh] overflow-auto rounded-xl border border-border-clean bg-neutral-100 p-3 text-center dark:bg-neutral-900 sm:p-6">
                        <img id="floorPlanImage" src="{{ $floorPlan->file_url }}" alt="Denah Fasilitas Lantai {{ $currentFloor }}"
                             class="mx-auto h-auto max-h-[65vh] max-w-full origin-center object-contain transition-transform duration-150">
                    </div>
                @endif
            </div>
        @else
            <div class="flex flex-col items-center px-6 py-14 text-center">
                <span class="mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-neutral-100 text-neutral-500 dark:bg-neutral-800 dark:text-neutral-300">
                    <i class="fa-regular fa-map text-xl" aria-hidden="true"></i>
                </span>
                <h3 class="font-semibold text-neutral-900 dark:text-white">Denah Lantai {{ $currentFloor }} belum tersedia</h3>
                <p class="mt-2 max-w-lg text-sm text-neutral-500 dark:text-neutral-400">
                    Belum ada dokumen denah untuk lantai ini. Admin dapat mengunggah denah resmi dalam format JPG, JPEG, PNG, atau PDF.
                </p>
            </div>
        @endif
    </section>

    <section class="card-clean overflow-hidden" aria-labelledby="room-gallery-heading">
        <div class="border-b border-border-clean p-5 sm:px-6">
            <h2 id="room-gallery-heading" class="text-lg font-semibold text-neutral-900 dark:text-white">Galeri Ruangan — Lantai {{ $currentFloor }}</h2>
            <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Foto dikelompokkan berdasarkan ruangan pada lantai yang dipilih.</p>
        </div>

        @if ($areas->isNotEmpty())
            <div class="grid grid-cols-1 gap-4 p-4 sm:grid-cols-2 lg:grid-cols-3 sm:p-6">
                @foreach ($areas as $area)
                    <article class="overflow-hidden rounded-xl border border-border-clean bg-white dark:bg-neutral-900">
                        <div class="flex min-h-48 items-center justify-center bg-neutral-100 p-3 dark:bg-neutral-800">
                            @if ($area->photo_url)
                                <button type="button" class="h-48 w-full cursor-zoom-in" data-image-url="{{ $area->photo_url }}"
                                        data-image-caption="{{ $area->name }} — Lantai {{ $currentFloor }}" onclick="openRoomPhotoPreview(this)">
                                    <img src="{{ $area->photo_url }}" alt="Foto {{ $area->name }}" class="h-full w-full object-contain">
                                </button>
                            @else
                                <div class="px-4 text-center text-sm text-neutral-500 dark:text-neutral-400">
                                    <i class="fa-regular fa-image mb-2 block text-2xl" aria-hidden="true"></i>
                                    Belum ada foto ruangan.
                                </div>
                            @endif
                        </div>
                        <div class="space-y-3 p-4">
                            <div>
                                <h3 class="font-semibold text-neutral-900 dark:text-white">{{ $area->name }}</h3>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">{{ $area->location }}</p>
                            </div>

                            @if (auth()->user()->role === 'admin')
                                <form action="{{ route('admin.areas.photo.store', $area) }}" method="POST" enctype="multipart/form-data" class="space-y-2">
                                    @csrf
                                    <label class="block text-xs font-medium text-neutral-600 dark:text-neutral-300" for="room-photo-{{ $area->id }}">
                                        {{ $area->photo_url ? 'Ganti foto ruangan' : 'Unggah foto ruangan' }}
                                    </label>
                                    <input id="room-photo-{{ $area->id }}" name="photo" type="file" accept=".jpg,.jpeg,.png" required
                                           class="block w-full text-xs text-neutral-600 file:mr-2 file:rounded-lg file:border-0 file:bg-neutral-100 file:px-3 file:py-2 file:text-xs dark:text-neutral-300 dark:file:bg-neutral-800">
                                    @error('photo')
                                        <p class="text-xs text-red-700 dark:text-red-300">{{ $message }}</p>
                                    @enderror
                                    <button type="submit" class="btn-secondary w-full justify-center text-xs">Simpan Foto</button>
                                </form>

                                @if ($area->photo)
                                    <form action="{{ route('admin.areas.photo.destroy', $area) }}" method="POST"
                                          onsubmit="return confirm('Hapus foto ruangan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full rounded-lg border border-red-200 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-50 dark:border-red-900 dark:text-red-300 dark:hover:bg-red-950/40">
                                            Hapus Foto
                                        </button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="px-6 py-12 text-center text-sm text-neutral-500 dark:text-neutral-400">
                Belum ada data ruangan untuk Lantai {{ $currentFloor }}. Area yang ditambahkan ke lantai ini akan muncul di sini.
            </div>
        @endif
    </section>

    <section class="card-clean overflow-hidden" aria-labelledby="monitoring-photos-heading">
        <div class="flex flex-col gap-3 border-b border-border-clean p-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>
                <h2 id="monitoring-photos-heading" class="text-lg font-semibold text-neutral-900 dark:text-white">Dokumentasi Foto Monitoring</h2>
                <p class="mt-1 text-sm text-neutral-500 dark:text-neutral-400">Foto sebelum dan sesudah tetap mengikuti catatan monitoring ruangan.</p>
            </div>
            <form method="GET" action="{{ route('facility.index') }}" class="flex items-center gap-2">
                <input type="hidden" name="floor" value="{{ $currentFloor }}">
                <label class="sr-only" for="gallery-area">Filter ruangan</label>
                <select id="gallery-area" name="area_id" class="input-clean text-xs" onchange="this.form.submit()">
                    <option value="">Semua ruangan</option>
                    @foreach ($areas as $area)
                        <option value="{{ $area->id }}" {{ $selectedAreaId === $area->id ? 'selected' : '' }}>{{ $area->name }}</option>
                    @endforeach
                </select>
                <noscript><button type="submit" class="btn-secondary text-xs">Filter</button></noscript>
            </form>
        </div>

        @if ($monitorings->isNotEmpty())
            <div class="grid grid-cols-1 gap-4 p-4 sm:grid-cols-2 xl:grid-cols-3 sm:p-6">
                @foreach ($monitorings as $monitoring)
                    <article class="rounded-xl border border-border-clean bg-white p-4 dark:bg-neutral-900">
                        <div class="mb-3">
                            <h3 class="font-semibold text-neutral-900 dark:text-white">{{ $monitoring->display_area_name }}</h3>
                            <p class="text-xs text-neutral-500 dark:text-neutral-400">
                                Lantai {{ $currentFloor }} · {{ \Carbon\Carbon::parse($monitoring->check_date)->format('d/m/Y') }}
                            </p>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            @foreach ([
                                ['url' => $monitoring->photo_before_url, 'label' => 'Foto Sebelum'],
                                ['url' => $monitoring->photo_after_url, 'label' => 'Foto Sesudah'],
                                ['url' => $monitoring->room_photo_url, 'label' => 'Foto Ruangan'],
                            ] as $photo)
                                @if ($photo['url'])
                                    <button type="button" class="overflow-hidden rounded-lg border border-border-clean bg-neutral-100 p-1 text-left dark:bg-neutral-800"
                                            data-image-url="{{ $photo['url'] }}" data-image-caption="{{ $photo['label'] }} — {{ $monitoring->display_area_name }}"
                                            onclick="openRoomPhotoPreview(this)">
                                        <img src="{{ $photo['url'] }}" alt="{{ $photo['label'] }} — {{ $monitoring->display_area_name }}"
                                             class="h-28 w-full object-contain">
                                        <span class="block px-1 py-1 text-[11px] font-medium text-neutral-600 dark:text-neutral-300">{{ $photo['label'] }}</span>
                                    </button>
                                @endif
                            @endforeach
                        </div>
                    </article>
                @endforeach
            </div>
            <div class="border-t border-border-clean p-4">
                {{ $monitorings->links() }}
            </div>
        @else
            <div class="px-6 py-12 text-center text-sm text-neutral-500 dark:text-neutral-400">
                Belum ada dokumentasi foto monitoring untuk ruangan di Lantai {{ $currentFloor }}.
            </div>
        @endif
    </section>
</div>

    <div id="facilityRoomDialog" class="fixed inset-0 z-[90] hidden items-center justify-center bg-black/70 p-3 sm:p-6"
         role="dialog" aria-modal="true" aria-labelledby="facilityRoomName" onclick="closeFacilityRoom()">
        <section class="relative max-h-[92vh] w-full max-w-3xl overflow-y-auto rounded-2xl border border-border-clean bg-white p-5 shadow-2xl dark:bg-neutral-900 sm:p-7"
                 onclick="event.stopPropagation()">
            <button type="button" class="absolute right-4 top-4 rounded-lg px-3 py-2 text-sm font-semibold text-neutral-600 hover:bg-neutral-100 dark:text-neutral-300 dark:hover:bg-neutral-800"
                    onclick="closeFacilityRoom()" aria-label="Tutup detail ruangan">Tutup</button>
            <div class="space-y-5 pr-14">
                <div class="flex flex-wrap items-center gap-2">
                    <h2 id="facilityRoomName" class="text-xl font-bold text-neutral-900 dark:text-white"></h2>
                    <span id="facilityRoomDemoBadge" class="hidden rounded-full border border-amber-300 bg-amber-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-amber-800 dark:border-amber-700 dark:bg-amber-950/40 dark:text-amber-200">DEMO / SEMENTARA</span>
                </div>
                <dl class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div class="rounded-xl bg-neutral-50 p-3 dark:bg-neutral-800"><dt class="text-xs text-neutral-500 dark:text-neutral-400">Identitas</dt><dd id="facilityRoomCode" class="mt-1 font-semibold text-neutral-900 dark:text-white"></dd></div>
                    <div class="rounded-xl bg-neutral-50 p-3 dark:bg-neutral-800"><dt class="text-xs text-neutral-500 dark:text-neutral-400">Lantai</dt><dd id="facilityRoomFloor" class="mt-1 font-semibold text-neutral-900 dark:text-white"></dd></div>
                    <div class="rounded-xl bg-neutral-50 p-3 dark:bg-neutral-800"><dt class="text-xs text-neutral-500 dark:text-neutral-400">Kategori</dt><dd id="facilityRoomCategory" class="mt-1 font-semibold text-neutral-900 dark:text-white"></dd></div>
                    <div class="rounded-xl bg-neutral-50 p-3 dark:bg-neutral-800"><dt class="text-xs text-neutral-500 dark:text-neutral-400">Status kebersihan</dt><dd id="facilityRoomStatus" class="mt-1 font-semibold"></dd></div>
                    <div class="rounded-xl bg-neutral-50 p-3 dark:bg-neutral-800"><dt class="text-xs text-neutral-500 dark:text-neutral-400">Pemeriksaan terakhir</dt><dd id="facilityRoomLastChecked" class="mt-1 font-semibold text-neutral-900 dark:text-white"></dd></div>
                    <div class="rounded-xl bg-neutral-50 p-3 dark:bg-neutral-800"><dt class="text-xs text-neutral-500 dark:text-neutral-400">Petugas</dt><dd id="facilityRoomOfficer" class="mt-1 font-semibold text-neutral-900 dark:text-white"></dd></div>
                </dl>
                <div>
                    <h3 class="text-sm font-semibold text-neutral-900 dark:text-white">Deskripsi</h3>
                    <p id="facilityRoomDescription" class="mt-1 whitespace-pre-line text-sm text-neutral-600 dark:text-neutral-300"></p>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-neutral-900 dark:text-white">Dokumentasi Foto Ruangan</h3>
                    <div id="facilityRoomPhotos" class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2"></div>
                    <p id="facilityRoomNoPhotos" class="mt-2 hidden text-sm text-neutral-500 dark:text-neutral-400">Belum ada foto dokumentasi untuk ruangan ini.</p>
                </div>
            </div>
        </section>
    </div>

<div id="roomPhotoPreview" class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/90 p-4" role="dialog" aria-modal="true" aria-label="Pratinjau foto" onclick="closeRoomPhotoPreview()">
    <div class="relative flex max-h-full max-w-6xl flex-col items-center gap-3" onclick="event.stopPropagation()">
        <button type="button" class="self-end rounded-lg bg-white/10 px-3 py-2 text-sm font-semibold text-white hover:bg-white/20" onclick="closeRoomPhotoPreview()">Tutup</button>
        <img id="roomPhotoPreviewImage" src="" alt="" class="max-h-[78vh] max-w-full object-contain">
        <p id="roomPhotoPreviewCaption" class="text-center text-sm text-white"></p>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let floorPlanZoom = 1;
    const facilityRoomData = {{ Illuminate\Support\Js::from($roomDetails) }};

    function changeFloorPlanZoom(change) {
        floorPlanZoom = Math.min(3, Math.max(0.5, floorPlanZoom + change));
        const svg = document.getElementById('interactiveFloorPlan');
        const svgZoom = document.getElementById('floorMapZoomLevel');
        if (svg) {
            svg.style.width = `${100 * floorPlanZoom}%`;
            svg.style.minWidth = `${760 * floorPlanZoom}px`;
        }
        if (svgZoom) svgZoom.textContent = `${Math.round(floorPlanZoom * 100)}%`;

        const image = document.getElementById('floorPlanImage');
        const imageZoom = document.getElementById('floorPlanZoomLevel');
        if (image) image.style.transform = `scale(${floorPlanZoom})`;
        if (imageZoom) imageZoom.textContent = `${Math.round(floorPlanZoom * 100)}%`;
    }

    function resetFloorPlanZoom() {
        floorPlanZoom = 1;
        const svg = document.getElementById('interactiveFloorPlan');
        if (svg) {
            svg.style.width = '100%';
            svg.style.minWidth = '760px';
        }
        const svgZoom = document.getElementById('floorMapZoomLevel');
        if (svgZoom) svgZoom.textContent = '100%';

        const image = document.getElementById('floorPlanImage');
        if (image) image.style.transform = 'scale(1)';
        const zoomLevel = document.getElementById('floorPlanZoomLevel');
        if (zoomLevel) zoomLevel.textContent = '100%';
    }

    function openFacilityRoom(roomKey) {
        const room = facilityRoomData[roomKey];
        if (!room) return;

        document.getElementById('facilityRoomName').textContent = room.name;
        document.getElementById('facilityRoomCode').textContent = room.code;
        document.getElementById('facilityRoomFloor').textContent = `Lantai ${room.floor}`;
        document.getElementById('facilityRoomCategory').textContent = room.category;
        document.getElementById('facilityRoomDescription').textContent = room.description;
        document.getElementById('facilityRoomLastChecked').textContent = room.last_checked;
        document.getElementById('facilityRoomOfficer').textContent = room.officer;

        const status = document.getElementById('facilityRoomStatus');
        status.textContent = room.status_label;
        status.className = 'mt-1 font-semibold ' + ({
            clean: 'text-emerald-700 dark:text-emerald-300',
            'needs-cleaning': 'text-orange-700 dark:text-orange-300',
            uninspected: 'text-neutral-600 dark:text-neutral-300',
        }[room.status] || 'text-neutral-600 dark:text-neutral-300');

        document.getElementById('facilityRoomDemoBadge').classList.toggle('hidden', !room.is_demo);

        const photos = document.getElementById('facilityRoomPhotos');
        photos.replaceChildren();
        room.photos.forEach((photo) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'overflow-hidden rounded-xl border border-border-clean bg-neutral-50 text-left dark:bg-neutral-800';
            button.dataset.imageUrl = photo.url;
            button.dataset.imageCaption = `${photo.label} — ${room.name}`;
            button.onclick = () => openRoomPhotoPreview(button);

            const image = document.createElement('img');
            image.src = photo.url;
            image.alt = `${photo.label} — ${room.name}`;
            image.className = 'h-40 w-full object-contain';

            const caption = document.createElement('span');
            caption.className = 'block px-3 py-2 text-xs font-medium text-neutral-700 dark:text-neutral-200';
            caption.textContent = photo.label;

            button.append(image, caption);
            photos.append(button);
        });
        document.getElementById('facilityRoomNoPhotos').classList.toggle('hidden', room.photos.length > 0);

        const dialog = document.getElementById('facilityRoomDialog');
        dialog.classList.remove('hidden');
        dialog.classList.add('flex');
    }

    function closeFacilityRoom() {
        const dialog = document.getElementById('facilityRoomDialog');
        dialog.classList.add('hidden');
        dialog.classList.remove('flex');
    }

    function openRoomPhotoPreview(button) {
        const modal = document.getElementById('roomPhotoPreview');
        const image = document.getElementById('roomPhotoPreviewImage');
        image.src = button.dataset.imageUrl;
        image.alt = button.dataset.imageCaption;
        document.getElementById('roomPhotoPreviewCaption').textContent = button.dataset.imageCaption;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeRoomPhotoPreview() {
        const modal = document.getElementById('roomPhotoPreview');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.getElementById('roomPhotoPreviewImage').src = '';
    }

    document.querySelectorAll('.facility-room').forEach((room) => {
        room.addEventListener('click', () => openFacilityRoom(room.dataset.roomKey));
        room.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                openFacilityRoom(room.dataset.roomKey);
            }
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeRoomPhotoPreview();
            closeFacilityRoom();
        }
    });
</script>
@endsection
