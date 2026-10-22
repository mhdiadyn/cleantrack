@extends('layouts.app')

@section('title', 'Ruangan: ' . $area->name . ' (' . $area->code . ')')
@section('header_title', 'Informasi Ruangan')
@section('header_subtitle', 'Verifikasi data ruangan sebelum mencatat hasil pemeriksaan kebersihan')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Area Identity Card -->
    <div class="card-clean p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-[#eae8e7] dark:border-[#2e3030]">
            <div class="flex items-start gap-4">
                <div class="w-14 h-14 rounded-2xl bg-[#fff1f0] text-[#dc143c] dark:bg-[#3b0b14] dark:text-[#ffb3b3] border border-[#fecdd3] dark:border-[#831843] flex items-center justify-center text-2xl shrink-0 shadow-xs">
                    <i class="fa-solid fa-door-open"></i>
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-block px-2.5 py-0.5 rounded-full bg-[#f5f3f3] dark:bg-[#262727] text-[#1b1c1c] dark:text-white font-mono font-bold text-xs border border-[#eae8e7] dark:border-[#333535] uppercase">
                            {{ $area->code }}
                        </span>
                        @if($area->is_active)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#daffd5] text-[#006622] border border-[#b2e5be] dark:bg-[#003912] dark:text-[#83fc8e] dark:border-[#00531a]">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#00822d]"></span>
                                Ruangan Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-[#f5f3f3] text-[#5c3f3f] border border-[#eae8e7] dark:bg-[#262727] dark:text-[#c8c6c5] dark:border-[#333535]">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#916f6e]"></span>
                                Nonaktif
                            </span>
                        @endif
                    </div>
                    <h2 class="font-heading text-2xl font-bold text-[#1b1c1c] dark:text-white mt-1.5">
                        {{ $area->name }}
                    </h2>
                    <p class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5] mt-1 flex items-center gap-1.5">
                        <i class="fa-solid fa-location-dot text-[#dc143c] dark:text-[#ffb3b3]"></i>
                        <span>{{ $area->location }} &bull; Lantai {{ $area->floor }}</span>
                    </p>
                </div>
            </div>

            <!-- Quick Action: Direct Monitoring -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                <a href="{{ route('monitoring.create', ['area_id' => $area->id]) }}" class="btn-primary btn-primary-lg justify-center text-xs">
                    <i class="fa-solid fa-clipboard-check mr-1.5"></i>
                    <span>Catat Monitoring</span>
                </a>
            </div>
        </div>

        <!-- Room Key Stats & Conditions -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-6">
            <div class="p-4 rounded-xl bg-[#f5f3f3] dark:bg-[#262727] border border-[#eae8e7] dark:border-[#333535]">
                <span class="text-[10px] font-semibold text-[#5c3f3f] dark:text-[#8c8282] uppercase tracking-wider block">Status Kebersihan Hari Ini</span>
                <div class="mt-2 flex items-center gap-2">
                    @if($area->current_status === 'bersih')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-[#daffd5] text-[#006622] border border-[#b2e5be] dark:bg-[#003912] dark:text-[#83fc8e] dark:border-[#00531a]">
                            <i class="fa-solid fa-circle-check text-xs"></i>
                            Bersih
                        </span>
                    @elseif($area->current_status === 'perlu_tindakan')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-[#fef3c7] text-[#b45309] border border-[#fde68a] dark:bg-[#451a03] dark:text-[#fcd34d] dark:border-[#78350f]">
                            <i class="fa-solid fa-triangle-exclamation text-xs"></i>
                            Perlu Tindakan
                        </span>
                    @elseif($area->current_status === 'kotor')
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-[#ffdad6] text-[#ba1a1a] border border-[#ffb4ab] dark:bg-[#410002] dark:text-[#ffb4ab] dark:border-[#93000a]">
                            <i class="fa-solid fa-circle-xmark text-xs"></i>
                            Kotor
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold bg-white dark:bg-[#1f2020] text-[#5c3f3f] dark:text-[#c8c6c5] border border-[#eae8e7] dark:border-[#333535]">
                            <i class="fa-regular fa-clock text-xs"></i>
                            Belum Diperiksa
                        </span>
                    @endif
                </div>
            </div>

            <div class="p-4 rounded-xl bg-[#f5f3f3] dark:bg-[#262727] border border-[#eae8e7] dark:border-[#333535]">
                <span class="text-[10px] font-semibold text-[#5c3f3f] dark:text-[#8c8282] uppercase tracking-wider block">Total Histori Diperiksa</span>
                <p class="font-heading text-xl font-bold text-[#1b1c1c] dark:text-white mt-1">
                    {{ $area->cleaningMonitorings()->count() }} <span class="text-xs font-normal text-[#5c3f3f] dark:text-[#c8c6c5]">kali</span>
                </p>
            </div>

            <div class="p-4 rounded-xl bg-[#f5f3f3] dark:bg-[#262727] border border-[#eae8e7] dark:border-[#333535]">
                <span class="text-[10px] font-semibold text-[#5c3f3f] dark:text-[#8c8282] uppercase tracking-wider block">Keterangan / Fasilitas</span>
                <p class="text-xs text-[#1b1c1c] dark:text-[#f2f0f0] mt-1 line-clamp-2">
                    {{ $area->description ?: 'Tidak ada keterangan khusus.' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Last Inspection Info (if any) -->
    @php
        $latest = $area->latestMonitoring;
    @endphp
    @if($latest)
        <div class="card-clean p-6">
            <h3 class="font-heading text-sm font-semibold text-[#1b1c1c] dark:text-white mb-3 flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-[#dc143c]"></i>
                <span>Pemeriksaan Terakhir Pada Ruangan Ini</span>
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs bg-[#f5f3f3] dark:bg-[#262727] p-4 rounded-xl border border-[#eae8e7] dark:border-[#333535]">
                <div>
                    <span class="text-[10px] text-[#5c3f3f] dark:text-[#8c8282] block">Petugas Terakhir</span>
                    <span class="font-semibold text-[#1b1c1c] dark:text-white">{{ $latest->user->name ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-[10px] text-[#5c3f3f] dark:text-[#8c8282] block">Tanggal & Jam</span>
                    <span class="font-semibold text-[#1b1c1c] dark:text-white">{{ \Carbon\Carbon::parse($latest->check_date)->format('d/m/Y') }} &bull; {{ substr($latest->check_time, 0, 5) }}</span>
                </div>
                <div>
                    <span class="text-[10px] text-[#5c3f3f] dark:text-[#8c8282] block">Kondisi</span>
                    <span class="font-semibold text-[#1b1c1c] dark:text-white">{{ $latest->cleanliness_condition }}</span>
                </div>
                <div>
                    <span class="text-[10px] text-[#5c3f3f] dark:text-[#8c8282] block">Status Pengerjaan</span>
                    <span class="font-semibold text-[#1b1c1c] dark:text-white">{{ $latest->status }}</span>
                </div>
            </div>
            @if($latest->notes)
                <p class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5] mt-3 italic bg-white dark:bg-[#1f2020] p-3 rounded-lg border border-[#eae8e7] dark:border-[#333535]">
                    "{{ $latest->notes }}"
                </p>
            @endif
        </div>
    @endif

    <!-- Bottom Actions -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2">
        <a href="{{ route('petugas.scan') }}" class="btn-outline-neutral text-xs w-full sm:w-auto justify-center">
            <i class="fa-solid fa-camera mr-1.5 text-[#dc143c]"></i>
            <span>Pindai Ruangan Lain</span>
        </a>
        <a href="{{ route('monitoring.create', ['area_id' => $area->id]) }}" class="btn-primary text-xs w-full sm:w-auto justify-center">
            <i class="fa-solid fa-plus-circle mr-1.5"></i>
            <span>Lanjutkan ke Form Monitoring</span>
        </a>
    </div>

</div>
@endsection
