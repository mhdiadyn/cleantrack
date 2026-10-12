@extends('layouts.app')

@section('title', 'Detail Monitoring #' . $monitoring->id)
@section('header_title', 'Laporan Hasil Pemeriksaan')
@section('header_subtitle', 'Detail monitoring kebersihan dan evaluasi kondisi area')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css"/>
<style>
    #monitoringPannellumContainer {
        width: 100%;
        height: 420px;
        border-radius: 0.75rem;
        overflow: hidden;
    }
</style>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Top Action Bar -->
    <div class="flex items-center justify-between no-print">
        <a href="{{ Auth::user()->role === 'admin' ? route('admin.monitoring.index') : route('petugas.my-monitoring') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 font-bold text-xs shadow-xs transition-colors">
            <i class="fa-solid fa-arrow-left text-[10px]"></i>
            <span>Kembali ke Riwayat</span>
        </a>

        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-800 dark:bg-red-600 hover:bg-slate-900 dark:hover:bg-red-700 text-white font-bold text-xs shadow-xs transition-colors">
                <i class="fa-solid fa-print"></i>
                <span>Cetak Lembar Laporan</span>
            </button>

            @if(Auth::user()->role === 'admin' || Auth::id() === $monitoring->user_id)
                <a href="{{ route('monitoring.edit', $monitoring->id) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs shadow-xs transition-colors">
                    <i class="fa-solid fa-pen-to-square"></i>
                    <span>Edit</span>
                </a>
            @endif

            @if(Auth::user()->role === 'admin')
                <form action="{{ route('monitoring.destroy', $monitoring->id) }}" 
                      method="POST" 
                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus data monitoring ini?')" 
                      class="inline-block">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-rose-50 dark:bg-rose-950/60 hover:bg-rose-100 dark:hover:bg-rose-900/60 text-rose-700 dark:text-rose-300 font-bold text-xs border border-rose-200 dark:border-rose-800/60 transition-colors">
                        <i class="fa-solid fa-trash"></i>
                        <span>Hapus</span>
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Official Report Card Layout (Merah Putih Header) -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200 dark:border-slate-800 shadow-md overflow-hidden print:shadow-none print:border-0">
        
        <!-- Report Header / Letterhead -->
        <div class="p-6 sm:p-8 bg-gradient-to-r from-red-700 via-red-600 to-rose-600 text-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-red-500/40">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-white/20 border border-white/30 text-white flex items-center justify-center text-2xl shrink-0 backdrop-blur-sm">
                    <i class="fa-solid fa-file-shield"></i>
                </div>
                <div>
                    <span class="text-[11px] font-extrabold uppercase tracking-widest text-red-200">Lembar Berita Acara</span>
                    <h2 class="text-xl sm:text-2xl font-black">Laporan Pemeriksaan Kebersihan</h2>
                    <p class="text-xs text-red-100 mt-0.5">Sistem Monitoring Terintegrasi • CleanTrack</p>
                </div>
            </div>

            <div class="text-left sm:text-right bg-white/10 sm:bg-transparent p-3 sm:p-0 rounded-xl">
                <span class="text-[10px] font-bold text-red-200 uppercase tracking-wider block">Nomor Registrasi Laporan</span>
                <span class="font-mono text-base font-black text-white">#CT-{{ str_pad($monitoring->id, 5, '0', STR_PAD_LEFT) }}</span>
                <span class="text-[11px] text-red-200 block mt-0.5">{{ \Carbon\Carbon::parse($monitoring->check_date)->format('d F Y') }}</span>
            </div>
        </div>

        <!-- Report Body -->
        <div class="p-6 sm:p-8 space-y-8">
            
            <!-- Metadata Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50/80 dark:bg-slate-800/50 p-6 rounded-2xl border border-slate-100 dark:border-slate-800">
                
                <!-- Area & Lokasi -->
                <div class="space-y-3">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Nama Area</span>
                        <h4 class="text-base font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2 mt-0.5">
                            <i class="fa-solid fa-map-location-dot text-red-600 dark:text-red-400"></i>
                            <span>{{ $monitoring->area->name ?? '-' }} (Lantai {{ $monitoring->area->floor ?? 1 }})</span>
                        </h4>
                    </div>

                    <div>
                        <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Lokasi Fasilitas</span>
                        <p class="text-xs font-semibold text-slate-700 dark:text-slate-300 mt-0.5">{{ $monitoring->area->location ?? '-' }}</p>
                    </div>

                    @if($monitoring->area && $monitoring->area->description)
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Deskripsi Fasilitas</span>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $monitoring->area->description }}</p>
                        </div>
                    @endif
                </div>

                <!-- Petugas, Status & Kondisi -->
                <div class="space-y-3">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Petugas Pemeriksa</span>
                        <div class="flex items-center gap-2 mt-1">
                            <div class="w-7 h-7 rounded-full bg-red-100 dark:bg-red-900/60 text-red-800 dark:text-red-300 font-bold text-xs flex items-center justify-center">
                                {{ strtoupper(substr($monitoring->user->name ?? 'P', 0, 1)) }}
                            </div>
                            <span class="text-xs font-bold text-slate-900 dark:text-slate-100">{{ $monitoring->user->name ?? '-' }}</span>
                            <span class="text-[11px] text-slate-500 dark:text-slate-400">({{ $monitoring->user->email ?? '' }})</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 pt-1">
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block mb-1">Kondisi Kebersihan</span>
                            <x-badge-condition :condition="$monitoring->cleanliness_condition" />
                        </div>

                        <div>
                            <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block mb-1">Status Pengerjaan</span>
                            <x-badge-status :status="$monitoring->status" />
                        </div>
                    </div>

                    <div class="pt-2 border-t border-slate-200/60 dark:border-slate-700/60 grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Waktu & Jam Pembersihan</span>
                            <div class="text-xs font-bold text-slate-800 dark:text-slate-200 mt-1 flex items-center gap-1.5">
                                <i class="fa-regular fa-clock text-red-600 dark:text-red-400"></i>
                                <span>{{ $monitoring->formatted_start_time ?: substr($monitoring->check_time, 0, 5) }}</span>
                                <span class="text-slate-400">-</span>
                                <span>{{ $monitoring->formatted_end_time ?: substr($monitoring->check_time, 0, 5) }} WIB</span>
                            </div>
                        </div>

                        <div>
                            <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Durasi Pengerjaan</span>
                            <div class="text-xs font-bold text-emerald-600 dark:text-emerald-400 mt-1 flex items-center gap-1.5">
                                <i class="fa-solid fa-stopwatch"></i>
                                <span>{{ $monitoring->duration_text ?: 'Pemeriksaan Langsung' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Dokumentasi Foto (Sebelum & Sesudah) -->
            <div>
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-camera text-red-600 dark:text-red-400"></i>
                    <span>Dokumentasi Foto Kebersihan</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Foto Sebelum -->
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden bg-slate-50 dark:bg-slate-800/40">
                        <div class="px-4 py-2.5 bg-rose-50 dark:bg-rose-950/50 border-b border-rose-100 dark:border-rose-900/60 flex items-center justify-between">
                            <span class="text-xs font-bold text-rose-800 dark:text-rose-300 flex items-center gap-1.5">
                                <i class="fa-solid fa-circle-exclamation text-rose-500"></i>
                                Foto Sebelum Tindakan
                            </span>
                            @if($monitoring->photo_before_url)
                                <button type="button" 
                                        @click="$dispatch('open-image-modal', { src: '{{ $monitoring->photo_before_url }}', title: 'Foto Sebelum - {{ $monitoring->area->name ?? '' }}' })"
                                        class="text-[11px] font-bold text-rose-600 dark:text-rose-400 hover:text-rose-800 dark:hover:text-rose-200 flex items-center gap-1 no-print">
                                    <i class="fa-solid fa-magnifying-glass-plus"></i> Perbesar
                                </button>
                            @endif
                        </div>

                        <div class="p-3 flex items-center justify-center min-h-[220px]">
                            @if($monitoring->photo_before_url)
                                <img src="{{ $monitoring->photo_before_url }}" 
                                     alt="Foto Sebelum" 
                                     @click="$dispatch('open-image-modal', { src: '{{ $monitoring->photo_before_url }}', title: 'Foto Sebelum - {{ $monitoring->area->name ?? '' }}' })"
                                     class="max-h-64 w-full object-cover rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs cursor-pointer hover:opacity-90 transition-opacity">
                            @else
                                <div class="text-center text-slate-400 dark:text-slate-500 py-8">
                                    <i class="fa-solid fa-image-slash text-3xl mb-2 block"></i>
                                    <p class="text-xs">Tidak ada foto sebelum yang dilampirkan.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Foto Sesudah -->
                    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden bg-slate-50 dark:bg-slate-800/40">
                        <div class="px-4 py-2.5 bg-emerald-50 dark:bg-emerald-950/50 border-b border-emerald-100 dark:border-emerald-900/60 flex items-center justify-between">
                            <span class="text-xs font-bold text-emerald-800 dark:text-emerald-300 flex items-center gap-1.5">
                                <i class="fa-solid fa-circle-check text-emerald-600 dark:text-emerald-400"></i>
                                Foto Sesudah Tindakan
                            </span>
                            @if($monitoring->photo_after_url)
                                <button type="button" 
                                        @click="$dispatch('open-image-modal', { src: '{{ $monitoring->photo_after_url }}', title: 'Foto Sesudah - {{ $monitoring->area->name ?? '' }}' })"
                                        class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-200 flex items-center gap-1 no-print">
                                    <i class="fa-solid fa-magnifying-glass-plus"></i> Perbesar
                                </button>
                            @endif
                        </div>

                        <div class="p-3 flex items-center justify-center min-h-[220px]">
                            @if($monitoring->photo_after_url)
                                <img src="{{ $monitoring->photo_after_url }}" 
                                     alt="Foto Sesudah" 
                                     @click="$dispatch('open-image-modal', { src: '{{ $monitoring->photo_after_url }}', title: 'Foto Sesudah - {{ $monitoring->area->name ?? '' }}' })"
                                     class="max-h-64 w-full object-cover rounded-xl border border-slate-200 dark:border-slate-700 shadow-xs cursor-pointer hover:opacity-90 transition-opacity">
                            @else
                                <div class="text-center text-slate-400 dark:text-slate-500 py-8">
                                    <i class="fa-solid fa-image-slash text-3xl mb-2 block"></i>
                                    <p class="text-xs">Tidak ada foto sesudah yang dilampirkan.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>

            <!-- Virtual 360 / Room Photo View Box (If Available) -->
            @if($monitoring->photo_360 || ($monitoring->area && $monitoring->area->photo_360))
                @php
                    $isPano360 = $monitoring->is_360;
                    $roomPhotoUrl = $monitoring->photo_360_url ?: ($monitoring->area ? $monitoring->area->photo_360_url : '');
                @endphp
                @if($isPano360)
                    <!-- Panorama 360 Equirectangular View -->
                    <div class="rounded-2xl border border-purple-200 dark:border-purple-800/60 overflow-hidden bg-purple-50/40 dark:bg-purple-950/20 p-6 no-print">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-full bg-purple-600 text-white font-black text-[10px] uppercase">360° Interactive</span>
                                <h4 class="font-bold text-slate-900 dark:text-white text-sm">Dokumentasi Panorama 360° Ruangan</h4>
                            </div>
                            <span class="text-xs text-purple-600 dark:text-purple-300 font-mono">Format Equirectangular 360°</span>
                        </div>
                        <div id="monitoringPannellumContainer" style="width: 100%; height: 420px; border-radius: 0.75rem; overflow: hidden;"></div>
                    </div>
                @else
                    <!-- Standard High-Res Proportional Room Photo -->
                    <div class="rounded-2xl border border-sky-200 dark:border-sky-800/60 overflow-hidden bg-sky-50/40 dark:bg-sky-950/20 p-6 no-print">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-full bg-sky-600 text-white font-black text-[10px] uppercase">Foto Ruangan Standar HD</span>
                                <h4 class="font-bold text-slate-900 dark:text-white text-sm">Dokumentasi Visual Fasilitas Ruangan</h4>
                            </div>
                            <button type="button" 
                                    @click="$dispatch('open-image-modal', { src: '{{ $roomPhotoUrl }}', title: 'Foto Fasilitas Ruangan - {{ $monitoring->area->name ?? '' }}' })"
                                    class="text-xs font-bold text-sky-700 dark:text-sky-300 hover:text-sky-900 flex items-center gap-1 cursor-pointer">
                                <i class="fa-solid fa-expand"></i> Perbesar Foto
                            </button>
                        </div>
                        <div class="bg-black/90 rounded-xl overflow-hidden flex items-center justify-center p-3 min-h-[360px] max-h-[500px]">
                            <img src="{{ $roomPhotoUrl }}" 
                                 alt="Foto Fasilitas Ruangan" 
                                 @click="$dispatch('open-image-modal', { src: '{{ $roomPhotoUrl }}', title: 'Foto Fasilitas Ruangan - {{ $monitoring->area->name ?? '' }}' })"
                                 class="max-h-[460px] w-auto max-w-full object-contain rounded-lg shadow-lg cursor-pointer hover:opacity-95 transition-opacity">
                        </div>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-2 text-center">Ditampilkan dengan rasio asli proporsional tanpa distorsi lengkung bola panorama.</p>
                    </div>
                @endif
            @endif

            <!-- Catatan Evaluasi -->
            <div>
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider mb-2 flex items-center gap-2">
                    <i class="fa-solid fa-comment-dots text-red-600 dark:text-red-400"></i>
                    <span>Catatan Petugas & Tindakan Tambahan</span>
                </h3>
                <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 text-xs text-slate-700 dark:text-slate-300 leading-relaxed">
                    {{ $monitoring->notes ?: 'Tidak ada catatan tambahan untuk pemeriksaan ini.' }}
                </div>
            </div>

            <!-- Tanda Tangan Pengesahan (Print Friendly) -->
            <div class="pt-8 border-t border-slate-200 dark:border-slate-800 grid grid-cols-2 gap-8 text-center text-xs">
                <div>
                    <p class="text-slate-500 dark:text-slate-400">Petugas Pemeriksa,</p>
                    <div class="h-20 flex items-end justify-center">
                        <p class="font-bold text-slate-900 dark:text-slate-100 border-b border-slate-400 dark:border-slate-600 pb-1 px-8">{{ $monitoring->user->name ?? '............................' }}</p>
                    </div>
                    <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1">NIP/ID: {{ $monitoring->user->id ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-slate-500 dark:text-slate-400">Mengetahui (Supervisor / Admin),</p>
                    <div class="h-20 flex items-end justify-center">
                        <p class="font-bold text-slate-900 dark:text-slate-100 border-b border-slate-400 dark:border-slate-600 pb-1 px-8">Administrator Fasilitas</p>
                    </div>
                    <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1">CleanTrack Management</p>
                </div>
            </div>

        </div>

        <!-- Report Footer -->
        <div class="p-4 bg-slate-50 dark:bg-slate-800/40 border-t border-slate-100 dark:border-slate-800 text-center text-[11px] text-slate-400 dark:text-slate-500">
            Dicetak secara otomatis melalui CleanTrack • Sistem Monitoring Kebersihan Digital • {{ date('Y') }}
        </div>
    </div>

</div>
@endsection

@section('scripts')
@if(($monitoring->photo_360 || ($monitoring->area && $monitoring->area->photo_360)) && $monitoring->is_360)
<script src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let panoUrl = '{{ $monitoring->photo_360_url ?: ($monitoring->area ? $monitoring->area->photo_360_url : "") }}';
        if (!panoUrl || panoUrl.startsWith('sample:')) {
            panoUrl = 'https://pannellum.org/images/alma.jpg';
        }
        pannellum.viewer('monitoringPannellumContainer', {
            "type": "equirectangular",
            "panorama": panoUrl,
            "autoLoad": true,
            "autoRotate": -1.5,
            "compass": true
        });
    });
</script>
@endif
@endsection
