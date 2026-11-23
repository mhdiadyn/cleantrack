@extends('layouts.app')

@section('title', 'Dashboard Petugas')
@section('header_title', 'Dashboard Petugas Kebersihan')
@section('header_subtitle', 'Pantau jadwal dan lakukan pemeriksaan kebersihan area')

@section('content')
<div class="space-y-6">

    <!-- Greeting Card (Corporate Clean) -->
    <div class="bg-white dark:bg-[#1f2020] rounded-xl border border-[#eae8e7] dark:border-[#2e3030] p-6 shadow-[0px_4px_12px_rgba(0,0,0,0.05)] border-l-4 border-l-[#dc143c]">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full text-xs font-semibold bg-[#fff1f0] text-[#dc143c] border border-[#fecdd3] dark:bg-[#3b0b14] dark:text-[#ffb3b3] dark:border-[#831843] mb-2.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#dc143c]"></span>
                    <span>Petugas Operasional Kebersihan</span>
                </div>
                <h2 class="font-heading text-xl sm:text-2xl font-semibold text-[#1b1c1c] dark:text-white">
                    Halo, {{ Auth::user()->name }}!
                </h2>
                <p class="text-xs sm:text-sm text-[#5c3f3f] dark:text-[#c8c6c5] mt-1 max-w-xl">
                    Periksa status kebersihan ruangan hari ini, lihat denah dan galeri foto, lalu catat hasil pemeriksaan.
                </p>
            </div>
            
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('monitoring.create') }}" class="btn-primary">
                    <i class="fa-solid fa-clipboard-check text-xs"></i>
                    <span>Mulai Pemeriksaan</span>
                </a>
                <a href="{{ route('facility.index') }}" class="btn-secondary">
                    <i class="fa-solid fa-map-location-dot text-xs"></i>
                    <span>Denah & Galeri Ruangan</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 4 Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- 1. Total Area Tugas -->
        <div class="card-clean card-clean-hover p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-[#5c3f3f] dark:text-[#c8c6c5] uppercase tracking-wider">Total Area Tugas</span>
                <div class="w-9 h-9 rounded-xl bg-[#fff1f0] text-[#dc143c] dark:bg-[#3b0b14] dark:text-[#ffb3b3] flex items-center justify-center text-sm">
                    <i class="fa-solid fa-map-location"></i>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="font-heading text-2xl font-semibold text-[#1b1c1c] dark:text-white">{{ $totalAreasToInspect }}</h3>
                <p class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5] mt-0.5">Area aktif yang dipantau</p>
            </div>
        </div>

        <!-- 2. Area Sudah Diperiksa -->
        <div class="card-clean card-clean-hover p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-[#5c3f3f] dark:text-[#c8c6c5] uppercase tracking-wider">Sudah Diperiksa</span>
                <div class="w-9 h-9 rounded-xl bg-[#daffd5] text-[#006622] dark:bg-[#003912] dark:text-[#83fc8e] flex items-center justify-center text-sm">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="font-heading text-2xl font-semibold text-[#006622] dark:text-[#83fc8e]">{{ $areasInspectedCount }}</h3>
                <p class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5] mt-0.5">Tercatat hari ini</p>
            </div>
        </div>

        <!-- 3. Area Belum Diperiksa -->
        <div class="card-clean card-clean-hover p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-[#5c3f3f] dark:text-[#c8c6c5] uppercase tracking-wider">Belum Diperiksa</span>
                <div class="w-9 h-9 rounded-xl bg-[#f5f3f3] text-[#5c3f3f] dark:bg-[#262727] dark:text-[#c8c6c5] flex items-center justify-center text-sm">
                    <i class="fa-solid fa-hourglass-half"></i>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="font-heading text-2xl font-semibold text-[#1b1c1c] dark:text-white">{{ $areasNotInspectedCount }}</h3>
                <p class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5] mt-0.5">Menunggu pemeriksaan</p>
            </div>
        </div>

        <!-- 4. Area yang Perlu Dibersihkan -->
        <div class="card-clean card-clean-hover p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-[#5c3f3f] dark:text-[#c8c6c5] uppercase tracking-wider">Perlu Tindakan</span>
                <div class="w-9 h-9 rounded-xl bg-[#ffdad6] text-[#ba1a1a] dark:bg-[#410002] dark:text-[#ffb4ab] flex items-center justify-center text-sm">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="font-heading text-2xl font-semibold text-[#ba1a1a] dark:text-[#ffb4ab]">{{ $areasNeedCleaningCount }}</h3>
                <p class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5] mt-0.5">Perlu dibersihkan segera</p>
            </div>
        </div>
    </div>

    <!-- Status Area Hari Ini (Checklist Area) -->
    <div class="card-clean overflow-hidden">
        <div class="p-5 border-b border-[#eae8e7] dark:border-[#2e3030] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h3 class="font-heading text-sm font-semibold text-[#1b1c1c] dark:text-white">Status Pemeriksaan Area Hari Ini</h3>
                <p class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5]">Daftar seluruh area dan status monitoring kebersihannya hari ini</p>
            </div>
            <span class="text-xs font-medium text-[#5c3f3f] dark:text-[#c8c6c5] px-3 py-1 bg-[#f5f3f3] dark:bg-[#262727] border border-[#eae8e7] dark:border-[#333535] rounded-xl">
                {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-[#1b1c1c] dark:text-[#f2f0f0]">
                <thead class="bg-[#f5f3f3] dark:bg-[#262727] text-[#5c3f3f] dark:text-[#c8c6c5] font-semibold uppercase text-[11px] tracking-wider border-b border-[#eae8e7] dark:border-[#2e3030]">
                    <tr>
                        <th class="py-3 px-4 text-center w-12">No</th>
                        <th class="py-3 px-4">Nama Area</th>
                        <th class="py-3 px-4">Lokasi & Lt</th>
                        <th class="py-3 px-4">Kondisi Terakhir</th>
                        <th class="py-3 px-4">Status Hari Ini</th>
                        <th class="py-3 px-4">Petugas Terakhir</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#eae8e7] dark:divide-[#2e3030]">
                    @forelse($areasStatus as $index => $area)
                        <tr class="hover:bg-[#fbf9f8] dark:hover:bg-[#262727]/60 transition-colors">
                            <td class="py-3.5 px-4 text-center font-medium text-[#5c3f3f] dark:text-[#8c8282]">{{ $index + 1 }}</td>
                            <td class="py-3.5 px-4">
                                <div class="font-semibold text-[#1b1c1c] dark:text-white">{{ $area->name }}</div>
                                @if($area->description)
                                    <div class="text-[11px] text-[#5c3f3f] dark:text-[#c8c6c5] truncate max-w-xs">{{ $area->description }}</div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-[#5c3f3f] dark:text-[#c8c6c5]">
                                {{ $area->location }} <span class="font-semibold text-[#dc143c] dark:text-[#ffb3b3] text-[11px]">(Lt {{ $area->floor }})</span>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($area->latest_today)
                                    <x-badge-condition :condition="$area->latest_today->cleanliness_condition" />
                                @else
                                    <span class="text-[#5c3f3f] dark:text-[#8c8282] text-xs italic">Belum diperiksa</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                @if($area->latest_today)
                                    <x-badge-status :status="$area->latest_today->status" />
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-[#f5f3f3] text-[#5c3f3f] border border-[#eae8e7] dark:bg-[#262727] dark:text-[#c8c6c5] dark:border-[#333535]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#916f6e]"></span>
                                        Belum Diperiksa
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                @if($area->latest_today && $area->latest_today->user)
                                    <div class="text-xs font-medium text-[#1b1c1c] dark:text-white">{{ $area->latest_today->user->name }}</div>
                                    <div class="text-[10px] text-[#5c3f3f] dark:text-[#c8c6c5]">{{ substr($area->latest_today->check_time, 0, 5) }} WIB</div>
                                @else
                                    <span class="text-[#5c3f3f] dark:text-[#8c8282] text-xs">-</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('monitoring.create', ['area_id' => $area->id]) }}" 
                                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#dc143c] hover:bg-[#b1002c] text-white font-semibold text-xs transition-colors">
                                        <i class="fa-solid fa-pencil text-[10px]"></i>
                                        <span>Periksa</span>
                                    </a>
                                    @if($area->latest_today)
                                        <a href="{{ route('monitoring.show', $area->latest_today->id) }}" 
                                           class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-[#f5f3f3] dark:bg-[#262727] hover:bg-[#eae8e7] dark:hover:bg-[#333535] text-[#5c3f3f] dark:text-[#c8c6c5] font-semibold text-xs border border-[#eae8e7] dark:border-[#333535] transition-colors" title="Lihat Detail">
                                            <i class="fa-solid fa-eye text-xs"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-[#5c3f3f] dark:text-[#c8c6c5]">
                                Belum ada area yang terdaftar di sistem.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Riwayat Pemeriksaan Saya Terbaru -->
    <div class="card-clean overflow-hidden">
        <div class="p-5 border-b border-[#eae8e7] dark:border-[#2e3030] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h3 class="font-heading text-sm font-semibold text-[#1b1c1c] dark:text-white">Riwayat Pemeriksaan Terakhir Saya</h3>
                <p class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5]">Laporan kebersihan yang baru saja Anda input</p>
            </div>
            <a href="{{ route('petugas.my-monitoring') }}" class="text-xs font-semibold text-[#dc143c] dark:text-[#ffb3b3] hover:underline flex items-center gap-1">
                <span>Lihat Semua Riwayat Saya</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-[#1b1c1c] dark:text-[#f2f0f0]">
                <thead class="bg-[#f5f3f3] dark:bg-[#262727] text-[#5c3f3f] dark:text-[#c8c6c5] font-semibold uppercase text-[11px] tracking-wider border-b border-[#eae8e7] dark:border-[#2e3030]">
                    <tr>
                        <th class="py-3 px-4 text-center w-12">No</th>
                        <th class="py-3 px-4">Area</th>
                        <th class="py-3 px-4">Kondisi</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Tanggal & Jam</th>
                        <th class="py-3 px-4">Catatan</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#eae8e7] dark:divide-[#2e3030]">
                    @forelse($myRecentMonitorings as $index => $item)
                        <tr class="hover:bg-[#fbf9f8] dark:hover:bg-[#262727]/60 transition-colors">
                            <td class="py-3.5 px-4 text-center font-medium text-[#5c3f3f] dark:text-[#8c8282]">{{ $index + 1 }}</td>
                            <td class="py-3.5 px-4">
                                <div class="font-semibold text-[#1b1c1c] dark:text-white">{{ $item->display_area_name }}@if($item->area) (Lt {{ $item->area->floor }})@endif</div>
                                <div class="text-[11px] text-[#5c3f3f] dark:text-[#c8c6c5]">{{ $item->display_area_location }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <x-badge-condition :condition="$item->cleanliness_condition" />
                            </td>
                            <td class="py-3.5 px-4">
                                <x-badge-status :status="$item->status" />
                            </td>
                            <td class="py-3.5 px-4 text-[#5c3f3f] dark:text-[#c8c6c5]">
                                {{ \Carbon\Carbon::parse($item->check_date)->format('d/m/Y') }}
                                <span class="text-[11px] block">{{ substr($item->check_time, 0, 5) }} WIB</span>
                            </td>
                            <td class="py-3.5 px-4 text-[#5c3f3f] dark:text-[#c8c6c5] max-w-xs truncate">
                                {{ $item->notes ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('monitoring.show', $item->id) }}" 
                                   class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold text-[#dc143c] dark:text-[#ffb3b3] hover:bg-[#fff1f0] dark:hover:bg-[#3b0b14] transition-colors">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                    <span>Detail</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-[#5c3f3f] dark:text-[#c8c6c5]">
                                <i class="fa-regular fa-clipboard text-3xl mb-2 block opacity-40"></i>
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
