@extends('layouts.app')

@section('title', 'Galeri Keseluruhan Ruangan & 360° - CleanTrack')

@section('styles')
<!-- Pannellum 360 Viewer CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css"/>
<style>
    #galleryPanoramaViewer {
        width: 100%;
        height: 520px;
        border-radius: 1rem;
        overflow: hidden;
    }
    .comparison-container {
        position: relative;
        overflow: hidden;
        user-select: none;
    }
    .comparison-slider {
        position: absolute;
        top: 0;
        bottom: 0;
        width: 3px;
        background: #ffffff;
        cursor: ew-resize;
        box-shadow: 0 0 10px rgba(0,0,0,0.5);
    }
</style>
@endsection

@section('content')
<div class="space-y-6">
    @if ($errors->has('photo_360'))
        <div role="alert" class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800 dark:border-red-900 dark:bg-red-950/40 dark:text-red-200">
            {{ $errors->first('photo_360') }}
        </div>
    @endif

    <!-- Header Section -->
    <div class="card-clean p-6 sm:p-8 border-l-4 border-l-crimson relative">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-status-info-bg text-status-info text-xs font-semibold uppercase tracking-wider mb-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-status-info animate-pulse"></span>
                    Visual Facility Showcase
                </div>
                <h1 class="text-2xl sm:text-3xl font-semibold font-heading text-neutral-900 tracking-tight flex items-center gap-3">
                    <svg class="w-7 h-7 text-crimson" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Galeri Keseluruhan Ruangan & 360°
                </h1>
                <p class="text-neutral-500 text-sm mt-1 max-w-2xl font-sans">
                    Koleksi dokumentasi foto fasilitas, inspeksi sebelum vs sesudah pembersihan, dan tur visual 360° interaktif di seluruh area gedung.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('floorplan.index') }}" class="btn-secondary text-xs sm:text-sm flex items-center gap-2">
                    <svg class="w-4 h-4 text-crimson" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                    </svg>
                    Buka Denah Gedung
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Counter Bar -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="card-clean p-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-crimson-surface text-crimson flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <span class="text-xs text-neutral-500 font-medium">Dokumentasi</span>
                    <h4 class="text-lg font-semibold font-heading text-neutral-900">{{ $totalPhotos }} Foto</h4>
                </div>
            </div>
        </div>

        <div class="card-clean p-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <span class="text-xs text-neutral-500 font-medium">Panorama 360°</span>
                    <h4 class="text-lg font-semibold font-heading text-neutral-900">{{ $total360 }} Area</h4>
                </div>
            </div>
        </div>

        <div class="card-clean p-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-status-info-bg text-status-info flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div>
                    <span class="text-xs text-neutral-500 font-medium">Foto Standar HD</span>
                    <h4 class="text-lg font-semibold font-heading text-neutral-900">{{ $totalStandard }} Area</h4>
                </div>
            </div>
        </div>

        <div class="card-clean p-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-status-success-bg text-status-success flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                </div>
                <div>
                    <span class="text-xs text-neutral-500 font-medium">Sebelum / Sesudah</span>
                    <h4 class="text-lg font-semibold font-heading text-neutral-900">{{ $totalComparisons }} Sesi</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Category Nav -->
    <div class="card-clean p-4">
        <form method="GET" action="{{ route('gallery.index') }}" class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <!-- Tabs / Types -->
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('gallery.index', array_merge(request()->query(), ['type' => 'all'])) }}" 
                   class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all {{ $selectedType === 'all' ? 'bg-crimson text-white shadow-xs' : 'bg-neutral-100 text-neutral-700 hover:bg-neutral-200' }}">
                    Semua Foto
                </a>
                <a href="{{ route('gallery.index', array_merge(request()->query(), ['type' => '360'])) }}" 
                   class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all flex items-center gap-1.5 {{ $selectedType === '360' ? 'bg-purple-700 text-white shadow-xs' : 'bg-purple-50 text-purple-700 hover:bg-purple-100' }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                    </svg>
                    360° Virtual Tour
                </a>
                <a href="{{ route('gallery.index', array_merge(request()->query(), ['type' => 'standard'])) }}" 
                   class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all flex items-center gap-1.5 {{ $selectedType === 'standard' ? 'bg-sky-700 text-white shadow-xs' : 'bg-sky-50 text-sky-700 hover:bg-sky-100' }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Foto Ruangan Standar
                </a>
                <a href="{{ route('gallery.index', array_merge(request()->query(), ['type' => 'comparison'])) }}" 
                   class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all flex items-center gap-1.5 {{ $selectedType === 'comparison' ? 'bg-emerald-700 text-white shadow-xs' : 'bg-emerald-50 text-emerald-800 hover:bg-emerald-100' }}">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                    Sebelum vs Sesudah
                </a>
            </div>

            <!-- Area & Search Dropdowns -->
            <div class="flex flex-wrap items-center gap-2.5">
                <select name="area_id" onchange="this.form.submit()" class="input-clean text-xs py-1.5 px-3">
                    <option value="">Semua Ruangan</option>
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}" {{ $selectedAreaId == $area->id ? 'selected' : '' }}>
                            {{ $area->code }} - {{ $area->name }} (Lt {{ $area->floor }})
                        </option>
                    @endforeach
                </select>

                <select name="status" onchange="this.form.submit()" class="input-clean text-xs py-1.5 px-3">
                    <option value="">Semua Status</option>
                    <option value="bersih" {{ $selectedStatus === 'bersih' ? 'selected' : '' }}>Bersih</option>
                    <option value="perlu_tindakan" {{ $selectedStatus === 'perlu_tindakan' ? 'selected' : '' }}>Perlu Tindakan</option>
                    <option value="kotor" {{ $selectedStatus === 'kotor' ? 'selected' : '' }}>Kotor</option>
                </select>

                <input type="hidden" name="type" value="{{ $selectedType }}">
            </div>
        </form>
    </div>

    <!-- Dedicated Room Showcase Area (Panorama 360 & Foto Standar) -->
    @if(($selectedType === '360' || $selectedType === 'standard' || $selectedType === 'all') && $areasWith360->count() > 0)
        <div class="bg-neutral-900 rounded-2xl p-6 text-white shadow-xs border border-neutral-800">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-5">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 rounded-md bg-white/10 border border-white/20 text-white text-xs font-black uppercase tracking-wider">Facility Visual Showcase</span>
                    <h3 class="font-semibold font-heading text-lg text-white">Dokumentasi Visual & Tur Virtual Ruangan</h3>
                </div>
                <div class="flex items-center gap-3">
                    @if(auth()->user()->role === 'admin')
                        <button type="button" onclick="openUpload360Modal()" class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-crimson hover:bg-crimson-dark text-white text-xs font-semibold shadow-xs transition-all cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                            </svg>
                            Upload / Ganti Foto Ruangan
                        </button>
                    @endif
                    <span class="text-xs text-neutral-400 font-medium hidden md:inline">Klik foto untuk melihat dalam ukuran penuh / interaktif</span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($areasWith360 as $area)
                    @php
                        $isStandard = $area->is_standard;
                        $borderColor = $isStandard ? 'border-sky-500/30 hover:border-sky-400' : 'border-purple-500/30 hover:border-purple-400';
                        $badgeBg = $isStandard ? 'bg-sky-600/90' : 'bg-purple-600/90';
                        $subBadgeBg = $isStandard ? 'bg-sky-900/90' : 'bg-purple-900/90';
                        $subBadgeText = $isStandard ? 'Buka Foto HD' : 'Buka Panorama 360°';
                        $tagColor = $isStandard ? 'text-sky-300' : 'text-purple-300';
                    @endphp
                    <div class="group relative bg-slate-800/90 rounded-xl overflow-hidden border {{ $borderColor }} transition-all hover:scale-[1.02] shadow-lg flex flex-col justify-between">
                        @if($isStandard)
                            <!-- Standard Room Photo Card -->
                            <div onclick="openStandardViewer('{{ $area->photo_360_url }}', '{{ addslashes($area->name) }} ({{ $area->code }})', 'Lantai {{ $area->floor }} - {{ addslashes($area->location) }}')" 
                                 class="h-40 bg-slate-800 relative overflow-hidden flex items-center justify-center cursor-pointer">
                                <img src="{{ $area->photo_360_url }}" alt="{{ $area->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500 opacity-80"
                                     onerror="this.src='https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=600&q=80'">
                                <div class="absolute inset-0 bg-neutral-950/40"></div>
                                
                                <div class="absolute inset-0 flex flex-col items-center justify-center gap-2">
                                    <span class="w-11 h-11 rounded-full {{ $badgeBg }} text-white flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </span>
                                    <span class="text-[10px] font-bold text-white {{ $subBadgeBg }} px-2.5 py-0.5 rounded-full">{{ $subBadgeText }}</span>
                                </div>
                            </div>
                        @else
                            <!-- 360 Panorama Card -->
                            <div onclick="open360Viewer('{{ $area->photo_360_url }}', '{{ addslashes($area->name) }} ({{ $area->code }})')" 
                                 class="h-40 bg-slate-800 relative overflow-hidden flex items-center justify-center cursor-pointer">
                                <img src="{{ $area->photo_360_url }}" alt="{{ $area->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500 opacity-70"
                                     onerror="this.src='https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=600&q=80'">
                                <div class="absolute inset-0 bg-neutral-950/40"></div>
                                
                                <div class="absolute inset-0 flex flex-col items-center justify-center gap-2">
                                    <span class="w-11 h-11 rounded-full {{ $badgeBg }} text-white flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                        </svg>
                                    </span>
                                    <span class="text-[10px] font-bold text-white {{ $subBadgeBg }} px-2.5 py-0.5 rounded-full backdrop-blur-sm">{{ $subBadgeText }}</span>
                                </div>
                            </div>
                        @endif

                        <div class="p-3 bg-slate-850 flex items-center justify-between gap-2">
                            <div class="min-w-0 flex-1">
                                <span class="text-[10px] font-mono {{ $tagColor }} font-bold uppercase">{{ $area->code }} • Lt {{ $area->floor }} • {{ $isStandard ? 'Foto Biasa' : '360° Tour' }}</span>
                                <h4 class="font-bold text-white text-xs truncate">{{ $area->name }}</h4>
                            </div>
                            @if(auth()->user()->role === 'admin')
                                <button type="button" 
                                        onclick="openEditArea360Modal('{{ $area->id }}', '{{ addslashes($area->name) }}', '{{ $area->photo_360_url }}', '{{ $area->photo_type ?? 'panorama_360' }}')" 
                                        class="p-1.5 rounded-lg bg-red-600/80 hover:bg-red-600 text-white transition-colors cursor-pointer shrink-0" 
                                        title="Ganti Foto Ruangan / 360 Ini">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Main Media Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($monitorings as $monitoring)
            @php
                $statusColor = 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300';
                if ($monitoring->status === 'kotor') {
                    $statusColor = 'bg-red-100 text-red-800 dark:bg-red-950 dark:text-red-300';
                } elseif ($monitoring->status === 'perlu_tindakan') {
                    $statusColor = 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300';
                }
            @endphp
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden flex flex-col group hover:border-red-400 transition-all">
                <!-- Header Info -->
                <div class="p-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between bg-slate-50/50 dark:bg-slate-800/80">
                    <div>
                        <span class="text-[10px] font-mono font-bold text-red-600 dark:text-red-400 uppercase tracking-wider">{{ $monitoring->area->code ?? 'AREA' }}</span>
                        <h4 class="font-bold text-slate-900 dark:text-white text-sm">{{ $monitoring->area->name ?? '-' }}</h4>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase {{ $statusColor }}">
                        {{ str_replace('_', ' ', $monitoring->status) }}
                    </span>
                </div>

                <!-- Photos Container (Before / After / 360) -->
                <div class="p-4 space-y-3 flex-1 flex flex-col justify-between">
                    @if($monitoring->photo_before && $monitoring->photo_after)
                        <!-- Side by Side Before & After -->
                        <div>
                            <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400 mb-2 block uppercase tracking-wide">Perbandingan Kebersihan:</span>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="relative rounded-xl overflow-hidden cursor-pointer group/img" onclick="openLightbox('{{ $monitoring->photo_before_url }}', 'Sebelum: {{ $monitoring->area->name }}')">
                                    <img src="{{ $monitoring->photo_before_url }}" alt="Sebelum" class="w-full h-32 object-cover group-hover/img:scale-105 transition-transform">
                                    <span class="absolute bottom-1.5 left-1.5 px-2 py-0.5 bg-red-600/90 text-white font-bold text-[9px] rounded-md backdrop-blur-sm">Sebelum</span>
                                </div>
                                <div class="relative rounded-xl overflow-hidden cursor-pointer group/img" onclick="openLightbox('{{ $monitoring->photo_after_url }}', 'Sesudah: {{ $monitoring->area->name }}')">
                                    <img src="{{ $monitoring->photo_after_url }}" alt="Sesudah" class="w-full h-32 object-cover group-hover/img:scale-105 transition-transform">
                                    <span class="absolute bottom-1.5 left-1.5 px-2 py-0.5 bg-emerald-600/90 text-white font-bold text-[9px] rounded-md backdrop-blur-sm">Sesudah</span>
                                </div>
                            </div>
                        </div>
                    @elseif($monitoring->photo_before || $monitoring->photo_after)
                        <!-- Single Photo -->
                        <div class="relative rounded-xl overflow-hidden cursor-pointer group/img" onclick="openLightbox('{{ $monitoring->photo_after_url ?: $monitoring->photo_before_url }}', '{{ $monitoring->area->name }}')">
                            <img src="{{ $monitoring->photo_after_url ?: $monitoring->photo_before_url }}" alt="Dokumentasi" class="w-full h-44 object-cover group-hover/img:scale-105 transition-transform">
                            <span class="absolute bottom-2 left-2 px-2.5 py-1 bg-slate-900/80 text-white font-bold text-[10px] rounded-lg backdrop-blur-sm">
                                {{ $monitoring->photo_after ? 'Dokumentasi Sesudah' : 'Dokumentasi Sebelum' }}
                            </span>
                        </div>
                    @endif

                    @if($monitoring->photo_360)
                        @if($monitoring->is_standard)
                            <!-- Standard Room Photo in this session -->
                            <button onclick="openStandardViewer('{{ $monitoring->photo_360_url }}', '{{ addslashes($monitoring->area->name ?? 'Ruangan') }} ({{ $monitoring->check_date->format('d M Y') }})', '{{ $monitoring->area->code ?? '' }} • Sesi Monitoring')" 
                                    class="w-full py-2.5 px-3 bg-sky-50 dark:bg-sky-950/40 hover:bg-sky-100 border border-sky-200 dark:border-sky-800 text-sky-700 dark:text-sky-300 text-xs font-bold rounded-xl flex items-center justify-center gap-2 transition-colors cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Buka Foto Ruangan (Standar HD)
                            </button>
                        @else
                            <!-- 360 Panorama Available in this session -->
                            <button onclick="open360Viewer('{{ $monitoring->photo_360_url }}', '{{ addslashes($monitoring->area->name ?? 'Ruangan') }} ({{ $monitoring->check_date->format('d M Y') }})')" 
                                    class="w-full py-2.5 px-3 bg-purple-50 dark:bg-purple-950/40 hover:bg-purple-100 border border-purple-200 dark:border-purple-800 text-purple-700 dark:text-purple-300 text-xs font-bold rounded-xl flex items-center justify-center gap-2 transition-colors cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                Buka Foto 360° Ruangan Ini
                            </button>
                        @endif
                    @endif

                    <!-- Notes -->
                    @if($monitoring->notes)
                        <p class="text-xs text-slate-600 dark:text-slate-300 line-clamp-2 bg-slate-50 dark:bg-slate-700/50 p-2.5 rounded-lg italic">
                            "{{ $monitoring->notes }}"
                        </p>
                    @endif
                </div>

                <!-- Footer Info -->
                <div class="px-4 py-3 bg-slate-50 dark:bg-slate-800/80 border-t border-slate-100 dark:border-slate-700 text-[11px] text-slate-500 dark:text-slate-400 flex items-center justify-between">
                    <span>Petugas: <strong class="text-slate-700 dark:text-slate-200">{{ $monitoring->user->name ?? '-' }}</strong></span>
                    <span>{{ $monitoring->check_date->format('d M Y') }} {{ substr($monitoring->check_time, 0, 5) }} WIB</span>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 flex flex-col items-center justify-center text-center bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700">
                <svg class="w-16 h-16 text-slate-300 dark:text-slate-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <h4 class="text-base font-bold text-slate-800 dark:text-white mb-1">Belum Ada Foto Dokumentasi</h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mb-4">
                    Belum ada foto yang sesuai dengan filter yang Anda pilih.
                </p>
                <a href="{{ route('gallery.index') }}" class="px-4 py-2 bg-red-600 text-white text-xs font-bold rounded-xl shadow-md">
                    Reset Filter
                </a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $monitorings->links() }}
    </div>
