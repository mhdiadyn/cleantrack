@extends('layouts.app')

@section('title', 'Dashboard Petugas')
@section('header_title', 'Dashboard Petugas Kebersihan')
@section('header_subtitle', 'Pantau jadwal dan lakukan pemeriksaan kebersihan area')

@section('content')
<div class="space-y-6">

    <!-- CTA Hero Banner Merah Putih -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-red-700 via-red-600 to-rose-600 text-white p-6 sm:p-8 shadow-xl shadow-red-500/10">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-white/20 text-white border border-white/30 mb-3 backdrop-blur-sm">
                    <i class="fa-solid fa-sparkles text-red-200"></i>
                    Petugas Operasional Kebersihan
                </span>
                <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Halo, {{ Auth::user()->name }}!</h2>
                <p class="text-red-100 text-xs sm:text-sm mt-1 max-w-xl">
                    Siap untuk menjaga kebersihan fasilitas hari ini? Gunakan denah interaktif untuk melihat status ruangan, ambil foto kondisi, dan rekam foto 360°.
                </p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('monitoring.create') }}" 
                   class="inline-flex items-center gap-2.5 px-5 py-3 rounded-xl bg-white text-red-600 hover:bg-red-50 font-extrabold text-sm shadow-lg shadow-black/10 transform hover:scale-105 transition-all">
                    <i class="fa-solid fa-clipboard-check text-base"></i>
                    <span>Mulai Pemeriksaan</span>
                </a>
                <a href="{{ route('floorplan.index') }}" 
                   class="inline-flex items-center gap-2 px-4 py-3 rounded-xl bg-red-800/80 hover:bg-red-900 text-white border border-red-400/40 font-bold text-xs transition-all">
                    <i class="fa-solid fa-map-location-dot"></i>
                    <span>Denah Gedung</span>
                </a>
            </div>
        </div>
        <!-- Decorative background glow -->
        <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>
    </div>

    <!-- 4 Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- 1. Total Area yang Harus Diperiksa -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Area Tugas</span>
                <div class="w-10 h-10 rounded-xl bg-red-50 dark:bg-red-950/50 text-red-600 dark:text-red-400 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-map-location"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-black text-slate-800 dark:text-slate-100">{{ $totalAreasToInspect }}</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Area aktif saat ini</p>
            </div>
        </div>

        <!-- 2. Area Sudah Diperiksa -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Sudah Diperiksa</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ $areasInspectedCount }}</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Telah tercatat hari ini</p>
            </div>
        </div>

        <!-- 3. Area Belum Diperiksa -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Belum Diperiksa</span>
                <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-hourglass-half"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-black text-slate-700 dark:text-slate-200">{{ $areasNotInspectedCount }}</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Menunggu pemeriksaan</p>
            </div>
        </div>

        <!-- 4. Area yang Perlu Dibersihkan -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Perlu Dibersihkan</span>
                <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-black text-rose-600 dark:text-rose-400">{{ $areasNeedCleaningCount }}</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Perlu tindakan cepat</p>
            </div>
        </div>
    </div>

    <!-- Status Area Hari Ini (Checklist Area) -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="p-5 sm:px-6 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Status Pemeriksaan Area Hari Ini</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Daftar seluruh area dan status pemeriksaan kebersihannya untuk hari ini</p>
            </div>
            <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 px-3 py-1 bg-slate-100 dark:bg-slate-800 rounded-lg">
                {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 font-bold uppercase text-[10px] tracking-wider border-b border-slate-100 dark:border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4 text-center w-12">No</th>
                        <th class="py-3.5 px-4">Nama Area</th>
                        <th class="py-3.5 px-4">Lokasi & Lt</th>
                        <th class="py-3.5 px-4">Kondisi Terakhir</th>
                        <th class="py-3.5 px-4">Status Hari Ini</th>
                        <th class="py-3.5 px-4">Petugas Terakhir</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($areasStatus as $index => $area)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="py-3.5 px-4 text-center font-medium text-slate-400 dark:text-slate-500">{{ $index + 1 }}</td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900 dark:text-slate-100">{{ $area->name }}</div>
                                @if($area->description)
                                    <div class="text-[11px] text-slate-400 dark:text-slate-500 truncate max-w-xs">{{ $area->description }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-medium text-slate-600 dark:text-slate-400">
                                {{ $area->location }} <span class="font-bold text-red-600 dark:text-red-400 text-[10px]">(Lt {{ $area->floor }})</span>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($area->latest_today)
                                    <x-badge-condition :condition="$area->latest_today->cleanliness_condition" />
                                @else
                                    <span class="text-slate-400 dark:text-slate-500 text-xs italic">Belum diperiksa</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                @if($area->latest_today)
                                    <x-badge-status :status="$area->latest_today->status" />
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400 dark:bg-slate-500"></span>
                                        Belum Diperiksa
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                @if($area->latest_today && $area->latest_today->user)
                                    <div class="text-xs font-medium text-slate-800 dark:text-slate-200">{{ $area->latest_today->user->name }}</div>
                                    <div class="text-[10px] text-slate-400 dark:text-slate-500">{{ substr($area->latest_today->check_time, 0, 5) }} WIB</div>
                                @else
                                    <span class="text-slate-400 dark:text-slate-500 text-xs">-</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('monitoring.create', ['area_id' => $area->id]) }}" 
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-600 hover:bg-red-700 text-white font-bold text-[11px] shadow-xs transition-colors">
                                        <i class="fa-solid fa-pencil"></i>
                                        <span>Periksa</span>
                                    </a>
                                    @if($area->latest_today)
                                        <a href="{{ route('monitoring.show', $area->latest_today->id) }}" 
                                           class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-[11px] transition-colors" title="Lihat Detail Hari Ini">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400 dark:text-slate-500">
                                Belum ada area yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Riwayat Pemeriksaan Saya Terbaru -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="p-5 sm:px-6 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Riwayat Pemeriksaan Terakhir Saya</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Daftar laporan kebersihan yang baru saja Anda input</p>
            </div>
            <a href="{{ route('petugas.my-monitoring') }}" class="text-xs font-bold text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 flex items-center gap-1">
                <span>Lihat Semua Riwayat Saya</span>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 font-bold uppercase text-[10px] tracking-wider border-b border-slate-100 dark:border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4 text-center w-12">No</th>
                        <th class="py-3.5 px-4">Area</th>
                        <th class="py-3.5 px-4">Kondisi</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4">Tanggal & Jam</th>
                        <th class="py-3.5 px-4">Catatan</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($myRecentMonitorings as $index => $item)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="py-3.5 px-4 text-center font-medium text-slate-400 dark:text-slate-500">{{ $index + 1 }}</td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900 dark:text-slate-100">{{ $item->area->name ?? '-' }} (Lt {{ $item->area->floor ?? 1 }})</div>
                                <div class="text-[11px] text-slate-400 dark:text-slate-500">{{ $item->area->location ?? '-' }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <x-badge-condition :condition="$item->cleanliness_condition" />
                            </td>
                            <td class="py-3.5 px-4">
                                <x-badge-status :status="$item->status" />
                            </td>
                            <td class="py-3.5 px-4 font-medium text-slate-600 dark:text-slate-400">
                                {{ \Carbon\Carbon::parse($item->check_date)->format('d/m/Y') }}
                                <span class="text-slate-400 dark:text-slate-500 text-[11px] block">{{ substr($item->check_time, 0, 5) }} WIB</span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 dark:text-slate-400 max-w-xs truncate">
                                {{ $item->notes ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('monitoring.show', $item->id) }}" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-50 dark:bg-red-950/60 hover:bg-red-100 dark:hover:bg-red-900/60 text-red-700 dark:text-red-300 font-bold text-[11px] transition-colors">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                    <span>Detail</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400 dark:text-slate-500">
                                <i class="fa-solid fa-clipboard-check text-3xl mb-2 block"></i>
                                Anda belum memiliki riwayat pemeriksaan. Klik "Mulai Pemeriksaan" untuk memulai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
