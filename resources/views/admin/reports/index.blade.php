@extends('layouts.app')

@section('title', 'Laporan Monitoring Kebersihan')
@section('header_title', 'Laporan & Rekapitulasi Kebersihan')
@section('header_subtitle', 'Analisis dan rekapitulasi data monitoring kebersihan seluruh area fasilitas')

@section('content')
<div class="space-y-6">

    <!-- Filter Card -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                
                <!-- Tanggal Awal -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Tanggal Awal</label>
                    <input type="date" 
                           name="start_date" 
                           value="{{ $startDate }}" 
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>

                <!-- Tanggal Akhir -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Tanggal Akhir</label>
                    <input type="date" 
                           name="end_date" 
                           value="{{ $endDate }}" 
                           class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-red-500">
                </div>

                <!-- Filter Area -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Filter Area</label>
                    <select name="area_id" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-red-500">
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
                    <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Filter Petugas</label>
                    <select name="user_id" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-red-500">
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
                    <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Filter Status</label>
                    <select name="status" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-red-500">
                        <option value="">Semua Status</option>
                        @foreach(['Belum Diperiksa', 'Perlu Dibersihkan', 'Sedang Dibersihkan', 'Sudah Dibersihkan'] as $st)
                            <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>

            </div>

            <!-- Action buttons -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-3 border-t border-slate-100 dark:border-slate-800">
                <span class="text-xs text-slate-500 dark:text-slate-400">
                    Periode: <b class="text-slate-800 dark:text-slate-200">{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</b> s/d <b class="text-slate-800 dark:text-slate-200">{{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</b>
                </span>

                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('admin.reports.index') }}" class="px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl text-xs font-semibold transition-colors">
                        Reset
                    </a>
                    <button type="submit" class="px-4 py-2 bg-slate-800 dark:bg-slate-700 hover:bg-slate-900 dark:hover:bg-slate-600 text-white rounded-xl text-xs font-bold transition-colors flex items-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-filter"></i>
                        <span>Filter</span>
                    </button>
                    <a href="{{ route('admin.reports.excel', request()->query()) }}" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold shadow-md shadow-emerald-600/20 transition-all flex items-center gap-1.5" title="Download Format Excel">
                        <i class="fa-solid fa-file-excel"></i>
                        <span>Excel</span>
                    </a>
                    <a href="{{ route('admin.reports.word', request()->query()) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold shadow-md shadow-blue-600/20 transition-all flex items-center gap-1.5" title="Download Format Word">
                        <i class="fa-solid fa-file-word"></i>
                        <span>Word</span>
                    </a>
                    <a href="{{ route('admin.reports.print', request()->query()) }}" target="_blank" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-bold shadow-md shadow-red-500/20 transition-all flex items-center gap-1.5">
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
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Pemeriksaan</span>
                <div class="w-10 h-10 rounded-xl bg-red-50 dark:bg-red-950/60 text-red-600 dark:text-red-400 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-clipboard-check"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-black text-slate-900 dark:text-slate-100">{{ $totalPemeriksaan }}</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Laporan masuk pada periode ini</p>
            </div>
        </div>

        <!-- Jumlah Area Bersih -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Kondisi Bersih</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-sparkles"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ $totalBersih }}</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Sesuai standar kebersihan</p>
            </div>
        </div>

        <!-- Jumlah Area Kotor -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Kondisi Kotor</span>
                <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-broom"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-black text-amber-600 dark:text-amber-400">{{ $totalKotor }}</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Tercatat kotor / sangat kotor</p>
            </div>
        </div>

        <!-- Jumlah Area Perlu Dibersihkan -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Perlu Dibersihkan</span>
                <div class="w-10 h-10 rounded-xl bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-black text-rose-600 dark:text-rose-400">{{ $totalPerluDibersihkan }}</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Status perlu tindakan segera</p>
            </div>
        </div>

    </div>

    <!-- Data Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="p-5 sm:px-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Detail Rekapitulasi Monitoring</h3>
            <span class="text-xs text-slate-500 dark:text-slate-400">Menampilkan {{ $monitorings->count() }} dari {{ $monitorings->total() }} baris</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 font-bold uppercase text-[10px] tracking-wider border-b border-slate-100 dark:border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4 text-center w-12">No</th>
                        <th class="py-3.5 px-4">Nama Area</th>
                        <th class="py-3.5 px-4">Lokasi & Lt</th>
                        <th class="py-3.5 px-4">Petugas</th>
                        <th class="py-3.5 px-4">Waktu Pengerjaan</th>
                        <th class="py-3.5 px-4">Kondisi</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4">Catatan</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($monitorings as $index => $item)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="py-3.5 px-4 text-center font-medium text-slate-400 dark:text-slate-500">
                                {{ $monitorings->firstItem() + $index }}
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-900 dark:text-slate-100 block">{{ $item->area->name ?? '-' }}</span>
                                <span class="text-[10px] font-mono text-red-600 dark:text-red-400 font-semibold">{{ $item->area->code ?? '' }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 dark:text-slate-400">
                                {{ $item->area->location ?? '-' }} <span class="font-semibold text-slate-700 dark:text-slate-300">(Lt {{ $item->area->floor ?? '1' }})</span>
                            </td>
                            <td class="py-3.5 px-4 font-medium text-slate-800 dark:text-slate-200">
                                {{ $item->user->name ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-700 dark:text-slate-300">
                                <div class="flex items-center gap-1.5 font-bold">
                                    <i class="fa-regular fa-clock text-slate-400 text-[10px]"></i>
                                    <span>{{ $item->formatted_start_time ?: substr($item->check_time, 0, 5) }}</span>
                                    <span>-</span>
                                    <span>{{ $item->formatted_end_time ?: substr($item->check_time, 0, 5) }}</span>
                                </div>
                                <div class="text-[10px] text-slate-400 mt-0.5">
                                    {{ \Carbon\Carbon::parse($item->check_date)->format('d/m/Y') }}
                                    @if($item->duration_text)
                                        • <span class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ $item->duration_text }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <x-badge-condition :condition="$item->cleanliness_condition" />
                            </td>
                            <td class="py-3.5 px-4">
                                <x-badge-status :status="$item->status" />
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 dark:text-slate-400 max-w-xs truncate">
                                {{ $item->notes ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('monitoring.show', $item->id) }}" 
                                   class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-red-50 dark:bg-red-950/60 hover:bg-red-100 dark:hover:bg-red-900/60 text-red-700 dark:text-red-300 font-bold text-[11px] transition-colors">
                                    <i class="fa-solid fa-eye"></i>
                                    <span>Detail</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-slate-400 dark:text-slate-500">
                                Tidak ada data monitoring dalam periode filter yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($monitorings->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $monitorings->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