</div>

<!-- 360 Panorama Viewer Modal -->
<div id="galleryPanoramaModal" class="fixed inset-0 z-50 hidden bg-slate-950/90 backdrop-blur-md flex items-center justify-center p-4">
    <div id="panoramaModalWrapper" class="bg-white dark:bg-slate-900 rounded-2xl max-w-5xl w-full border border-slate-700 shadow-2xl overflow-hidden flex flex-col">
        <!-- 360 Header -->
        <div class="p-4 bg-slate-900 text-white flex items-center justify-between border-b border-slate-800">
            <div class="flex items-center gap-2.5">
                <span class="px-2 py-0.5 rounded bg-purple-600 text-white text-[10px] font-black uppercase tracking-wider">360° TOUR VIRTUAL</span>
                <h3 id="galleryPanoramaTitle" class="font-bold text-white text-base truncate max-w-md">Virtual 360° View</h3>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="toggle360Fullscreen()" class="p-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-colors" title="Layar Penuh (Fullscreen)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0-4l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                    </svg>
                </button>
                <button type="button" onclick="close360Viewer()" class="text-slate-400 hover:text-white p-2 rounded-lg hover:bg-slate-800 transition-colors" title="Tutup">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- 360 Container -->
        <div class="p-4 bg-black">
            <div id="galleryPanoramaViewer"></div>
            <div class="flex items-center justify-between text-slate-400 text-[11px] mt-2 px-1">
                <span class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                    </svg>
                    Gunakan kursor / sentuhan untuk memutar 360° secara interaktif.
                </span>
                <span class="text-purple-300 text-[10px] font-mono font-bold">Proyeksi Equirectangular Murni</span>
            </div>
        </div>
    </div>
