@extends('layouts.app')

@section('title', 'Laporan Monitoring Kebersihan')
@section('header_title', 'Laporan & Rekapitulasi Kebersihan')
@section('header_subtitle', 'Analisis dan rekapitulasi data monitoring kebersihan seluruh area fasilitas')

@section('content')
<div class="space-y-6">

    <!-- Filter Card -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('admin.reports.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                
                <!-- Tanggal Awal -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Tanggal Awal</label>
                    <input type="date" 
                           name="start_date" 
                           value="{{ $startDate }}" 
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-teal-500">
                </div>

                <!-- Tanggal Akhir -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Tanggal Akhir</label>
                    <input type="date" 
                           name="end_date" 
                           value="{{ $endDate }}" 
                           class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-teal-500">
                </div>

                <!-- Filter Area -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Filter Area</label>
                    <select name="area_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-teal-500">
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
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Filter Petugas</label>
                    <select name="user_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-teal-500">
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
                    <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-1">Filter Status</label>
                    <select name="status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-teal-500">
                        <option value="">Semua Status</option>
                        @foreach(['Belum Diperiksa', 'Perlu Dibersihkan', 'Sedang Dibersihkan', 'Sudah Dibersihkan'] as $st)
                            <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>

            </div>

            <!-- Action buttons -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-3 border-t border-slate-100">
                <span class="text-xs text-slate-500">
                    Periode: <b class="text-slate-800">{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</b> s/d <b class="text-slate-800">{{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</b>
                </span>

                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.reports.index') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs font-semibold transition-colors">
                        Reset
                    </a>
                    <button type="submit" class="px-5 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-xl text-xs font-bold transition-colors flex items-center gap-1.5">
                        <i class="fa-solid fa-filter"></i>
                        <span>Filter Laporan</span>
                    </button>
                    <a href="{{ route('admin.reports.print', request()->query()) }}" target="_blank" class="px-5 py-2 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-bold shadow-md shadow-teal-500/20 transition-all flex items-center gap-2">
                        <i class="fa-solid fa-print"></i>
                        <span>Cetak Laporan</span>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- 4 Rekapitulasi Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Total Pemeriksaan -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Pemeriksaan</span>
                <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-clipboard-check"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-black text-slate-900">{{ $totalPemeriksaan }}</h3>
                <p class="text-xs text-slate-500 mt-1">Laporan masuk pada periode ini</p>
            </div>
        </div>

        <!-- Jumlah Area Bersih -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Kondisi Bersih</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-sparkles"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-black text-emerald-600">{{ $totalBersih }}</h3>
                <p class="text-xs text-slate-500 mt-1">Sesuai standar kebersihan</p>
            </div>
        </div>

        <!-- Jumlah Area Kotor -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Kondisi Kotor</span>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-broom"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-black text-amber-600">{{ $totalKotor }}</h3>
                <p class="text-xs text-slate-500 mt-1">Tercatat kotor / sangat kotor</p>
            </div>
        </div>

        <!-- Jumlah Area Perlu Dibersihkan -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Perlu Dibersihkan</span>
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-black text-rose-600">{{ $totalPerluDibersihkan }}</h3>
                <p class="text-xs text-slate-500 mt-1">Status perlu tindakan segera</p>
            </div>
        </div>

    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 sm:px-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-slate-800">Detail Rekapitulasi Monitoring</h3>
            <span class="text-xs text-slate-500">Menampilkan {{ $monitorings->count() }} dari {{ $monitorings->total() }} baris</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase text-[10px] tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-4 text-center w-12">No</th>
                        <th class="py-3.5 px-4">Nama Area</th>
                        <th class="py-3.5 px-4">Lokasi</th>
                        <th class="py-3.5 px-4">Petugas</th>
                        <th class="py-3.5 px-4">Kondisi</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4">Tanggal & Jam</th>
                        <th class="py-3.5 px-4">Catatan</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($monitorings as $index => $item)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-4 text-center font-medium text-slate-400">
                                {{ $monitorings->firstItem() + $index }}
                            </td>
                            <td class="py-3.5 px-4 font-bold text-slate-900">
                                {{ $item->area->name ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-600">
                                {{ $item->area->location ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 font-medium text-slate-800">
                                {{ $item->user->name ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4">
                                <x-badge-condition :condition="$item->cleanliness_condition" />
                            </td>
                            <td class="py-3.5 px-4">
                                <x-badge-status :status="$item->status" />
                            </td>
                            <td class="py-3.5 px-4 font-medium text-slate-600">
                                {{ \Carbon\Carbon::parse($item->check_date)->format('d/m/Y') }} • {{ substr($item->check_time, 0, 5) }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 max-w-xs truncate">
                                {{ $item->notes ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('monitoring.show', $item->id) }}" 
                                   class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-teal-50 hover:bg-teal-100 text-teal-700 font-bold text-[11px] transition-colors">
                                    <i class="fa-solid fa-eye"></i>
                                    <span>Detail</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-slate-400">
                                Tidak ada data monitoring dalam periode filter yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($monitorings->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $monitorings->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
