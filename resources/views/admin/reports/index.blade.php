@extends('layouts.app')

@section('title', 'Laporan Monitoring Kebersihan')
@section('header_title', 'Laporan & Rekapitulasi Kebersihan')
@section('header_subtitle', 'Analisis dan rekapitulasi data monitoring kebersihan seluruh area fasilitas')

@section('content')
<div class="space-y-6">

    <!-- Filter Card -->
    <div class="card-clean p-6">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                
                <!-- Tanggal Awal -->
                <div>
                    <label class="label-clean">Tanggal Awal</label>
                    <input type="date" 
                           name="start_date" 
                           value="{{ $startDate }}" 
                           class="input-clean text-xs">
                </div>

                <!-- Tanggal Akhir -->
                <div>
                    <label class="label-clean">Tanggal Akhir</label>
                    <input type="date" 
                           name="end_date" 
                           value="{{ $endDate }}" 
                           class="input-clean text-xs">
                </div>

                <!-- Filter Area -->
                <div>
                    <label class="label-clean">Filter Area</label>
                    <select name="area_id" class="input-clean text-xs">
                        <option value="">Semua Area</option>
                        @foreach($areas as $area)
                            <option value="{{ $area->id }}" {{ request('area_id') == $area->id ? 'selected' : '' }}>
                                {{ $area->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Petugas -->
                <div>
                    <label class="label-clean">Filter Petugas</label>
                    <select name="user_id" class="input-clean text-xs">
                        <option value="">Semua Petugas</option>
                        @foreach($petugasList as $p)
                            <option value="{{ $p->id }}" {{ request('user_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Status -->
                <div>
                    <label class="label-clean">Filter Status</label>
                    <select name="status" class="input-clean text-xs">
                        <option value="">Semua Status</option>
                        @foreach(['Belum Diperiksa', 'Perlu Dibersihkan', 'Sedang Dibersihkan', 'Sudah Dibersihkan'] as $st)
                            <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>

            </div>

            <!-- Action buttons -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-3 border-t border-[#eae8e7] dark:border-[#2e3030]">
                <span class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5]">
                    Periode: <b class="text-[#1b1c1c] dark:text-white">{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</b> s/d <b class="text-[#1b1c1c] dark:text-white">{{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</b>
                </span>

                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('admin.reports.index') }}" class="btn-outline-neutral text-xs">
                        Reset
                    </a>
                    <button type="submit" class="btn-primary text-xs">
                        <i class="fa-solid fa-filter text-xs"></i>
                        <span>Filter</span>
                    </button>
                    <a href="{{ route('admin.reports.excel', request()->query()) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-[#00822d] hover:bg-[#006622] text-white font-semibold text-xs transition-colors" title="Download Format Excel">
                        <i class="fa-solid fa-file-excel"></i>
                        <span>Excel</span>
                    </a>
                    <a href="{{ route('admin.reports.word', request()->query()) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-[#0284c7] hover:bg-[#0369a1] text-white font-semibold text-xs transition-colors" title="Download Format Word">
                        <i class="fa-solid fa-file-word"></i>
                        <span>Word</span>
                    </a>
                    <a href="{{ route('admin.reports.print', request()->query()) }}" target="_blank" class="btn-secondary text-xs">
                        <i class="fa-solid fa-print"></i>
                        <span>Cetak PDF</span>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- 4 Rekapitulasi Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Total Pemeriksaan -->
        <div class="card-clean card-clean-hover p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-[#5c3f3f] dark:text-[#c8c6c5] uppercase tracking-wider">Total Pemeriksaan</span>
                <div class="w-9 h-9 rounded-xl bg-[#fff1f0] text-[#dc143c] dark:bg-[#3b0b14] dark:text-[#ffb3b3] flex items-center justify-center text-sm">
                    <i class="fa-solid fa-clipboard-check"></i>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="font-heading text-2xl font-semibold text-[#1b1c1c] dark:text-white">{{ $totalPemeriksaan }}</h3>
                <p class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5] mt-0.5">Laporan pada periode ini</p>
            </div>
        </div>

        <!-- Jumlah Area Bersih -->
        <div class="card-clean card-clean-hover p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-[#5c3f3f] dark:text-[#c8c6c5] uppercase tracking-wider">Kondisi Bersih</span>
                <div class="w-9 h-9 rounded-xl bg-[#daffd5] text-[#006622] dark:bg-[#003912] dark:text-[#83fc8e] flex items-center justify-center text-sm">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="font-heading text-2xl font-semibold text-[#006622] dark:text-[#83fc8e]">{{ $totalBersih }}</h3>
                <p class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5] mt-0.5">Sesuai standar kebersihan</p>
            </div>
        </div>

        <!-- Jumlah Area Kotor -->
        <div class="card-clean card-clean-hover p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-[#5c3f3f] dark:text-[#c8c6c5] uppercase tracking-wider">Kondisi Kotor</span>
                <div class="w-9 h-9 rounded-xl bg-[#fef3c7] text-[#b45309] dark:bg-[#451a03] dark:text-[#fcd34d] flex items-center justify-center text-sm">
                    <i class="fa-solid fa-broom"></i>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="font-heading text-2xl font-semibold text-[#b45309] dark:text-[#fcd34d]">{{ $totalKotor }}</h3>
                <p class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5] mt-0.5">Kotor atau sangat kotor</p>
            </div>
        </div>

        <!-- Jumlah Area Perlu Dibersihkan -->
        <div class="card-clean card-clean-hover p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-[#5c3f3f] dark:text-[#c8c6c5] uppercase tracking-wider">Perlu Tindakan</span>
                <div class="w-9 h-9 rounded-xl bg-[#ffdad6] text-[#ba1a1a] dark:bg-[#410002] dark:text-[#ffb4ab] flex items-center justify-center text-sm">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="font-heading text-2xl font-semibold text-[#ba1a1a] dark:text-[#ffb4ab]">{{ $totalPerluDibersihkan }}</h3>
                <p class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5] mt-0.5">Perlu pembersihan segera</p>
            </div>
        </div>

    </div>

    <!-- Data Table -->
    <div class="card-clean overflow-hidden">
        <div class="p-5 border-b border-[#eae8e7] dark:border-[#2e3030] flex items-center justify-between">
            <h3 class="font-heading text-sm font-semibold text-[#1b1c1c] dark:text-white">Detail Rekapitulasi Monitoring</h3>
            <span class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5]">Menampilkan {{ $monitorings->count() }} dari {{ $monitorings->total() }} baris</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-[#1b1c1c] dark:text-[#f2f0f0]">
                <thead class="bg-[#f5f3f3] dark:bg-[#262727] text-[#5c3f3f] dark:text-[#c8c6c5] font-semibold uppercase text-[11px] tracking-wider border-b border-[#eae8e7] dark:border-[#2e3030]">
                    <tr>
                        <th class="py-3 px-4 text-center w-12">No</th>
                        <th class="py-3 px-4">Nama Area</th>
                        <th class="py-3 px-4">Lokasi & Lt</th>
                        <th class="py-3 px-4">Petugas</th>
                        <th class="py-3 px-4">Waktu Pengerjaan</th>
                        <th class="py-3 px-4">Kondisi</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Catatan</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#eae8e7] dark:divide-[#2e3030]">
                    @forelse($monitorings as $index => $item)
                        <tr class="hover:bg-[#fbf9f8] dark:hover:bg-[#262727]/60 transition-colors">
                            <td class="py-3.5 px-4 text-center font-medium text-[#5c3f3f] dark:text-[#8c8282]">
                                {{ $monitorings->firstItem() + $index }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-semibold text-[#1b1c1c] dark:text-white block">{{ $item->display_area_name }}</span>
                                <span class="text-[10px] font-mono font-semibold text-[#dc143c] dark:text-[#ffb3b3]">{{ $item->display_area_code }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-[#5c3f3f] dark:text-[#c8c6c5]">
                                {{ $item->display_area_location }}@if($item->area) <span class="text-[11px]">({{ $item->area->floor_label }})</span>@endif
                            </td>
                            <td class="py-3.5 px-4 font-medium text-[#1b1c1c] dark:text-white">
                                {{ $item->user->name ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-[#5c3f3f] dark:text-[#c8c6c5]">
                                <div class="flex items-center gap-1.5 font-semibold text-[#1b1c1c] dark:text-white">
                                    <i class="fa-regular fa-clock text-[#5c3f3f] dark:text-[#8c8282] text-[10px]"></i>
                                    <span>{{ $item->formatted_start_time ?: substr($item->check_time, 0, 5) }}</span>
                                    <span>-</span>
                                    <span>{{ $item->formatted_end_time ?: substr($item->check_time, 0, 5) }}</span>
                                </div>
                                <div class="text-[10px] text-[#5c3f3f] dark:text-[#8c8282] mt-0.5">
                                    {{ \Carbon\Carbon::parse($item->check_date)->format('d/m/Y') }}
                                    @if($item->duration_text)
                                        &bull; <span class="text-[#006622] dark:text-[#83fc8e] font-semibold">{{ $item->duration_text }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <x-badge-condition :condition="$item->cleanliness_condition" />
                            </td>
                            <td class="py-3.5 px-4">
                                <x-badge-status :status="$item->status" />
                            </td>
                            <td class="py-3.5 px-4 text-[#5c3f3f] dark:text-[#c8c6c5] max-w-xs truncate">
                                {{ $item->notes ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('monitoring.show', $item->id) }}" 
                                   class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold text-[#dc143c] dark:text-[#ffb3b3] hover:bg-[#fff1f0] dark:hover:bg-[#3b0b14] transition-colors">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                    <span>Detail</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-[#5c3f3f] dark:text-[#c8c6c5]">
                                Tidak ada data monitoring dalam periode filter yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($monitorings->hasPages())
            <div class="p-4 border-t border-[#eae8e7] dark:border-[#2e3030]">
                {{ $monitorings->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
