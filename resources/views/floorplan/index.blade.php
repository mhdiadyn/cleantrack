@extends('layouts.app')

@section('title', 'Denah Fasilitas Interaktif - CleanTrack')

@section('styles')
<!-- Pannellum 360 Viewer CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css"/>
<style>
    .blueprint-grid {
        background-color: #f8fafc;
        background-image: 
            linear-gradient(to right, rgba(220, 38, 38, 0.05) 1px, transparent 1px),
            linear-gradient(to bottom, rgba(220, 38, 38, 0.05) 1px, transparent 1px);
        background-size: 24px 24px;
    }
    .dark .blueprint-grid {
        background-color: #0f172a;
        background-image: 
            linear-gradient(to right, rgba(248, 113, 113, 0.07) 1px, transparent 1px),
            linear-gradient(to bottom, rgba(248, 113, 113, 0.07) 1px, transparent 1px);
        background-size: 24px 24px;
    }
    .pulse-ring {
        animation: pulse-animation 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
    @keyframes pulse-animation {
        0%, 100% { opacity: 0.8; transform: scale(1); }
        50% { opacity: 0.2; transform: scale(1.6); }
    }
    #panorama360Container {
        width: 100%;
        height: 480px;
        border-radius: 0.75rem;
        overflow: hidden;
    }