</div>

<!-- Standard Room Photo Viewer Modal (High-Res, Proportional, Zoom & Pan) -->
<div id="standardPhotoModal" class="fixed inset-0 z-50 hidden bg-slate-950/95 backdrop-blur-md flex items-center justify-center p-3 sm:p-6" onclick="closeStandardViewer()">
    <div id="standardModalWrapper" class="bg-slate-900 border border-slate-800 rounded-2xl max-w-5xl w-full shadow-2xl overflow-hidden flex flex-col max-h-[92vh]" onclick="event.stopPropagation()">
        <!-- Standard Photo Header -->
        <div class="p-4 bg-slate-850 text-white flex items-center justify-between border-b border-slate-800 shrink-0">
            <div class="min-w-0 pr-3">
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded bg-sky-600 text-white text-[10px] font-black uppercase tracking-wider">FOTO RUANGAN STANDAR HD</span>
                    <span id="standardPhotoSubtitle" class="text-xs text-sky-300 font-mono"></span>
                </div>
                <h3 id="standardPhotoTitle" class="font-bold text-white text-base truncate mt-0.5">Nama Ruangan</h3>
            </div>
            <!-- Zoom & Action Controls -->
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
                    <button type="button" onclick="resetStandardZoom()" class="px-2 py-1 text-[11px] font-semibold text-slate-400 hover:text-white hover:bg-slate-700 rounded-lg transition-colors" title="Reset Zoom">
                        Reset
                    </button>
                </div>
                <button type="button" onclick="toggleStandardFullscreen()" class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-colors" title="Layar Penuh (Fullscreen)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-5h-4m4 0v4m0-4l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                    </svg>
                </button>
                <a id="standardDownloadBtn" href="" download target="_blank" class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-colors" title="Unduh Foto Resolusi Asli">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                </a>
                <button type="button" onclick="closeStandardViewer()" class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition-colors" title="Tutup">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Proportional Image Container -->
        <div class="relative flex-1 bg-black overflow-auto flex items-center justify-center p-4 min-h-[360px] max-h-[72vh] select-none cursor-grab active:cursor-grabbing" id="standardPhotoViewport">
            <img id="standardPhotoImage" src="" alt="Foto Ruangan" class="max-h-[68vh] w-auto max-w-full object-contain rounded-lg shadow-2xl transition-transform duration-150 ease-out origin-center">
        </div>

        <!-- Standard Photo Footer Info -->
        <div class="p-3 bg-slate-850 text-slate-400 text-xs flex flex-col sm:flex-row items-center justify-between gap-2 border-t border-slate-800 shrink-0">
            <span class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-sky-400"></span>
                <span>Rasio Asli Proporsional (Bebas dari efek lengkung / gepeng)</span>
            </span>
            <span id="standardPhotoDimensions" class="font-mono text-slate-500 text-[11px]"></span>
        </div>
    </div>
