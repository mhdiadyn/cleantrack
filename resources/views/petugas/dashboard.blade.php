@extends('layouts.app')

@section('title', 'Dashboard Petugas')
@section('header_title', 'Dashboard Petugas Kebersihan')
@section('header_subtitle', 'Pantau jadwal dan lakukan pemeriksaan kebersihan area')

@section('content')
<div class="space-y-6">

    <!-- CTA Hero Banner -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-teal-900 via-slate-900 to-emerald-950 text-white p-6 sm:p-8 shadow-lg border border-teal-800/40">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 mb-3">
                    <i class="fa-solid fa-sparkles text-emerald-400"></i>
                    Petugas Operasional Kebersihan
                </span>
                <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Halo, {{ Auth::user()->name }}!</h2>
                <p class="text-slate-300 text-xs sm:text-sm mt-1 max-w-xl">
                    Siap untuk menjaga kebersihan fasilitas hari ini? Lakukan pemeriksaan berkala, ambil foto kondisi, dan catat status area dengan teliti.
                </p>
            </div>
            
            <div>
                <a href="{{ route('monitoring.create') }}" 
                   class="inline-flex items-center gap-2.5 px-6 py-3.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-400 hover:from-emerald-600 hover:to-teal-500 text-slate-950 font-extrabold text-sm shadow-xl shadow-teal-500/30 transform hover:-translate-y-0.5 transition-all">
                    <i class="fa-solid fa-clipboard-check text-base"></i>
                    <span>Mulai Pemeriksaan</span>
                </a>
            </div>
        </div>
        <!-- Decorative background glow -->
        <div class="absolute -right-10 -bottom-10 w-72 h-72 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
    </div>

    <!-- 4 Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- 1. Total Area yang Harus Diperiksa -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Area Tugas</span>
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-map-location"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-black text-slate-800">{{ $totalAreasToInspect }}</h3>
                <p class="text-xs text-slate-500 mt-1">Area aktif saat ini</p>
            </div>
        </div>

        <!-- 2. Area Sudah Diperiksa -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Sudah Diperiksa</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-black text-emerald-600">{{ $areasInspectedCount }}</h3>
                <p class="text-xs text-slate-500 mt-1">Telah tercatat hari ini</p>
            </div>
        </div>

        <!-- 3. Area Belum Diperiksa -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Belum Diperiksa</span>
                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-hourglass-half"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-black text-slate-700">{{ $areasNotInspectedCount }}</h3>
                <p class="text-xs text-slate-500 mt-1">Menunggu pemeriksaan</p>
            </div>
        </div>

        <!-- 4. Area yang Perlu Dibersihkan -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Perlu Dibersihkan</span>
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-black text-rose-600">{{ $areasNeedCleaningCount }}</h3>
                <p class="text-xs text-slate-500 mt-1">Perlu tindakan cepat</p>
            </div>
        </div>
    </div>

    <!-- Status Area Hari Ini (Checklist Area) -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 sm:px-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold text-slate-800">Status Pemeriksaan Area Hari Ini</h3>
                <p class="text-xs text-slate-500">Daftar seluruh area dan status pemeriksaan kebersihannya untuk hari ini</p>
            </div>
            <span class="text-xs font-semibold text-slate-500 px-3 py-1 bg-slate-100 rounded-lg">
                {{ \Carbon\Carbon::now()->format('d M Y') }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase text-[10px] tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-4 text-center w-12">No</th>
                        <th class="py-3.5 px-4">Nama Area</th>
                        <th class="py-3.5 px-4">Lokasi</th>
                        <th class="py-3.5 px-4">Kondisi Terakhir</th>
                        <th class="py-3.5 px-4">Status Hari Ini</th>
                        <th class="py-3.5 px-4">Petugas Terakhir</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($areasStatus as $index => $area)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-4 text-center font-medium text-slate-400">{{ $index + 1 }}</td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900">{{ $area->name }}</div>
                                @if($area->description)
                                    <div class="text-[11px] text-slate-400 truncate max-w-xs">{{ $area->description }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-medium text-slate-600">
                                {{ $area->location }}
                            </td>
                            <td class="py-3.5 px-4">
                                @if($area->latest_today)
                                    <x-badge-condition :condition="$area->latest_today->cleanliness_condition" />
                                @else
                                    <span class="text-slate-400 text-xs italic">Belum diperiksa</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                @if($area->latest_today)
                                    <x-badge-status :status="$area->latest_today->status" />
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        Belum Diperiksa
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                @if($area->latest_today && $area->latest_today->user)
                                    <div class="text-xs font-medium text-slate-800">{{ $area->latest_today->user->name }}</div>
                                    <div class="text-[10px] text-slate-400">{{ substr($area->latest_today->check_time, 0, 5) }} WIB</div>
                                @else
                                    <span class="text-slate-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('monitoring.create', ['area_id' => $area->id]) }}" 
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-teal-600 hover:bg-teal-700 text-white font-bold text-[11px] shadow-xs transition-colors">
                                        <i class="fa-solid fa-pencil"></i>
                                        <span>Periksa</span>
                                    </a>
                                    @if($area->latest_today)
                                        <a href="{{ route('monitoring.show', $area->latest_today->id) }}" 
                                           class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-[11px] transition-colors" title="Lihat Detail Hari Ini">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">
                                Belum ada area yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Riwayat Pemeriksaan Saya Terbaru -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 sm:px-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold text-slate-800">Riwayat Pemeriksaan Terakhir Saya</h3>
                <p class="text-xs text-slate-500">Daftar laporan kebersihan yang baru saja Anda input</p>
            </div>
            <a href="{{ route('petugas.my-monitoring') }}" class="text-xs font-bold text-teal-600 hover:text-teal-700 flex items-center gap-1">
                <span>Lihat Semua Riwayat Saya</span>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase text-[10px] tracking-wider border-b border-slate-100">
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
                <tbody class="divide-y divide-slate-100">
                    @forelse($myRecentMonitorings as $index => $item)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-4 text-center font-medium text-slate-400">{{ $index + 1 }}</td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900">{{ $item->area->name ?? '-' }}</div>
                                <div class="text-[11px] text-slate-400">{{ $item->area->location ?? '-' }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <x-badge-condition :condition="$item->cleanliness_condition" />
                            </td>
                            <td class="py-3.5 px-4">
                                <x-badge-status :status="$item->status" />
                            </td>
                            <td class="py-3.5 px-4 font-medium text-slate-600">
                                {{ \Carbon\Carbon::parse($item->check_date)->format('d/m/Y') }}
                                <span class="text-slate-400 text-[11px] block">{{ substr($item->check_time, 0, 5) }} WIB</span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 max-w-xs truncate">
                                {{ $item->notes ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('monitoring.show', $item->id) }}" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-teal-50 hover:bg-teal-100 text-teal-700 font-bold text-[11px] transition-colors">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                    <span>Detail</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">
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