</style>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-red-700 via-red-600 to-rose-600 rounded-2xl p-6 sm:p-8 text-white shadow-xl shadow-red-500/10 relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute top-0 right-1/4 w-32 h-32 bg-rose-400/20 rounded-full blur-xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md text-xs font-semibold uppercase tracking-wider mb-3">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Live Facility Map
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white flex items-center gap-3">
                    <svg class="w-8 h-8 text-white/90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                    </svg>
                    Denah Fasilitas Interaktif
                </h1>
                <p class="text-red-100 text-sm mt-1 max-w-2xl">
                    Pantau status kebersihan setiap ruangan secara visual, lihat foto ruangan 360°, dan periksa riwayat monitoring secara langsung.
                </p>
            </div>

            <!-- Floor Selector & Actions -->
            <div class="flex flex-wrap items-center gap-3">
                <div class="bg-white/20 backdrop-blur-md p-1.5 rounded-xl flex items-center gap-1 border border-white/20">
                    @foreach($floors as $floorNum)
                        <a href="{{ route('floorplan.index', ['floor' => $floorNum]) }}" 
                           class="px-4 py-2 rounded-lg text-xs sm:text-sm font-semibold transition-all duration-200 {{ $currentFloor == $floorNum ? 'bg-white text-red-600 shadow-md scale-105' : 'text-white/90 hover:bg-white/10 hover:text-white' }}">
                            Lantai {{ $floorNum }}
                        </a>
                    @endforeach
                </div>
                <a href="{{ route('gallery.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white text-red-600 hover:bg-red-50 text-xs sm:text-sm font-bold rounded-xl shadow-md transition-all hover:scale-105">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Galeri & 360°
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Summary Row -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200 dark:border-slate-700 shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-950/50 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Bersih (Optimal)</p>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ $stats['bersih'] }} <span class="text-xs font-normal text-slate-400">ruang</span></h3>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200 dark:border-slate-700 shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-950/50 flex items-center justify-center text-amber-600 dark:text-amber-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Perlu Tindakan</p>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ $stats['perlu_tindakan'] }} <span class="text-xs font-normal text-slate-400">ruang</span></h3>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200 dark:border-slate-700 shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-red-100 dark:bg-red-950/50 flex items-center justify-center text-red-600 dark:text-red-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Kotor</p>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ $stats['kotor'] }} <span class="text-xs font-normal text-slate-400">ruang</span></h3>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-xl p-4 border border-slate-200 dark:border-slate-700 shadow-sm flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-600 dark:text-slate-300">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">Belum Dicek</p>
                <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ $stats['belum_dimonitor'] }} <span class="text-xs font-normal text-slate-400">ruang</span></h3>
            </div>
        </div>
    </div>

    <!-- Main Blueprint & Interactive Canvas Card -->
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
        <!-- Blueprint Toolbar -->
        <div class="p-4 sm:px-6 border-b border-slate-200 dark:border-slate-700 flex flex-wrap items-center justify-between gap-4 bg-slate-50/50 dark:bg-slate-800/80">
            <div class="flex items-center gap-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">Denah Gedung:</span>
                <span class="px-3 py-1 rounded-lg bg-red-100 dark:bg-red-950/60 text-red-700 dark:text-red-300 font-bold text-xs">
                    Lantai {{ $currentFloor }} ({{ $areas->count() }} Ruangan)
                </span>
            </div>

            <!-- Legend Indicator -->
            <div class="flex flex-wrap items-center gap-3 text-xs font-medium">
                <span class="flex items-center gap-1.5 text-slate-600 dark:text-slate-300">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 inline-block shadow-sm"></span> Bersih
                </span>
                <span class="flex items-center gap-1.5 text-slate-600 dark:text-slate-300">
                    <span class="w-3 h-3 rounded-full bg-amber-500 inline-block shadow-sm"></span> Perlu Tindakan
                </span>
                <span class="flex items-center gap-1.5 text-slate-600 dark:text-slate-300">
                    <span class="w-3 h-3 rounded-full bg-red-500 inline-block shadow-sm"></span> Kotor
                </span>
                <span class="flex items-center gap-1.5 text-slate-600 dark:text-slate-300">
                    <span class="w-3 h-3 rounded-full bg-slate-400 inline-block shadow-sm"></span> Belum Dicek
                </span>
                <span class="flex items-center gap-1.5 text-slate-600 dark:text-slate-300">
                    <span class="w-3 h-3 rounded-full bg-purple-500 inline-block shadow-sm"></span> Ada 360°
                </span>
            </div>
        </div>

        <!-- Interactive Blueprint Area -->
        <div class="relative w-full overflow-x-auto p-4 sm:p-6 blueprint-grid min-h-[520px] flex items-center justify-center">
            <!-- Architectural Background Map Mockup -->
            <div class="relative w-full max-w-5xl min-w-[760px] h-[480px] bg-white/90 dark:bg-slate-900/90 rounded-2xl border-2 border-dashed border-red-200 dark:border-red-950 p-6 shadow-inner backdrop-blur-sm">
                <!-- Architectural Grid Walls (Simulated floor layout) -->
                <div class="absolute inset-4 rounded-xl border-2 border-slate-300 dark:border-slate-700 pointer-events-none">
                    <!-- Corridor Center -->
                    <div class="absolute top-1/2 left-0 right-0 h-16 -translate-y-1/2 bg-slate-100/50 dark:bg-slate-800/50 border-y border-dashed border-slate-300 dark:border-slate-700 flex items-center justify-center">
                        <span class="text-[10px] font-mono tracking-widest text-slate-400 uppercase">KORIDOR UTAMA - LANTAI {{ $currentFloor }}</span>
                    </div>
                    <!-- Vertical dividers -->
                    <div class="absolute top-0 bottom-0 left-1/3 w-px bg-slate-200 dark:bg-slate-800"></div>
                    <div class="absolute top-0 bottom-0 left-2/3 w-px bg-slate-200 dark:bg-slate-800"></div>
                </div>

                <!-- Room Markers Generated from Database -->
                @forelse($areas as $area)
                    @php
                        $status = $area->current_status;
                        $colorClass = 'bg-slate-400 ring-slate-200 dark:ring-slate-700';
                        $badgeColor = 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300';
                        $pulseColor = 'bg-slate-400';

                        if ($status === 'bersih') {
                            $colorClass = 'bg-emerald-500 ring-emerald-200 dark:ring-emerald-900';
                            $badgeColor = 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/80 dark:text-emerald-300';
                            $pulseColor = 'bg-emerald-500';
                        } elseif ($status === 'perlu_tindakan') {
                            $colorClass = 'bg-amber-500 ring-amber-200 dark:ring-amber-900';
                            $badgeColor = 'bg-amber-100 text-amber-800 dark:bg-amber-950/80 dark:text-amber-300';
                            $pulseColor = 'bg-amber-500';
                        } elseif ($status === 'kotor') {
                            $colorClass = 'bg-red-500 ring-red-200 dark:ring-red-900';
                            $badgeColor = 'bg-red-100 text-red-800 dark:bg-red-950/80 dark:text-red-300';
                            $pulseColor = 'bg-red-500';
                        }
                    @endphp

                    <div class="absolute transform -translate-x-1/2 -translate-y-1/2 group cursor-pointer"
                         style="left: {{ $area->pos_x }}%; top: {{ $area->pos_y }}%;"
                         onclick="openAreaModal({{ json_encode($area) }})">
                        <!-- Pulse Ring for attention -->
                        <span class="absolute -inset-2 rounded-full {{ $pulseColor }} pulse-ring opacity-60"></span>
                        
                        <!-- Marker Icon / Card -->
                        <div class="relative z-10 flex items-center gap-2 bg-white dark:bg-slate-800 px-3 py-1.5 rounded-full shadow-lg border border-slate-200 dark:border-slate-700 group-hover:scale-110 transition-all duration-200 group-hover:border-red-500">
                            <span class="w-3.5 h-3.5 rounded-full {{ $colorClass }} ring-4 shadow-sm"></span>
                            <span class="text-xs font-bold text-slate-800 dark:text-white max-w-[120px] truncate">{{ $area->name }}</span>
                            @if($area->photo_360)
                                @if($area->is_standard)
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-black bg-sky-100 text-sky-700 dark:bg-sky-950 dark:text-sky-300 uppercase tracking-tight">HD</span>
                                @else
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-black bg-purple-100 text-purple-700 dark:bg-purple-950 dark:text-purple-300 uppercase tracking-tight">360°</span>
                                @endif
                            @endif
                        </div>

                        <!-- Tooltip on hover -->
                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block z-30 w-56 p-3 bg-slate-900/95 text-white text-xs rounded-xl shadow-xl backdrop-blur-sm pointer-events-none transition-all">
                            <p class="font-bold text-white text-sm mb-1">{{ $area->name }}</p>
                            <div class="flex items-center justify-between text-[11px] text-slate-300 border-b border-slate-700 pb-1 mb-1">
                                <span>Kode Ruang:</span>
                                <span class="font-mono text-red-300">{{ $area->code }}</span>
                            </div>
                            <div class="flex items-center justify-between text-[11px] text-slate-300 mb-1">
                                <span>Status Terakhir:</span>
                                <span class="font-semibold uppercase {{ $status === 'bersih' ? 'text-emerald-400' : ($status === 'kotor' ? 'text-red-400' : 'text-amber-400') }}">
                                    {{ $status ? str_replace('_', ' ', $status) : 'Belum dicek' }}
                                </span>
                            </div>
                            @if($area->latestMonitoring)
                                <p class="text-[10px] text-slate-400 mt-1">Petugas: {{ $area->latestMonitoring->user->name ?? '-' }} ({{ $area->latestMonitoring->check_date->diffForHumans() }})</p>
                            @endif
                            <p class="text-[10px] text-red-300 mt-2 font-medium">Klik untuk lihat detail & foto 360° &rarr;</p>
                        </div>
                    </div>
                @empty
                    <div class="h-full flex flex-col items-center justify-center text-center p-6">
                        <svg class="w-12 h-12 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <p class="text-slate-500 font-medium">Belum ada data ruangan di Lantai {{ $currentFloor }}.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Room List Cards Grid below Blueprint -->
        <div class="p-6 border-t border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800">
            <h3 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-4 flex items-center gap-2">
                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                </svg>
                Daftar Ruangan di Lantai {{ $currentFloor }}
            </h3>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($areas as $area)
                    @php
                        $status = $area->current_status;
                    @endphp
                    <div onclick="openAreaModal({{ json_encode($area) }})" 
                         class="p-4 rounded-xl border border-slate-200 dark:border-slate-700 hover:border-red-500 dark:hover:border-red-500 bg-slate-50/60 dark:bg-slate-800/60 hover:bg-white dark:hover:bg-slate-800 transition-all cursor-pointer shadow-sm group">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <span class="text-[10px] font-mono font-bold text-red-600 dark:text-red-400 uppercase tracking-wider">{{ $area->code }}</span>
                                <h4 class="font-bold text-slate-900 dark:text-white text-base group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors">{{ $area->name }}</h4>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold uppercase {{ $status === 'bersih' ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300' : ($status === 'kotor' ? 'bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-300' : ($status === 'perlu_tindakan' ? 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300')) }}">
                                {{ $status ? str_replace('_', ' ', $status) : 'Belum dicek' }}
                            </span>
                        </div>

                        <div class="mt-3 text-xs text-slate-500 dark:text-slate-400 flex items-center justify-between">
                            <span>Lantai {{ $area->floor }}</span>
                            @if($area->photo_360)
                                <span class="inline-flex items-center gap-1 text-purple-600 dark:text-purple-400 font-semibold">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                    Tersedia 360°
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Room Details Modal -->
<div id="roomModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-slate-800 rounded-2xl max-w-lg w-full border border-slate-200 dark:border-slate-700 shadow-2xl overflow-hidden transition-all transform animate-in fade-in zoom-in-95 duration-200">
        <!-- Modal Header -->
        <div class="p-5 bg-gradient-to-r from-red-600 to-rose-600 text-white flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-md flex items-center justify-center font-bold text-white text-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <h3 id="modalRoomName" class="font-bold text-lg text-white">Nama Ruangan</h3>
                    <p id="modalRoomCode" class="text-xs text-red-100 font-mono">CODE-001</p>
                </div>
            </div>
            <button onclick="closeAreaModal()" class="text-white/80 hover:text-white p-1 rounded-lg hover:bg-white/10 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 space-y-4">
            <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-700/50">
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400">Status Kebersihan:</span>
                <span id="modalStatusBadge" class="px-3 py-1 rounded-full text-xs font-bold uppercase">Status</span>
            </div>

            <div class="space-y-2 text-xs">
                <div class="flex items-center justify-between text-slate-600 dark:text-slate-300">
                    <span>Lokasi:</span>
                    <span id="modalFloor" class="font-semibold text-slate-900 dark:text-white">Lantai 1</span>
                </div>
                <div class="flex items-center justify-between text-slate-600 dark:text-slate-300">
                    <span>Pemeriksaan Terakhir:</span>
                    <span id="modalLastCheck" class="font-semibold text-slate-900 dark:text-white">-</span>
                </div>
                <div class="flex items-center justify-between text-slate-600 dark:text-slate-300">
                    <span>Petugas Kebersihan:</span>
                    <span id="modalOfficer" class="font-semibold text-slate-900 dark:text-white">-</span>
                </div>
            </div>

            <div id="modalNotesContainer" class="p-3 bg-amber-50 dark:bg-amber-950/40 rounded-xl border border-amber-200 dark:border-amber-900/50 hidden">
                <p class="text-xs font-bold text-amber-800 dark:text-amber-300 mb-1">Catatan Kebersihan:</p>
                <p id="modalNotes" class="text-xs text-amber-700 dark:text-amber-200">-</p>
            </div>

            <!-- 360 & Media Action Buttons -->
            <div class="pt-2 flex flex-col gap-2">
                <button id="btnOpen360" onclick="launch360FromModal()" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold rounded-xl shadow-md transition-all cursor-pointer">
                    <span id="btn360Icon">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    </span>
                    <span id="btn360Label">Buka Virtual Tour Foto 360° Ruangan</span>
                </button>

                <div class="grid grid-cols-2 gap-2">
                    <a id="btnNewMonitoring" href="{{ route('monitoring.create') }}" class="inline-flex items-center justify-center gap-1.5 px-3 py-2.5 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-xl transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Catat Monitoring
                    </a>
                    <a href="{{ route('gallery.index') }}" class="inline-flex items-center justify-center gap-1.5 px-3 py-2.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 text-xs font-bold rounded-xl transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Lihat Galeri Foto
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 360 Panorama Viewer Modal -->
<div id="panoramaModal" class="fixed inset-0 z-50 hidden bg-slate-950/90 backdrop-blur-md flex items-center justify-center p-4">
    <div id="panoramaModalWrapper" class="bg-white dark:bg-slate-900 rounded-2xl max-w-5xl w-full border border-slate-700 shadow-2xl overflow-hidden flex flex-col">
        <!-- 360 Header -->
        <div class="p-4 bg-slate-900 text-white flex items-center justify-between border-b border-slate-800">
            <div class="flex items-center gap-2.5">
                <span class="px-2 py-0.5 rounded bg-purple-600 text-white text-[10px] font-black uppercase">360° TOUR VIRTUAL</span>
                <h3 id="panoramaTitle" class="font-bold text-white text-base">Virtual 360° View</h3>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="toggle360Fullscreen()" class="p-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-colors" title="Layar Penuh (Fullscreen)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0-4l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                    </svg>
                </button>
                <button onclick="closePanoramaModal()" class="text-slate-400 hover:text-white p-2 rounded-lg hover:bg-slate-800 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- 360 Container -->
        <div class="p-4 bg-black">
            <div id="panorama360Container"></div>
            <p class="text-slate-400 text-[11px] text-center mt-2 flex items-center justify-center gap-2">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                </svg>
                Geser (drag) kursor untuk memutar pandangan 360°, scroll untuk memperbesar/memperkecil.
            </p>
        </div>
    </div>