</div>

<!-- Image Lightbox Modal -->
<div id="lightboxModal" class="fixed inset-0 z-50 hidden bg-slate-950/90 backdrop-blur-md flex items-center justify-center p-4" onclick="closeLightbox()">
    <div class="relative max-w-4xl w-full max-h-[90vh] flex flex-col items-center" onclick="event.stopPropagation()">
        <button onclick="closeLightbox()" class="absolute -top-10 right-0 text-white/80 hover:text-white p-2">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        <img id="lightboxImage" src="" alt="Preview" class="max-h-[80vh] w-auto rounded-xl shadow-2xl object-contain border border-slate-700">
        <p id="lightboxCaption" class="text-white text-sm font-semibold mt-3 bg-slate-900/80 px-4 py-1.5 rounded-full"></p>
    </div>
</div>

@if(auth()->user()->role === 'admin')
<!-- Edit / Upload Foto Modal (Mendukung Panorama 360 & Foto Standar) -->
<div id="edit360Modal" class="fixed inset-0 z-50 hidden bg-neutral-900/60 backdrop-blur-xs flex items-center justify-center p-4" onclick="closeEdit360Modal()">
    <div class="card-clean max-w-xl w-full p-6 space-y-4 max-h-[95vh] overflow-y-auto" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between border-b border-border-clean pb-3">
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-xl bg-crimson-surface text-crimson flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold font-heading text-neutral-900 text-base">Upload & Ganti Foto Ruangan</h3>
                    <p id="edit360AreaName" class="text-xs text-neutral-500">Pilih jenis foto yang tepat agar tampilan tidak melengkung atau gepeng</p>
                </div>
            </div>
            <button onclick="closeEdit360Modal()" class="text-neutral-400 hover:text-neutral-600 p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form id="edit360Form" method="POST" action="{{ route('admin.areas.upload-360-general') }}" enctype="multipart/form-data" onsubmit="return validate360Form()" class="space-y-4">
            @csrf

            <!-- Area Selector (Khusus modal general) -->
            <div id="areaSelectorContainer" class="hidden">
                <label class="label-clean mb-1.5">
                    Pilih Ruangan / Area <span class="text-crimson">*</span>
                </label>
                <select id="modalAreaSelect" name="area_id" class="input-clean text-sm w-full">
                    <option value="">-- Pilih Ruangan --</option>
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}">{{ $area->code }} - {{ $area->name }} (Lt {{ $area->floor }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Pilihan Jenis Foto (Radio Cards) -->
            <div>
                <label class="label-clean mb-1.5">
                    Pilih Jenis Foto yang Diunggah <span class="text-crimson">*</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <!-- Opsi 1: Panorama 360 -->
                    <label id="cardOption360" class="relative flex flex-col p-3.5 rounded-xl border-2 cursor-pointer transition-all border-purple-500 bg-purple-50/50">
                        <div class="flex items-center justify-between mb-1.5">
                            <div class="flex items-center gap-2">
                                <span class="w-7 h-7 rounded-lg bg-purple-600 text-white flex items-center justify-center text-xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                </span>
                                <span class="font-semibold text-xs text-neutral-900">Panorama 360°</span>
                            </div>
                            <input type="radio" name="photo_type" value="panorama_360" checked onchange="handlePhotoTypeChange(this.value)" class="text-purple-600 focus:ring-purple-500">
                        </div>
                        <p class="text-[11px] text-neutral-600 leading-snug">
                            Foto panorama penuh 360° dari kamera 360 (rasio ideal 2:1). Ditampilkan di viewer bola 360°.
                        </p>
                    </label>

                    <!-- Opsi 2: Foto Standar / Biasa -->
                    <label id="cardOptionStandard" class="relative flex flex-col p-3.5 rounded-xl border-2 cursor-pointer transition-all border-border-clean bg-neutral-50 hover:border-sky-400">
                        <div class="flex items-center justify-between mb-1.5">
                            <div class="flex items-center gap-2">
                                <span class="w-7 h-7 rounded-lg bg-sky-600 text-white flex items-center justify-center text-xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </span>
                                <span class="font-semibold text-xs text-neutral-900">Foto Biasa (Standar)</span>
                            </div>
                            <input type="radio" name="photo_type" value="standard" onchange="handlePhotoTypeChange(this.value)" class="text-sky-600 focus:ring-sky-500">
                        </div>
                        <p class="text-[11px] text-neutral-600 leading-snug">
                            Foto ruangan biasa dari kamera HP / DSLR. Ditampilkan proporsional tanpa distorsi melengkung.
                        </p>
                    </label>
                </div>
            </div>

            <!-- Upload File Input -->
            <div>
                <label class="label-clean mb-1.5">
                    Pilih File Foto Baru <span class="text-crimson">*</span>
                </label>
                <div class="border-2 border-dashed border-border-clean hover:border-crimson rounded-xl p-5 text-center transition-colors cursor-pointer bg-neutral-50" onclick="document.getElementById('modal_photo_360').click()">
                    <svg class="w-10 h-10 text-crimson mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <p id="fileLabel360" class="text-sm font-semibold text-neutral-800">Klik untuk memilih file foto (JPG, PNG, WebP)</p>
                    <p id="fileSubHint360" class="text-xs text-neutral-400 mt-1">Resolusi tinggi dipertahankan (Maksimal 50 MB)</p>
                </div>
                <input type="file" id="modal_photo_360" name="photo_360" accept="image/*" class="hidden" required onchange="handleFileSelected(this)">
            </div>

            <!-- Aspect Ratio Detection & Recommendation Alert (Dynamic JS) -->
            <div id="aspectRatioWarningBox" class="hidden rounded-xl border p-3 text-xs flex items-start gap-3">
                <span id="warningIcon" class="text-base shrink-0">⚠️</span>
                <div class="flex-1">
                    <p id="warningText" class="font-medium text-neutral-800 leading-relaxed"></p>
                    <button type="button" id="btnQuickSwitchType" class="mt-2 inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-bold text-white shadow-xs cursor-pointer"></button>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="closeEdit360Modal()" class="btn-secondary text-xs py-2 px-4">
                    Batal
                </button>
                <button type="submit" class="btn-primary text-xs py-2 px-5">
                    Simpan & Update Foto
                </button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection

