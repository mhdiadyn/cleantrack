@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('header_title', 'Dashboard Administrator')
@section('header_subtitle', 'Ringkasan monitoring dan aktivitas kebersihan fasilitas')

@section('content')
<div class="space-y-6">

    <!-- Welcome Greeting Card (Corporate Clean) -->
    <div class="bg-white dark:bg-[#1f2020] rounded-xl border border-[#eae8e7] dark:border-[#2e3030] p-6 shadow-[0px_4px_12px_rgba(0,0,0,0.05)] border-l-4 border-l-[#dc143c]">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full text-xs font-semibold bg-[#daffd5] text-[#006622] border border-[#b2e5be] dark:bg-[#003912] dark:text-[#83fc8e] dark:border-[#00531a] mb-2.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#00822d]"></span>
                    <span>Monitoring Real-Time</span>
                </div>
                <h2 class="font-heading text-xl sm:text-2xl font-semibold text-[#1b1c1c] dark:text-white">
                    Selamat Datang, {{ Auth::user()->name }}
                </h2>
                <p class="text-xs sm:text-sm text-[#5c3f3f] dark:text-[#c8c6c5] mt-1 max-w-2xl">
                    Lihat denah gedung dan dokumentasi foto ruangan, serta kelola laporan kebersihan harian.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('monitoring.create') }}" class="btn-primary">
                    <i class="fa-solid fa-plus text-xs"></i>
                    <span>Catat Baru</span>
                </a>
                <a href="{{ route('facility.index') }}" class="btn-secondary">
                    <i class="fa-solid fa-map-location-dot text-xs"></i>
                    <span>Denah & Galeri Ruangan</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 5 Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        
        <!-- 1. Total Area -->
        <div class="card-clean card-clean-hover p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-[#5c3f3f] dark:text-[#c8c6c5] uppercase tracking-wider">Total Area</span>
                <div class="w-9 h-9 rounded-xl bg-[#fff1f0] text-[#dc143c] dark:bg-[#3b0b14] dark:text-[#ffb3b3] flex items-center justify-center text-sm">
                    <i class="fa-solid fa-building"></i>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="font-heading text-2xl font-semibold text-[#1b1c1c] dark:text-white">{{ $totalAreas }}</h3>
                <p class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5] mt-0.5">Area aktif terdaftar</p>
            </div>
        </div>

        <!-- 2. Area Sudah Bersih -->
        <div class="card-clean card-clean-hover p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-[#5c3f3f] dark:text-[#c8c6c5] uppercase tracking-wider">Sudah Bersih</span>
                <div class="w-9 h-9 rounded-xl bg-[#daffd5] text-[#006622] dark:bg-[#003912] dark:text-[#83fc8e] flex items-center justify-center text-sm">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="font-heading text-2xl font-semibold text-[#006622] dark:text-[#83fc8e]">{{ $areaSudahBersih }}</h3>
                <p class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5] mt-0.5">Kondisi optimal</p>
            </div>
        </div>

        <!-- 3. Area Sedang Dibersihkan -->
        <div class="card-clean card-clean-hover p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-[#5c3f3f] dark:text-[#c8c6c5] uppercase tracking-wider">Sedang Dibersihkan</span>
                <div class="w-9 h-9 rounded-xl bg-[#e0f2fe] text-[#0369a1] dark:bg-[#082f49] dark:text-[#7dd3fc] flex items-center justify-center text-sm">
                    <i class="fa-solid fa-arrows-rotate"></i>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="font-heading text-2xl font-semibold text-[#0369a1] dark:text-[#7dd3fc]">{{ $areaSedangDibersihkan }}</h3>
                <p class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5] mt-0.5">Dalam pengerjaan</p>
            </div>
        </div>

        <!-- 4. Area Perlu Dibersihkan -->
        <div class="card-clean card-clean-hover p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-[#5c3f3f] dark:text-[#c8c6c5] uppercase tracking-wider">Perlu Tindakan</span>
                <div class="w-9 h-9 rounded-xl bg-[#ffdad6] text-[#ba1a1a] dark:bg-[#410002] dark:text-[#ffb4ab] flex items-center justify-center text-sm">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="font-heading text-2xl font-semibold text-[#ba1a1a] dark:text-[#ffb4ab]">{{ $areaPerluDibersihkan }}</h3>
                <p class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5] mt-0.5">Perlu dibersihkan</p>
            </div>
        </div>

        <!-- 5. Total Pemeriksaan Hari Ini -->
        <div class="card-clean card-clean-hover p-5 flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-semibold text-[#5c3f3f] dark:text-[#c8c6c5] uppercase tracking-wider">Laporan Hari Ini</span>
                <div class="w-9 h-9 rounded-xl bg-[#f5f3f3] text-[#5c3f3f] dark:bg-[#262727] dark:text-[#c8c6c5] flex items-center justify-center text-sm">
                    <i class="fa-solid fa-clipboard-list"></i>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="font-heading text-2xl font-semibold text-[#1b1c1c] dark:text-white">{{ $totalChecksToday }}</h3>
                <p class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5] mt-0.5">Inspeksi tercatat</p>
            </div>
        </div>
    </div>

    <!-- Charts & Analytics Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Status Kebersihan Chart -->
        <div class="lg:col-span-2 card-clean p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="font-heading text-sm font-semibold text-[#1b1c1c] dark:text-white">Distribusi Status Monitoring</h3>
                    <p class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5]">Jumlah pemeriksaan kebersihan berdasarkan status operasional</p>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 bg-[#fff1f0] dark:bg-[#3b0b14] text-[#dc143c] dark:text-[#ffb3b3] rounded-lg">
                    Status
                </span>
            </div>
            <div class="h-64 relative">
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        <!-- Kondisi Kebersihan Chart -->
        <div class="card-clean p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="font-heading text-sm font-semibold text-[#1b1c1c] dark:text-white">Kondisi Kebersihan</h3>
                        <p class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5]">Penilaian fisik hasil pemeriksaan</p>
                    </div>
                </div>
                <div class="h-44 relative flex items-center justify-center">
                    <canvas id="conditionChart"></canvas>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2.5 mt-4 pt-4 border-t border-[#eae8e7] dark:border-[#2e3030] text-xs">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#00822d]"></span>
                    <span class="text-[#5c3f3f] dark:text-[#c8c6c5]">Bersih: <b class="text-[#1b1c1c] dark:text-white">{{ $conditionCounts['Bersih'] }}</b></span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#0284c7]"></span>
                    <span class="text-[#5c3f3f] dark:text-[#c8c6c5]">Cukup: <b class="text-[#1b1c1c] dark:text-white">{{ $conditionCounts['Cukup Bersih'] }}</b></span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#d97706]"></span>
                    <span class="text-[#5c3f3f] dark:text-[#c8c6c5]">Kotor: <b class="text-[#1b1c1c] dark:text-white">{{ $conditionCounts['Kotor'] }}</b></span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-[#dc143c]"></span>
                    <span class="text-[#5c3f3f] dark:text-[#c8c6c5]">Sgt Kotor: <b class="text-[#1b1c1c] dark:text-white">{{ $conditionCounts['Sangat Kotor'] }}</b></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Monitoring Terbaru Table -->
    <div class="card-clean overflow-hidden">
        <div class="p-5 border-b border-[#eae8e7] dark:border-[#2e3030] flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h3 class="font-heading text-sm font-semibold text-[#1b1c1c] dark:text-white">Monitoring Terbaru</h3>
                <p class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5]">10 catatan pemeriksaan kebersihan terakhir yang masuk</p>
            </div>
            <a href="{{ route('admin.monitoring.index') }}" class="text-xs font-semibold text-[#dc143c] dark:text-[#ffb3b3] hover:underline flex items-center gap-1">
                <span>Lihat Semua Riwayat</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-[#1b1c1c] dark:text-[#f2f0f0]">
                <thead class="bg-[#f5f3f3] dark:bg-[#262727] text-[#5c3f3f] dark:text-[#c8c6c5] font-semibold uppercase text-[11px] tracking-wider border-b border-[#eae8e7] dark:border-[#2e3030]">
                    <tr>
                        <th class="py-3 px-4 text-center w-12">No</th>
                        <th class="py-3 px-4">Area & Lokasi</th>
                        <th class="py-3 px-4">Petugas</th>
                        <th class="py-3 px-4">Kondisi</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">Jam</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#eae8e7] dark:divide-[#2e3030]">
                    @forelse($recentMonitorings as $index => $item)
                        <tr class="hover:bg-[#fbf9f8] dark:hover:bg-[#262727]/60 transition-colors">
                            <td class="py-3.5 px-4 text-center font-medium text-[#5c3f3f] dark:text-[#8c8282]">{{ $index + 1 }}</td>
                            <td class="py-3.5 px-4">
                                <div class="font-semibold text-[#1b1c1c] dark:text-white">{{ $item->display_area_name }}@if($item->area) (Lt {{ $item->area->floor }})@endif</div>
                                <div class="text-[11px] text-[#5c3f3f] dark:text-[#c8c6c5]">{{ $item->display_area_location }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-lg bg-[#f5f3f3] dark:bg-[#333535] text-[#5c3f3f] dark:text-[#c8c6c5] font-semibold text-[10px] flex items-center justify-center">
                                        {{ strtoupper(substr($item->user->name ?? 'P', 0, 1)) }}
                                    </div>
                                    <span class="font-medium text-[#1b1c1c] dark:text-white">{{ $item->user->name ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <x-badge-condition :condition="$item->cleanliness_condition" />
                            </td>
                            <td class="py-3.5 px-4">
                                <x-badge-status :status="$item->status" />
                            </td>
                            <td class="py-3.5 px-4 text-[#5c3f3f] dark:text-[#c8c6c5]">
                                {{ \Carbon\Carbon::parse($item->check_date)->format('d/m/Y') }}
                            </td>
                            <td class="py-3.5 px-4 text-[#5c3f3f] dark:text-[#c8c6c5]">
                                {{ substr($item->check_time, 0, 5) }} WIB
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
                            <td colspan="8" class="py-10 text-center text-[#5c3f3f] dark:text-[#c8c6c5]">
                                <i class="fa-regular fa-clipboard text-3xl mb-2 block opacity-40"></i>
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
        const isDark = document.documentElement.classList.contains('dark');
        const textColor = isDark ? '#c8c6c5' : '#5c3f3f';
        const gridColor = isDark ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.05)';

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
                        '#00822d',
                        '#0284c7',
                        '#d97706',
                        '#916f6e'
                    ],
                    borderRadius: 8,
                    maxBarThickness: 40
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
                        ticks: { color: textColor, font: { family: 'Inter', size: 11 } },
                        grid: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1, color: textColor, font: { family: 'Inter', size: 11 } },
                        grid: { color: gridColor }
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
                        '#00822d',
                        '#0284c7',
                        '#d97706',
                        '#dc143c'
                    ],
                    borderWidth: 2,
                    borderColor: isDark ? '#1f2020' : '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                cutout: '72%'
            }
        });
    });
</script>
@endpush
@endsection