</div>

<!-- Standard Room Photo Viewer Modal in Floorplan (High-Res, Proportional, Zoom & Pan) -->
<div id="standardPhotoModal" class="fixed inset-0 z-50 hidden bg-slate-950/95 backdrop-blur-md flex items-center justify-center p-3 sm:p-6" onclick="closeStandardViewer()">
    <div id="standardModalWrapper" class="bg-slate-900 border border-slate-800 rounded-2xl max-w-5xl w-full shadow-2xl overflow-hidden flex flex-col max-h-[92vh]" onclick="event.stopPropagation()">
        <!-- Header -->
        <div class="p-4 bg-slate-850 text-white flex items-center justify-between border-b border-slate-800 shrink-0">
            <div class="min-w-0 pr-3">
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded bg-sky-600 text-white text-[10px] font-black uppercase tracking-wider">FOTO RUANGAN STANDAR HD</span>
                    <span id="standardPhotoSubtitle" class="text-xs text-sky-300 font-mono"></span>
                </div>
                <h3 id="standardPhotoTitle" class="font-bold text-white text-base truncate mt-0.5">Nama Ruangan</h3>
            </div>
            <!-- Zoom Controls -->
            <div class="flex items-center gap-2 shrink-0">
                <div class="hidden sm:flex items-center bg-slate-800 rounded-xl p-1 border border-slate-700/80">
                    <button type="button" onclick="zoomStandardPhoto(-0.25)" class="p-1.5 text-slate-300 hover:text-white hover:bg-slate-700 rounded-lg transition-colors" title="Zoom Out (-)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                        </svg>
                    </button>
                    <span id="standardZoomLevel" class="px-2 text-xs font-mono font-bold text-slate-300">100%</span>
                    <button type="button" onclick="zoomStandardPhoto(0.25)" class="p-1.5 text-slate-300 hover:text-white hover:bg-slate-700 rounded-lg transition-colors" title="Zoom In (+)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </button>
                    <button type="button" onclick="resetStandardZoom()" class="px-2 py-1 text-[11px] font-semibold text-slate-400 hover:text-white hover:bg-slate-700 rounded-lg transition-colors">
                        Reset
                    </button>
                </div>
                <button type="button" onclick="toggleStandardFullscreen()" class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-colors" title="Layar Penuh (Fullscreen)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0-4l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                    </svg>
                </button>
                <button type="button" onclick="closeStandardViewer()" class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition-colors" title="Tutup">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Proportional Image Viewport -->
        <div class="relative flex-1 bg-black overflow-auto flex items-center justify-center p-4 min-h-[360px] max-h-[72vh] select-none cursor-grab active:cursor-grabbing">
            <img id="standardPhotoImage" src="" alt="Foto Ruangan" class="max-h-[68vh] w-auto max-w-full object-contain rounded-lg shadow-2xl transition-transform duration-150 ease-out origin-center">
        </div>

        <!-- Footer -->
        <div class="p-3 bg-slate-850 text-slate-400 text-xs flex items-center justify-between border-t border-slate-800 shrink-0">
            <span class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-sky-400"></span>
                <span>Rasio Asli Proporsional (Resolusi Penuh Tanpa Distorsi Bola)</span>
            </span>
            <span id="standardPhotoDimensions" class="font-mono text-slate-500 text-[11px]"></span>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Pannellum 360 JS -->
