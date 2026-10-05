@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('header_title', 'Dashboard Administrator')
@section('header_subtitle', 'Ringkasan monitoring dan aktivitas kebersihan terkini')

@section('content')
<div class="space-y-6">

    <!-- Welcome Greeting Banner -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-slate-900 via-teal-950 to-slate-900 text-white p-6 sm:p-8 shadow-lg border border-teal-900/40">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-teal-500/20 text-teal-300 border border-teal-500/30 mb-3">
                    <span class="w-2 h-2 rounded-full bg-teal-400 animate-ping"></span>
                    Sistem Aktif & Terpantau
                </span>
                <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Selamat Datang, {{ Auth::user()->name }}!</h2>
                <p class="text-slate-300 text-xs sm:text-sm mt-1 max-w-2xl">
                    Pantau seluruh kondisi kebersihan area fasilitas secara real-time. Pastikan semua area tercatat dan memenuhi standar kebersihan.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('monitoring.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-teal-500 hover:bg-teal-600 text-white font-bold text-xs shadow-md shadow-teal-500/20 transition-all">
                    <i class="fa-solid fa-plus"></i>
                    <span>Catat Pemeriksaan</span>
                </a>
                <a href="{{ route('admin.reports.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-200 border border-slate-700 font-semibold text-xs transition-all">
                    <i class="fa-solid fa-print"></i>
                    <span>Lihat Laporan</span>
                </a>
            </div>
        </div>
        <!-- Decorative subtle pattern -->
        <div class="absolute right-0 top-0 translate-x-1/4 -translate-y-1/4 w-80 h-80 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>
    </div>

    <!-- 5 Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        
        <!-- 1. Total Area -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Area</span>
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-building"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-black text-slate-800">{{ $totalAreas }}</h3>
                <p class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                    <span class="text-blue-600 font-semibold">Area Aktif</span> terdaftar
                </p>
            </div>
        </div>

        <!-- 2. Area Sudah Bersih -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Sudah Bersih</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-black text-emerald-600">{{ $areaSudahBersih }}</h3>
                <p class="text-xs text-slate-500 mt-1">Area bersih hari ini</p>
            </div>
        </div>

        <!-- 3. Area Sedang Dibersihkan -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Sedang Dibersihkan</span>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-spinner fa-spin"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-black text-amber-600">{{ $areaSedangDibersihkan }}</h3>
                <p class="text-xs text-slate-500 mt-1">Dalam pengerjaan</p>
            </div>
        </div>

        <!-- 4. Area Perlu Dibersihkan -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Perlu Dibersihkan</span>
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-black text-rose-600">{{ $areaPerluDibersihkan }}</h3>
                <p class="text-xs text-slate-500 mt-1">Membutuhkan tindakan</p>
            </div>
        </div>

        <!-- 5. Total Pemeriksaan Hari Ini -->
        <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Pemeriksaan Hari Ini</span>
                <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-clipboard-list"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-black text-teal-600">{{ $totalChecksToday }}</h3>
                <p class="text-xs text-slate-500 mt-1">Laporan masuk hari ini</p>
            </div>
        </div>
    </div>

    <!-- Charts & Analytics Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Status Kebersihan Chart -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-800">Distribusi Status Monitoring</h3>
                    <p class="text-xs text-slate-500">Jumlah rekapitulasi data berdasarkan status kebersihan</p>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 bg-slate-100 rounded-lg text-slate-600">Grafik Status</span>
            </div>
            <div class="h-64 relative">
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        <!-- Kondisi Kebersihan Chart -->
        <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800">Kondisi Kebersihan</h3>
                        <p class="text-xs text-slate-500">Kategori penilaian kebersihan area</p>
                    </div>
                </div>
                <div class="h-48 relative flex items-center justify-center">
                    <canvas id="conditionChart"></canvas>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2 mt-4 pt-4 border-t border-slate-100 text-xs">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-md bg-teal-500"></span>
                    <span class="text-slate-600">Bersih: <b>{{ $conditionCounts['Bersih'] }}</b></span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-md bg-sky-500"></span>
                    <span class="text-slate-600">Cukup: <b>{{ $conditionCounts['Cukup Bersih'] }}</b></span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-md bg-amber-500"></span>
                    <span class="text-slate-600">Kotor: <b>{{ $conditionCounts['Kotor'] }}</b></span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-md bg-rose-500"></span>
                    <span class="text-slate-600">Sgt Kotor: <b>{{ $conditionCounts['Sangat Kotor'] }}</b></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Monitoring Terbaru Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 sm:px-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold text-slate-800">Monitoring Terbaru</h3>
                <p class="text-xs text-slate-500">10 data pemeriksaan kebersihan terakhir yang tercatat di sistem</p>
            </div>
            <a href="{{ route('admin.monitoring.index') }}" class="text-xs font-bold text-teal-600 hover:text-teal-700 flex items-center gap-1">
                <span>Lihat Semua Riwayat</span>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase text-[10px] tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-4 text-center w-12">No</th>
                        <th class="py-3.5 px-4">Area & Lokasi</th>
                        <th class="py-3.5 px-4">Petugas</th>
                        <th class="py-3.5 px-4">Kondisi</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4">Tanggal</th>
                        <th class="py-3.5 px-4">Jam</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentMonitorings as $index => $item)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-4 text-center font-medium text-slate-400">{{ $index + 1 }}</td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900">{{ $item->area->name ?? '-' }}</div>
                                <div class="text-[11px] text-slate-400">{{ $item->area->location ?? '-' }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-slate-200 text-slate-700 font-bold text-[10px] flex items-center justify-center">
                                        {{ strtoupper(substr($item->user->name ?? 'P', 0, 1)) }}
                                    </div>
                                    <span class="font-medium text-slate-800">{{ $item->user->name ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <x-badge-condition :condition="$item->cleanliness_condition" />
                            </td>
                            <td class="py-3.5 px-4">
                                <x-badge-status :status="$item->status" />
                            </td>
                            <td class="py-3.5 px-4 font-medium text-slate-600">
                                {{ \Carbon\Carbon::parse($item->check_date)->format('d/m/Y') }}
                            </td>
                            <td class="py-3.5 px-4 font-medium text-slate-600">
                                {{ substr($item->check_time, 0, 5) }} WIB
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
                            <td colspan="8" class="py-8 text-center text-slate-400">
                                <i class="fa-solid fa-clipboard-question text-3xl mb-2 block"></i>
                                Belum ada data pemeriksaan kebersihan hari ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Status Bar Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'bar',
            data: {
                labels: ['Sudah Dibersihkan', 'Sedang Dibersihkan', 'Perlu Dibersihkan', 'Belum Diperiksa'],
                datasets: [{
                    label: 'Jumlah Pemeriksaan',
                    data: [
                        {{ $statusCounts['Sudah Dibersihkan'] }},
                        {{ $statusCounts['Sedang Dibersihkan'] }},
                        {{ $statusCounts['Perlu Dibersihkan'] }},
                        {{ $statusCounts['Belum Diperiksa'] }}
                    ],
                    backgroundColor: [
                        'rgba(16, 185, 129, 0.85)',
                        'rgba(245, 158, 11, 0.85)',
                        'rgba(244, 63, 94, 0.85)',
                        'rgba(148, 163, 184, 0.85)'
                    ],
                    borderRadius: 8,
                    maxBarThickness: 45
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });

        // Condition Doughnut Chart
        const condCtx = document.getElementById('conditionChart').getContext('2d');
        new Chart(condCtx, {
            type: 'doughnut',
            data: {
                labels: ['Bersih', 'Cukup Bersih', 'Kotor', 'Sangat Kotor'],
                datasets: [{
                    data: [
                        {{ $conditionCounts['Bersih'] }},
                        {{ $conditionCounts['Cukup Bersih'] }},
                        {{ $conditionCounts['Kotor'] }},
                        {{ $conditionCounts['Sangat Kotor'] }}
                    ],
                    backgroundColor: [
                        '#14b8a6',
                        '#0ea5e9',
                        '#f59e0b',
                        '#f43f5e'
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                cutout: '70%'
            }
        });
    });
</script>
@endpush
@endsection