@section('scripts')
<!-- Pannellum 360 JS -->
<script src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js"></script>

<script>
    let galleryPannellum = null;
    let currentStandardZoom = 1;
    let selectedImageDimensions = null;

    // --- 360 VIEWER FUNCTIONS ---
    function open360Viewer(imageUrl, title) {
        if (!imageUrl || imageUrl.startsWith('sample:')) {
            imageUrl = 'https://pannellum.org/images/alma.jpg';
        }
        document.getElementById('galleryPanoramaTitle').innerText = title || 'Virtual 360° View';
        document.getElementById('galleryPanoramaModal').classList.remove('hidden');

        setTimeout(() => {
            if (galleryPannellum) {
                try { galleryPannellum.destroy(); } catch(e){}
            }
            galleryPannellum = pannellum.viewer('galleryPanoramaViewer', {
                "type": "equirectangular",
                "panorama": imageUrl,
                "autoLoad": true,
                "autoRotate": -2,
                "compass": true
            });
        }, 120);
    }

    function close360Viewer() {
        document.getElementById('galleryPanoramaModal').classList.add('hidden');
        if (galleryPannellum) {
            try { galleryPannellum.destroy(); } catch(e){}
            galleryPannellum = null;
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

    // --- STANDARD PHOTO VIEWER FUNCTIONS (PROPORTIONAL, HIGH RES) ---
    function openStandardViewer(imageUrl, title, subtitle) {
        if (!imageUrl) return;
        currentStandardZoom = 1;
        const img = document.getElementById('standardPhotoImage');
        img.src = imageUrl;
        img.style.transform = 'scale(1)';
        document.getElementById('standardZoomLevel').innerText = '100%';
        document.getElementById('standardPhotoTitle').innerText = title || 'Foto Ruangan';
        document.getElementById('standardPhotoSubtitle').innerText = subtitle || '';
        document.getElementById('standardDownloadBtn').href = imageUrl;

        // Measure natural dimensions
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

    // --- LIGHTBOX FALLBACK ---
    function openLightbox(imageUrl, caption) {
        document.getElementById('lightboxImage').src = imageUrl;
        document.getElementById('lightboxCaption').innerText = caption || '';
        document.getElementById('lightboxModal').classList.remove('hidden');
    }

    function closeLightbox() {
        document.getElementById('lightboxModal').classList.add('hidden');
    }

    // --- EDIT / UPLOAD MODAL & ASPECT RATIO DETECTION ---
    function openEditArea360Modal(areaId, areaName, currentUrl, currentPhotoType) {
        const modal = document.getElementById('edit360Modal');
        if (!modal) return;
        document.getElementById('edit360AreaName').innerText = 'Area: ' + areaName;
        document.getElementById('edit360Form').action = '/admin/areas/' + areaId + '/update-360';
        document.getElementById('areaSelectorContainer').classList.add('hidden');
        document.getElementById('modalAreaSelect').required = false;
        document.getElementById('modalAreaSelect').value = areaId;
        document.getElementById('modal_photo_360').value = '';
        document.getElementById('fileLabel360').innerText = 'Klik untuk memilih file foto (JPG, PNG, WebP)';
        document.getElementById('aspectRatioWarningBox').classList.add('hidden');
        selectedImageDimensions = null;

        // Set radio button
        const photoType = currentPhotoType || 'panorama_360';
        setRadioPhotoType(photoType);

        modal.classList.remove('hidden');
    }

    function openUpload360Modal() {
        const modal = document.getElementById('edit360Modal');
        if (!modal) return;
        document.getElementById('edit360AreaName').innerText = 'Pilih area dan upload foto ruangan / 360';
        document.getElementById('areaSelectorContainer').classList.remove('hidden');
        document.getElementById('modalAreaSelect').required = true;
        document.getElementById('modalAreaSelect').value = '';
        document.getElementById('edit360Form').action = '{{ route("admin.areas.upload-360-general") }}';
        document.getElementById('modal_photo_360').value = '';
        document.getElementById('fileLabel360').innerText = 'Klik untuk memilih file foto (JPG, PNG, WebP)';
        document.getElementById('aspectRatioWarningBox').classList.add('hidden');
        selectedImageDimensions = null;
        setRadioPhotoType('panorama_360');

        modal.classList.remove('hidden');
    }

    function setRadioPhotoType(type) {
        const radios = document.getElementsByName('photo_type');
        for (let r of radios) {
            r.checked = (r.value === type);
        }
        handlePhotoTypeChange(type);
    }

    function handlePhotoTypeChange(type) {
        const card360 = document.getElementById('cardOption360');
        const cardStandard = document.getElementById('cardOptionStandard');
        if (type === 'standard') {
            cardStandard.className = 'relative flex flex-col p-3.5 rounded-xl border-2 cursor-pointer transition-all border-sky-500 bg-sky-50/60 dark:bg-sky-950/30';
            card360.className = 'relative flex flex-col p-3.5 rounded-xl border-2 cursor-pointer transition-all border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/40 hover:border-purple-400';
            document.getElementById('fileSubHint360').innerText = 'Foto proporsional (kamera HP/digital) tanpa distorsi panorama bola.';
        } else {
            card360.className = 'relative flex flex-col p-3.5 rounded-xl border-2 cursor-pointer transition-all border-purple-500 bg-purple-50/50 dark:bg-purple-950/30';
            cardStandard.className = 'relative flex flex-col p-3.5 rounded-xl border-2 cursor-pointer transition-all border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/40 hover:border-sky-400';
            document.getElementById('fileSubHint360').innerText = 'Format equirectangular rasio 2:1 dari kamera 360°.';
        }
        checkAspectRatioRecommendation();
    }

    function handleFileSelected(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const sizeMb = (file.size / 1024 / 1024).toFixed(2);
            document.getElementById('fileLabel360').innerText = 'File dipilih: ' + file.name + ' (' + sizeMb + ' MB)';

            // Read image to calculate width, height, and ratio
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = new Image();
                img.onload = function() {
                    selectedImageDimensions = {
                        width: img.naturalWidth,
                        height: img.naturalHeight,
                        ratio: (img.naturalWidth / img.naturalHeight)
                    };
                    checkAspectRatioRecommendation();
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    }

    function checkAspectRatioRecommendation() {
        const warningBox = document.getElementById('aspectRatioWarningBox');
        if (!selectedImageDimensions) {
            warningBox.classList.add('hidden');
            return;
        }

        const currentType = document.querySelector('input[name="photo_type"]:checked')?.value || 'panorama_360';
        const ratio = selectedImageDimensions.ratio;
        const ratioStr = ratio.toFixed(2) + ':1';
        const resolutionStr = selectedImageDimensions.width + ' × ' + selectedImageDimensions.height + ' px';

        // An equirectangular 360 photo is strictly or very close to 2:1 aspect ratio (between 1.85 and 2.15)
        const isIdeal360Ratio = (ratio >= 1.85 && ratio <= 2.15);

        if (currentType === 'panorama_360' && !isIdeal360Ratio) {
            warningBox.className = 'rounded-xl border border-amber-300 dark:border-amber-700 bg-amber-50 dark:bg-amber-950/40 p-3.5 text-xs flex items-start gap-3';
            document.getElementById('warningIcon').innerText = '⚠️';
            document.getElementById('warningText').innerHTML = 
                '<strong>Perhatian Rasio Foto:</strong> Foto yang dipilih berukuran <strong>' + resolutionStr + '</strong> (rasio <strong>' + ratioStr + '</strong>), bukan rasio 2:1 panorama 360°.<br>' +
                'Jika ini foto ruangan biasa dari kamera HP / digital, foto akan terlihat <strong>gepeng atau melengkung</strong> jika dipaksa ke viewer 360°.<br>' +
                'Disarankan menggunakan opsi <strong>Foto Biasa (Standar)</strong> agar tampil jernih & proporsional.';
            
            const btnQuick = document.getElementById('btnQuickSwitchType');
            btnQuick.innerText = 'Ubah ke Foto Biasa (Standar)';
            btnQuick.className = 'mt-2 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold bg-sky-600 hover:bg-sky-700 text-white shadow-xs cursor-pointer';
            btnQuick.onclick = function() { setRadioPhotoType('standard'); };
            warningBox.classList.remove('hidden');
        } else if (currentType === 'standard') {
            warningBox.className = 'rounded-xl border border-sky-300 dark:border-sky-800 bg-sky-50 dark:bg-sky-950/40 p-3 text-xs flex items-start gap-3';
            document.getElementById('warningIcon').innerText = 'ℹ️';
            document.getElementById('warningText').innerHTML = 
                '<strong>Mode Foto Standar:</strong> Foto akan ditampilkan proporsional sesuai resolusi asli <strong>' + resolutionStr + '</strong> tanpa distorsi lengkung.';
            document.getElementById('btnQuickSwitchType').classList.add('hidden');
            warningBox.classList.remove('hidden');
        } else {
            warningBox.className = 'rounded-xl border border-emerald-300 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950/40 p-3 text-xs flex items-start gap-3';
            document.getElementById('warningIcon').innerText = '✅';
            document.getElementById('warningText').innerHTML = 
                '<strong>Format Cocok:</strong> Foto memiliki rasio <strong>' + ratioStr + '</strong> (' + resolutionStr + ') yang sesuai untuk panorama equirectangular 360°.';
            document.getElementById('btnQuickSwitchType').classList.add('hidden');
            warningBox.classList.remove('hidden');
        }
    }

    function validate360Form() {
        const areaSelectContainer = document.getElementById('areaSelectorContainer');
        if (areaSelectContainer && !areaSelectContainer.classList.contains('hidden')) {
            const areaSelect = document.getElementById('modalAreaSelect');
            if (!areaSelect.value) {
                alert('Silakan pilih ruangan / area terlebih dahulu.');
                areaSelect.focus();
                return false;
            }
        }
        const fileInput = document.getElementById('modal_photo_360');
        if (!fileInput.files || !fileInput.files[0]) {
            alert('Silakan pilih file foto yang ingin diunggah.');
            return false;
        }
        return true;
    }

    function closeEdit360Modal() {
        const modal = document.getElementById('edit360Modal');
        if (modal) modal.classList.add('hidden');
    }
</script>
@endsection