<script src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js"></script>

<script>
    let activeArea = null;
    let pannellumViewer = null;
    let currentStandardZoom = 1;

    function openAreaModal(area) {
        activeArea = area;
        document.getElementById('modalRoomName').innerText = area.name;
        document.getElementById('modalRoomCode').innerText = area.code;
        document.getElementById('modalFloor').innerText = 'Lantai ' + area.floor;

        const badge = document.getElementById('modalStatusBadge');
        const status = area.current_status || 'belum_dimonitor';
        
        if (status === 'bersih') {
            badge.className = 'px-3 py-1 rounded-full text-xs font-bold uppercase bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300';
            badge.innerText = 'Bersih';
        } else if (status === 'perlu_tindakan') {
            badge.className = 'px-3 py-1 rounded-full text-xs font-bold uppercase bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300';
            badge.innerText = 'Perlu Tindakan';
        } else if (status === 'kotor') {
            badge.className = 'px-3 py-1 rounded-full text-xs font-bold uppercase bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-300';
            badge.innerText = 'Kotor';
        } else {
            badge.className = 'px-3 py-1 rounded-full text-xs font-bold uppercase bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300';
            badge.innerText = 'Belum Dicek';
        }

        if (area.latest_monitoring) {
            const date = new Date(area.latest_monitoring.check_date);
            document.getElementById('modalLastCheck').innerText = date.toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' });
            document.getElementById('modalOfficer').innerText = area.latest_monitoring.user ? area.latest_monitoring.user.name : '-';
            
            if (area.latest_monitoring.notes) {
                document.getElementById('modalNotesContainer').classList.remove('hidden');
                document.getElementById('modalNotes').innerText = area.latest_monitoring.notes;
            } else {
                document.getElementById('modalNotesContainer').classList.add('hidden');
            }
        } else {
            document.getElementById('modalLastCheck').innerText = 'Belum pernah diperiksa';
            document.getElementById('modalOfficer').innerText = '-';
            document.getElementById('modalNotesContainer').classList.add('hidden');
        }

        const btn360 = document.getElementById('btnOpen360');
        const btnLabel = document.getElementById('btn360Label');
        const btnIcon = document.getElementById('btn360Icon');

        const hasPhoto = area.photo_360 || (area.latest_monitoring && area.latest_monitoring.photo_360);
        if (hasPhoto) {
            btn360.classList.remove('hidden');
            const isStandard = area.photo_type === 'standard' || (area.latest_monitoring && area.latest_monitoring.photo_360_type === 'standard');
            if (isStandard) {
                btn360.className = 'w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-r from-sky-600 to-blue-600 hover:from-sky-700 hover:to-blue-700 text-white font-bold rounded-xl shadow-md transition-all cursor-pointer';
                btnLabel.innerText = 'Lihat Foto Ruangan (Standar HD)';
                btnIcon.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" /></svg>`;
            } else {
                btn360.className = 'w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold rounded-xl shadow-md transition-all cursor-pointer';
                btnLabel.innerText = 'Buka Virtual Tour Foto 360° Ruangan';
                btnIcon.innerHTML = `<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>`;
            }
        } else {
            btn360.classList.add('hidden');
        }

        // Set monitoring link pre-selected area
        const btnNew = document.getElementById('btnNewMonitoring');
        btnNew.href = "{{ route('monitoring.create') }}?area_id=" + area.id;

        document.getElementById('roomModal').classList.remove('hidden');
    }

    function closeAreaModal() {
        document.getElementById('roomModal').classList.add('hidden');
    }

    function launch360FromModal() {
        if (!activeArea) return;
        
        let photoUrl = activeArea.photo_360;
        let isStandard = (activeArea.photo_type === 'standard');

        if (!photoUrl && activeArea.latest_monitoring && activeArea.latest_monitoring.photo_360) {
            photoUrl = '/storage/' + activeArea.latest_monitoring.photo_360;
            if (activeArea.latest_monitoring.photo_360_type === 'standard') {
                isStandard = true;
            }
        }

        // URL formatting
        if (photoUrl && photoUrl.startsWith('sample:')) {
            photoUrl = 'https://pannellum.org/images/alma.jpg';
        } else if (!photoUrl) {
            photoUrl = 'https://pannellum.org/images/alma.jpg';
        } else if (!photoUrl.startsWith('http') && !photoUrl.startsWith('/storage/')) {
            photoUrl = '/storage/' + photoUrl;
        }

        if (isStandard) {
            openStandardViewer(photoUrl, activeArea.name + ' (' + activeArea.code + ')', 'Lantai ' + activeArea.floor);
        } else {
            open360Viewer(photoUrl, activeArea.name + ' (' + activeArea.code + ')');
        }
    }

    function open360Viewer(imageUrl, title) {
        document.getElementById('panoramaTitle').innerText = title || 'Virtual 360° View';
        document.getElementById('panoramaModal').classList.remove('hidden');

        // Initialize or reload pannellum
        setTimeout(() => {
            if (pannellumViewer) {
                try { pannellumViewer.destroy(); } catch(e){}
            }
            pannellumViewer = pannellum.viewer('panorama360Container', {
                "type": "equirectangular",
                "panorama": imageUrl,
                "autoLoad": true,
                "autoRotate": -2,
                "compass": true
            });
        }, 120);
    }

    function closePanoramaModal() {
        document.getElementById('panoramaModal').classList.add('hidden');
        if (pannellumViewer) {
            try { pannellumViewer.destroy(); } catch(e){}
            pannellumViewer = null;
        }
    }

    function toggle360Fullscreen() {
        const modal = document.getElementById('panoramaModalWrapper');
        if (!document.fullscreenElement) {
            if (modal.requestFullscreen) modal.requestFullscreen();
            else if (modal.webkitRequestFullscreen) modal.webkitRequestFullscreen();
        } else {
            if (document.exitFullscreen) document.exitFullscreen();
        }
    }

    // --- STANDARD PHOTO VIEWER FUNCTIONS ---
    function openStandardViewer(imageUrl, title, subtitle) {
        if (!imageUrl) return;
        currentStandardZoom = 1;
        const img = document.getElementById('standardPhotoImage');
        img.src = imageUrl;
        img.style.transform = 'scale(1)';
        document.getElementById('standardZoomLevel').innerText = '100%';
        document.getElementById('standardPhotoTitle').innerText = title || 'Foto Ruangan';
        document.getElementById('standardPhotoSubtitle').innerText = subtitle || '';

        img.onload = function() {
            document.getElementById('standardPhotoDimensions').innerText = 
                'Resolusi Asli: ' + img.naturalWidth + ' × ' + img.naturalHeight + ' px (' + (img.naturalWidth / img.naturalHeight).toFixed(2) + ':1)';
        };

        document.getElementById('standardPhotoModal').classList.remove('hidden');
    }

    function closeStandardViewer() {
        document.getElementById('standardPhotoModal').classList.add('hidden');
        currentStandardZoom = 1;
        const img = document.getElementById('standardPhotoImage');
        if (img) img.style.transform = 'scale(1)';
    }

    function zoomStandardPhoto(delta) {
        currentStandardZoom = Math.min(Math.max(0.5, currentStandardZoom + delta), 3.0);
        const img = document.getElementById('standardPhotoImage');
        if (img) {
            img.style.transform = `scale(${currentStandardZoom})`;
            document.getElementById('standardZoomLevel').innerText = Math.round(currentStandardZoom * 100) + '%';
        }
    }

    function resetStandardZoom() {
        currentStandardZoom = 1;
        const img = document.getElementById('standardPhotoImage');
        if (img) {
            img.style.transform = 'scale(1)';
            document.getElementById('standardZoomLevel').innerText = '100%';
        }
    }

    function toggleStandardFullscreen() {
        const modal = document.getElementById('standardModalWrapper');
        if (!document.fullscreenElement) {
            if (modal.requestFullscreen) modal.requestFullscreen();
            else if (modal.webkitRequestFullscreen) modal.webkitRequestFullscreen();
        } else {
            if (document.exitFullscreen) document.exitFullscreen();
        }
    }
</script>
@endsection
