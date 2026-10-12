@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('header_title', 'Dashboard Administrator')
@section('header_subtitle', 'Ringkasan monitoring dan aktivitas kebersihan fasilitas')

@section('content')
<div class="space-y-6">

    <!-- Welcome Greeting Banner (Merah Putih Header) -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-red-700 via-red-600 to-rose-600 text-white p-6 sm:p-8 shadow-xl shadow-red-500/10">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-white/20 text-white border border-white/30 mb-3 backdrop-blur-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                    Sistem Monitoring Real-Time
                </span>
                <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Selamat Datang, {{ Auth::user()->name }}!</h2>
                <p class="text-red-100 text-xs sm:text-sm mt-1 max-w-2xl">
                    Pantau seluruh kondisi kebersihan area fasilitas secara visual di denah interaktif, cek foto 360°, dan kelola laporan kebersihan secara terpadu.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('floorplan.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white text-red-600 hover:bg-red-50 font-bold text-xs shadow-md transition-all hover:scale-105">
                    <i class="fa-solid fa-map-location-dot"></i>
                    <span>Buka Denah Gedung</span>
                </a>
                <a href="{{ route('gallery.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-red-800/80 hover:bg-red-900 text-white border border-red-400/40 font-semibold text-xs transition-all">
                    <i class="fa-solid fa-images"></i>
                    <span>Galeri & 360°</span>
                </a>
                <a href="{{ route('monitoring.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-black text-white font-bold text-xs shadow-md transition-all">
                    <i class="fa-solid fa-plus"></i>
                    <span>Catat Baru</span>
                </a>
            </div>
        </div>
        <!-- Decorative subtle pattern -->
        <div class="absolute right-0 top-0 translate-x-1/4 -translate-y-1/4 w-80 h-80 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>
    </div>

    <!-- 5 Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        
        <!-- 1. Total Area -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Area</span>
                <div class="w-10 h-10 rounded-xl bg-red-50 dark:bg-red-950/50 text-red-600 dark:text-red-400 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-building"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-black text-slate-800 dark:text-slate-100">{{ $totalAreas }}</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 flex items-center gap-1">
                    <span class="text-red-600 dark:text-red-400 font-semibold">Area Aktif</span> terdaftar
                </p>
            </div>
        </div>

        <!-- 2. Area Sudah Bersih -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Sudah Bersih</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ $areaSudahBersih }}</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Area bersih optimal</p>
            </div>
        </div>

        <!-- 3. Area Sedang Dibersihkan -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Sedang Dibersihkan</span>
                <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/50 text-amber-600 dark:text-amber-400 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-spinner fa-spin"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-black text-amber-600 dark:text-amber-400">{{ $areaSedangDibersihkan }}</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Dalam pengerjaan</p>
            </div>
        </div>

        <!-- 4. Area Perlu Dibersihkan -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Perlu Dibersihkan</span>
                <div class="w-10 h-10 rounded-xl bg-red-50 dark:bg-red-950/50 text-red-600 dark:text-red-400 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-black text-red-600 dark:text-red-400">{{ $areaPerluDibersihkan }}</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Membutuhkan tindakan</p>
            </div>
        </div>

        <!-- 5. Total Pemeriksaan Hari Ini -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs hover:shadow-md transition-all">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Laporan Hari Ini</span>
                <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 flex items-center justify-center text-sm font-bold">
                    <i class="fa-solid fa-clipboard-list"></i>
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-2xl font-black text-slate-900 dark:text-white">{{ $totalChecksToday }}</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Inspeksi tercatat hari ini</p>
            </div>
        </div>
    </div>

    <!-- Charts & Analytics Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Status Kebersihan Chart -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Distribusi Status Monitoring</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">Jumlah rekapitulasi data berdasarkan status kebersihan</p>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 bg-red-50 dark:bg-red-950/60 text-red-700 dark:text-red-300 rounded-lg">Grafik Status</span>
            </div>
            <div class="h-64 relative">
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        <!-- Kondisi Kebersihan Chart -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Kondisi Kebersihan</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Kategori penilaian kebersihan area</p>
                    </div>
                </div>
                <div class="h-48 relative flex items-center justify-center">
                    <canvas id="conditionChart"></canvas>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2 mt-4 pt-4 border-t border-slate-100 dark:border-slate-800 text-xs">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-md bg-emerald-500"></span>
                    <span class="text-slate-600 dark:text-slate-400">Bersih: <b class="text-slate-800 dark:text-slate-200">{{ $conditionCounts['Bersih'] }}</b></span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-md bg-amber-500"></span>
                    <span class="text-slate-600 dark:text-slate-400">Cukup: <b class="text-slate-800 dark:text-slate-200">{{ $conditionCounts['Cukup Bersih'] }}</b></span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-md bg-orange-500"></span>
                    <span class="text-slate-600 dark:text-slate-400">Kotor: <b class="text-slate-800 dark:text-slate-200">{{ $conditionCounts['Kotor'] }}</b></span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-md bg-red-600"></span>
                    <span class="text-slate-600 dark:text-slate-400">Sgt Kotor: <b class="text-slate-800 dark:text-slate-200">{{ $conditionCounts['Sangat Kotor'] }}</b></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Monitoring Terbaru Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="p-5 sm:px-6 border-b border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold text-slate-800 dark:text-slate-100">Monitoring Terbaru</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">10 data pemeriksaan kebersihan terakhir yang tercatat di sistem</p>
            </div>
            <a href="{{ route('admin.monitoring.index') }}" class="text-xs font-bold text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 flex items-center gap-1">
                <span>Lihat Semua Riwayat</span>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 font-bold uppercase text-[10px] tracking-wider border-b border-slate-100 dark:border-slate-800">
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
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($recentMonitorings as $index => $item)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="py-3.5 px-4 text-center font-medium text-slate-400 dark:text-slate-500">{{ $index + 1 }}</td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900 dark:text-slate-100">{{ $item->area->name ?? '-' }} (Lt {{ $item->area->floor ?? 1 }})</div>
                                <div class="text-[11px] text-slate-400 dark:text-slate-500">{{ $item->area->location ?? '-' }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 font-bold text-[10px] flex items-center justify-center">
                                        {{ strtoupper(substr($item->user->name ?? 'P', 0, 1)) }}
                                    </div>
                                    <span class="font-medium text-slate-800 dark:text-slate-200">{{ $item->user->name ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <x-badge-condition :condition="$item->cleanliness_condition" />
                            </td>
                            <td class="py-3.5 px-4">
                                <x-badge-status :status="$item->status" />
                            </td>
                            <td class="py-3.5 px-4 font-medium text-slate-600 dark:text-slate-400">
                                {{ \Carbon\Carbon::parse($item->check_date)->format('d/m/Y') }}
                            </td>
                            <td class="py-3.5 px-4 font-medium text-slate-600 dark:text-slate-400">
                                {{ substr($item->check_time, 0, 5) }} WIB
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
                            <td colspan="8" class="py-8 text-center text-slate-400 dark:text-slate-500">
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
                        'rgba(220, 38, 38, 0.85)',
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
                    x: {
                        ticks: { color: document.documentElement.classList.contains('dark') ? '#94a3b8' : '#64748b' },
                        grid: { color: document.documentElement.classList.contains('dark') ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)' }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { 
                            stepSize: 1,
                            color: document.documentElement.classList.contains('dark') ? '#94a3b8' : '#64748b'
                        },
                        grid: { color: document.documentElement.classList.contains('dark') ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)' }
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
                        '#10b981',
                        '#f59e0b',
                        '#ea580c',
                        '#dc2626'
                    ],
                    borderWidth: 2,
                    borderColor: document.documentElement.classList.contains('dark') ? '#0f172a' : '#ffffff'
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
