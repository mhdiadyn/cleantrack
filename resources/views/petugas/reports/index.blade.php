@extends('layouts.app')

@section('title', 'Laporan Kebersihan Petugas')
@section('header_title', 'Laporan Kebersihan Saya')
@section('header_subtitle', 'Rekapitulasi data pekerjaan kebersihan yang telah dilaporkan')

@section('content')
<div class="space-y-6">

    <!-- Filter Card -->
    <div class="card-clean p-6">
        <form method="GET" action="{{ route('petugas.reports.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                
                <!-- Tanggal Awal -->
                <div>
                    <label for="start_date" class="label-clean">Tanggal Awal</label>
                    <input type="date" 
                           id="start_date"
                           name="start_date" 
                           value="{{ request('start_date', $startDate) }}" 
                           class="input-clean text-xs">
                </div>

                <!-- Tanggal Akhir -->
                <div>
                    <label for="end_date" class="label-clean">Tanggal Akhir</label>
                    <input type="date" 
                           id="end_date"
                           name="end_date" 
                           value="{{ request('end_date', $endDate) }}" 
                           class="input-clean text-xs">
                </div>

                <!-- Filter Area -->
                <div>
                    <label for="area_id" class="label-clean">Area / Ruangan</label>
                    <select id="area_id" name="area_id" class="input-clean text-xs">
                        <option value="">Semua Area</option>
                        @foreach($areas as $area)
                            <option value="{{ $area->id }}" {{ (string) request('area_id') === (string) $area->id ? 'selected' : '' }}>
                                {{ $area->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Status -->
                <div>
                    <label for="status" class="label-clean">Status Pekerjaan</label>
                    <select id="status" name="status" class="input-clean text-xs">
                        <option value="">Semua Status</option>
                        @foreach(['Belum Diperiksa', 'Perlu Dibersihkan', 'Sedang Dibersihkan', 'Sudah Dibersihkan'] as $st)
                            <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Kondisi -->
                <div>
                    <label for="condition" class="label-clean">Kondisi Kebersihan</label>
                    <select id="condition" name="condition" class="input-clean text-xs">
                        <option value="">Semua Kondisi</option>
                        @foreach(['Bersih', 'Cukup Bersih', 'Kotor', 'Sangat Kotor'] as $cond)
                            <option value="{{ $cond }}" {{ request('condition') === $cond ? 'selected' : '' }}>{{ $cond }}</option>
                        @endforeach
                    </select>
                </div>

            </div>

            <!-- Action buttons & Filter Info -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-3 border-t border-[#eae8e7] dark:border-[#2e3030]">
                <div class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5] flex items-center gap-2">
                    <i class="fa-solid fa-circle-info text-[#dc143c]"></i>
                    @if(request()->filled('start_date') || request()->filled('end_date'))
                        <span>
                            Periode: <b class="text-[#1b1c1c] dark:text-white">{{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('d/m/Y') : 'Awal' }}</b> s/d <b class="text-[#1b1c1c] dark:text-white">{{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('d/m/Y') : 'Sekarang' }}</b>
                        </span>
                    @else
                        <span>Menampilkan seluruh arsip data pekerjaan saya</span>
                    @endif
                </div>

                <div class="flex items-center gap-2">
                    @if(request()->hasAny(['start_date', 'end_date', 'area_id', 'status', 'condition']))
                        <a href="{{ route('petugas.reports.index') }}" class="btn-outline-neutral text-xs">
                            <i class="fa-solid fa-rotate-left text-[11px]"></i>
                            <span>Reset</span>
                        </a>
                    @endif
                    <button type="submit" class="btn-primary text-xs">
                        <i class="fa-solid fa-filter text-xs"></i>
                        <span>Terapkan Filter</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- 4 Summary Metrics Cards -->
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
                <p class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5] mt-0.5">Total tugas yang tercatat</p>
            </div>
        </div>

        <!-- Pekerjaan Selesai -->
        <div class="card-clean card-clean-hover p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-[#5c3f3f] dark:text-[#c8c6c5] uppercase tracking-wider">Pekerjaan Selesai</span>
                <div class="w-9 h-9 rounded-xl bg-[#daffd5] text-[#006622] dark:bg-[#003912] dark:text-[#83fc8e] flex items-center justify-center text-sm">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="font-heading text-2xl font-semibold text-[#006622] dark:text-[#83fc8e]">{{ $totalSelesai }}</h3>
                <p class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5] mt-0.5">Sudah dibersihkan tuntas</p>
            </div>
        </div>

        <!-- Sedang Dikerjakan -->
        <div class="card-clean card-clean-hover p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-[#5c3f3f] dark:text-[#c8c6c5] uppercase tracking-wider">Dalam Proses</span>
                <div class="w-9 h-9 rounded-xl bg-[#e0f2fe] text-[#0284c7] dark:bg-[#082f49] dark:text-[#7dd3fc] flex items-center justify-center text-sm">
                    <i class="fa-solid fa-arrows-rotate"></i>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="font-heading text-2xl font-semibold text-[#0284c7] dark:text-[#7dd3fc]">{{ $totalProses }}</h3>
                <p class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5] mt-0.5">Sedang dalam pengerjaan</p>
            </div>
        </div>

        <!-- Perlu Tindakan / Belum Selesai -->
        <div class="card-clean card-clean-hover p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-[#5c3f3f] dark:text-[#c8c6c5] uppercase tracking-wider">Perlu Tindakan</span>
                <div class="w-9 h-9 rounded-xl bg-[#ffdad6] text-[#ba1a1a] dark:bg-[#410002] dark:text-[#ffb4ab] flex items-center justify-center text-sm">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="font-heading text-2xl font-semibold text-[#ba1a1a] dark:text-[#ffb4ab]">{{ $totalPerluTindakan }}</h3>
                <p class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5] mt-0.5">Menunggu / perlu tindakan</p>
            </div>
        </div>

    </div>

    <!-- Data Table Card -->
    <div class="card-clean overflow-hidden">
        <div class="p-5 border-b border-[#eae8e7] dark:border-[#2e3030] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h3 class="font-heading text-sm font-semibold text-[#1b1c1c] dark:text-white">Tabel Laporan Pemeriksaan</h3>
                <p class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5] mt-0.5">Daftar pemeriksaan kebersihan yang dilakukan oleh Anda</p>
            </div>
            <span class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5]">
                Menampilkan {{ $monitorings->count() }} dari {{ $monitorings->total() }} data
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-[#1b1c1c] dark:text-[#f2f0f0]">
                <thead class="bg-[#f5f3f3] dark:bg-[#262727] text-[#5c3f3f] dark:text-[#c8c6c5] font-semibold uppercase text-[11px] tracking-wider border-b border-[#eae8e7] dark:border-[#2e3030]">
                    <tr>
                        <th class="py-3 px-4 text-center w-12">No</th>
                        <th class="py-3 px-4">Tanggal & Jam</th>
                        <th class="py-3 px-4">Area & Lokasi</th>
                        <th class="py-3 px-4">Petugas</th>
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
                            <td class="py-3.5 px-4 text-[#5c3f3f] dark:text-[#c8c6c5]">
                                <div class="font-semibold text-[#1b1c1c] dark:text-white flex items-center gap-1.5">
                                    <i class="fa-regular fa-calendar text-[#dc143c] text-xs"></i>
                                    <span>{{ \Carbon\Carbon::parse($item->check_date)->format('d/m/Y') }}</span>
                                </div>
                                <div class="text-[10px] text-[#5c3f3f] dark:text-[#8c8282] mt-0.5 flex items-center gap-1">
                                    <i class="fa-regular fa-clock text-[9px]"></i>
                                    <span>{{ $item->formatted_start_time ?: substr($item->check_time, 0, 5) }}</span>
                                    <span>-</span>
                                    <span>{{ $item->formatted_end_time ?: substr($item->check_time, 0, 5) }}</span>
                                    @if($item->duration_text)
                                        &bull; <span class="text-[#006622] dark:text-[#83fc8e] font-semibold">{{ $item->duration_text }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-semibold text-[#1b1c1c] dark:text-white block">{{ $item->area->name ?? '-' }}</span>
                                <span class="text-[11px] text-[#5c3f3f] dark:text-[#c8c6c5]">
                                    {{ $item->area->location ?? '-' }} 
                                    @if($item->area && $item->area->floor)
                                        (Lt {{ $item->area->floor }})
                                    @endif
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-medium text-[#1b1c1c] dark:text-white">
                                <div class="flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-[#00822d]"></span>
                                    <span>{{ $item->user->name ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <x-badge-condition :condition="$item->cleanliness_condition" />
                            </td>
                            <td class="py-3.5 px-4">
                                <x-badge-status :status="$item->status" />
                            </td>
                            <td class="py-3.5 px-4 text-[#5c3f3f] dark:text-[#c8c6c5] max-w-xs truncate">
                                {{ $item->notes ?: '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('monitoring.show', $item->id) }}" 
                                   class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold text-[#dc143c] dark:text-[#ffb3b3] hover:bg-[#fff1f0] dark:hover:bg-[#3b0b14] transition-colors"
                                   title="Lihat Detail Pemeriksaan">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                    <span>Detail</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-[#5c3f3f] dark:text-[#c8c6c5]">
                                <div class="w-12 h-12 rounded-2xl bg-[#f5f3f3] dark:bg-[#262727] text-[#5c3f3f] dark:text-[#8c8282] flex items-center justify-center mx-auto mb-3 text-lg">
                                    <i class="fa-solid fa-file-circle-xmark"></i>
                                </div>
                                <h4 class="font-heading font-semibold text-sm text-[#1b1c1c] dark:text-white">Tidak Ada Data Laporan</h4>
                                <p class="text-xs text-[#5c3f3f] dark:text-[#8c8282] mt-1 max-w-md mx-auto">
                                    @if(request()->hasAny(['start_date', 'end_date', 'area_id', 'status', 'condition']))
                                        Tidak ditemukan data pekerjaan yang sesuai dengan kriteria filter yang Anda pilih. Coba ubah atau reset filter.
                                    @else
                                        Anda belum memiliki riwayat pemeriksaan kebersihan yang tercatat di sistem.
                                    @endif
                                </p>
                                @if(request()->hasAny(['start_date', 'end_date', 'area_id', 'status', 'condition']))
                                    <div class="mt-4">
                                        <a href="{{ route('petugas.reports.index') }}" class="btn-outline-neutral text-xs">
                                            <i class="fa-solid fa-rotate-left text-[11px]"></i>
                                            <span>Reset Filter</span>
                                        </a>
                                    </div>
                                @else
                                    <div class="mt-4">
                                        <a href="{{ route('monitoring.create') }}" class="btn-primary text-xs">
                                            <i class="fa-solid fa-plus text-xs"></i>
                                            <span>Mulai Pemeriksaan Sekarang</span>
                                        </a>
                                    </div>
                                @endif
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
