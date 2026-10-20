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
        border-radius: 12px;
        overflow: hidden;
    }
</style>
@endsection

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Top Action Bar -->
    <div class="flex items-center justify-between no-print">
        <a href="{{ Auth::user()->role === 'admin' ? route('admin.monitoring.index') : route('petugas.my-monitoring') }}" 
           class="btn-outline-neutral text-xs">
            <i class="fa-solid fa-arrow-left text-[10px]"></i>
            <span>Kembali ke Riwayat</span>
        </a>

        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="btn-outline-neutral text-xs">
                <i class="fa-solid fa-print"></i>
                <span>Cetak Lembar Laporan</span>
            </button>

            @if(Auth::user()->role === 'admin' || Auth::id() === $monitoring->user_id)
                <a href="{{ route('monitoring.edit', $monitoring->id) }}" class="btn-secondary text-xs">
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
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-[#ffdad6] hover:bg-[#ffb4ab] text-[#ba1a1a] dark:bg-[#410002] dark:text-[#ffb4ab] font-semibold text-xs border border-[#ffb4ab] dark:border-[#93000a] transition-colors cursor-pointer">
                        <i class="fa-solid fa-trash"></i>
                        <span>Hapus</span>
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Official Report Card Layout (Technolife Corporate Clean) -->
    <div class="card-clean overflow-hidden print:shadow-none print:border-0">
        
        <!-- Report Header / Letterhead (Solid Crimson - No Gradient) -->
        <div class="p-6 sm:p-8 bg-[#dc143c] text-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-white/20 text-white flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-file-shield"></i>
                </div>
                <div>
                    <span class="text-[10px] font-semibold uppercase tracking-wider text-white/80">Lembar Berita Acara</span>
                    <h2 class="font-heading text-xl sm:text-2xl font-semibold">Laporan Pemeriksaan Kebersihan</h2>
                    <p class="text-xs text-white/80 mt-0.5">Sistem Monitoring Terintegrasi &bull; CleanTrack</p>
                </div>
            </div>

            <div class="text-left sm:text-right bg-white/10 sm:bg-transparent p-3 sm:p-0 rounded-xl">
                <span class="text-[10px] font-semibold text-white/80 uppercase tracking-wider block">Nomor Registrasi Laporan</span>
                <span class="font-mono text-base font-bold text-white">#CT-{{ str_pad($monitoring->id, 5, '0', STR_PAD_LEFT) }}</span>
                <span class="text-[11px] text-white/80 block mt-0.5">{{ \Carbon\Carbon::parse($monitoring->check_date)->format('d F Y') }}</span>
            </div>
        </div>

        <!-- Report Body -->
        <div class="p-6 sm:p-8 space-y-8">
            
            <!-- Metadata Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-[#f5f3f3] dark:bg-[#262727] p-6 rounded-xl border border-[#eae8e7] dark:border-[#333535]">
                
                <!-- Area & Lokasi -->
                <div class="space-y-3">
                    <div>
                        <span class="text-[10px] font-semibold text-[#5c3f3f] dark:text-[#8c8282] uppercase tracking-wider block">Nama Area</span>
                        <h4 class="font-heading text-base font-semibold text-[#1b1c1c] dark:text-white flex items-center gap-2 mt-0.5">
                            <i class="fa-solid fa-map-location-dot text-[#dc143c] dark:text-[#ffb3b3]"></i>
                            <span>{{ $monitoring->area->name ?? '-' }} (Lantai {{ $monitoring->area->floor ?? 1 }})</span>
                        </h4>
                    </div>

                    <div>
                        <span class="text-[10px] font-semibold text-[#5c3f3f] dark:text-[#8c8282] uppercase tracking-wider block">Lokasi Fasilitas</span>
                        <p class="text-xs font-medium text-[#1b1c1c] dark:text-white mt-0.5">{{ $monitoring->area->location ?? '-' }}</p>
                    </div>

                    @if($monitoring->area && $monitoring->area->description)
                        <div>
                            <span class="text-[10px] font-semibold text-[#5c3f3f] dark:text-[#8c8282] uppercase tracking-wider block">Deskripsi Fasilitas</span>
                            <p class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5] mt-0.5">{{ $monitoring->area->description }}</p>
                        </div>
                    @endif
                </div>

                <!-- Petugas, Status & Kondisi -->
                <div class="space-y-3">
                    <div>
                        <span class="text-[10px] font-semibold text-[#5c3f3f] dark:text-[#8c8282] uppercase tracking-wider block">Petugas Pemeriksa</span>
                        <div class="flex items-center gap-2 mt-1">
                            <div class="w-7 h-7 rounded-lg bg-[#fff1f0] text-[#dc143c] dark:bg-[#3b0b14] dark:text-[#ffb3b3] font-bold text-xs flex items-center justify-center">
                                {{ strtoupper(substr($monitoring->user->name ?? 'P', 0, 1)) }}
                            </div>
                            <span class="text-xs font-semibold text-[#1b1c1c] dark:text-white">{{ $monitoring->user->name ?? '-' }}</span>
                            <span class="text-[11px] text-[#5c3f3f] dark:text-[#c8c6c5]">({{ $monitoring->user->email ?? '' }})</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 pt-1">
                        <div>
                            <span class="text-[10px] font-semibold text-[#5c3f3f] dark:text-[#8c8282] uppercase tracking-wider block mb-1">Kondisi Kebersihan</span>
                            <x-badge-condition :condition="$monitoring->cleanliness_condition" />
                        </div>

                        <div>
                            <span class="text-[10px] font-semibold text-[#5c3f3f] dark:text-[#8c8282] uppercase tracking-wider block mb-1">Status Pengerjaan</span>
                            <x-badge-status :status="$monitoring->status" />
                        </div>
                    </div>

                    <div class="pt-2 border-t border-[#eae8e7] dark:border-[#333535] grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <span class="text-[10px] font-semibold text-[#5c3f3f] dark:text-[#8c8282] uppercase tracking-wider block">Waktu Pembersihan</span>
                            <div class="text-xs font-semibold text-[#1b1c1c] dark:text-white mt-1 flex items-center gap-1.5">
                                <i class="fa-regular fa-clock text-[#dc143c] dark:text-[#ffb3b3]"></i>
                                <span>{{ $monitoring->formatted_start_time ?: substr($monitoring->check_time, 0, 5) }}</span>
                                <span class="text-[#5c3f3f]">-</span>
                                <span>{{ $monitoring->formatted_end_time ?: substr($monitoring->check_time, 0, 5) }} WIB</span>
                            </div>
                        </div>

                        <div>
                            <span class="text-[10px] font-semibold text-[#5c3f3f] dark:text-[#8c8282] uppercase tracking-wider block">Durasi Pengerjaan</span>
                            <div class="text-xs font-semibold text-[#006622] dark:text-[#83fc8e] mt-1 flex items-center gap-1.5">
                                <i class="fa-solid fa-stopwatch"></i>
                                <span>{{ $monitoring->duration_text ?: 'Pemeriksaan Langsung' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Dokumentasi Foto (Sebelum & Sesudah) -->
            <div>
                <h3 class="font-heading text-sm font-semibold text-[#1b1c1c] dark:text-white uppercase tracking-wider mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-camera text-[#dc143c]"></i>
                    <span>Dokumentasi Foto Kebersihan</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Foto Sebelum -->
                    <div class="rounded-xl border border-[#eae8e7] dark:border-[#333535] overflow-hidden bg-[#f5f3f3] dark:bg-[#262727]">
                        <div class="px-4 py-2.5 bg-[#fff1f0] dark:bg-[#3b0b14] border-b border-[#fecdd3] dark:border-[#831843] flex items-center justify-between">
                            <span class="text-xs font-semibold text-[#dc143c] dark:text-[#ffb3b3] flex items-center gap-1.5">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                Foto Sebelum Tindakan
                            </span>
                            @if($monitoring->photo_before_url)
                                <button type="button" 
                                        @click="$dispatch('open-image-modal', { src: '{{ $monitoring->photo_before_url }}', title: 'Foto Sebelum - {{ $monitoring->area->name ?? '' }}' })"
                                        class="text-[11px] font-semibold text-[#dc143c] dark:text-[#ffb3b3] hover:underline flex items-center gap-1 no-print cursor-pointer">
                                    <i class="fa-solid fa-magnifying-glass-plus"></i> Perbesar
                                </button>
                            @endif
                        </div>

                        <div class="p-3 flex items-center justify-center min-h-[220px]">
                            @if($monitoring->photo_before_url)
                                <img src="{{ $monitoring->photo_before_url }}" 
                                     alt="Foto Sebelum" 
                                     @click="$dispatch('open-image-modal', { src: '{{ $monitoring->photo_before_url }}', title: 'Foto Sebelum - {{ $monitoring->area->name ?? '' }}' })"
                                     class="max-h-64 w-full object-cover rounded-lg border border-[#eae8e7] dark:border-[#333535] cursor-pointer hover:opacity-90 transition-opacity">
                            @else
                                <div class="text-center text-[#5c3f3f] dark:text-[#8c8282] py-8">
                                    <i class="fa-regular fa-image text-3xl mb-2 block opacity-40"></i>
                                    <p class="text-xs">Tidak ada foto sebelum yang dilampirkan.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Foto Sesudah -->
                    <div class="rounded-xl border border-[#eae8e7] dark:border-[#333535] overflow-hidden bg-[#f5f3f3] dark:bg-[#262727]">
                        <div class="px-4 py-2.5 bg-[#daffd5] dark:bg-[#003912] border-b border-[#b2e5be] dark:border-[#00531a] flex items-center justify-between">
                            <span class="text-xs font-semibold text-[#006622] dark:text-[#83fc8e] flex items-center gap-1.5">
                                <i class="fa-solid fa-circle-check"></i>
                                Foto Sesudah Tindakan
                            </span>
                            @if($monitoring->photo_after_url)
                                <button type="button" 
                                        @click="$dispatch('open-image-modal', { src: '{{ $monitoring->photo_after_url }}', title: 'Foto Sesudah - {{ $monitoring->area->name ?? '' }}' })"
                                        class="text-[11px] font-semibold text-[#006622] dark:text-[#83fc8e] hover:underline flex items-center gap-1 no-print cursor-pointer">
                                    <i class="fa-solid fa-magnifying-glass-plus"></i> Perbesar
                                </button>
                            @endif
                        </div>

                        <div class="p-3 flex items-center justify-center min-h-[220px]">
                            @if($monitoring->photo_after_url)
                                <img src="{{ $monitoring->photo_after_url }}" 
                                     alt="Foto Sesudah" 
                                     @click="$dispatch('open-image-modal', { src: '{{ $monitoring->photo_after_url }}', title: 'Foto Sesudah - {{ $monitoring->area->name ?? '' }}' })"
                                     class="max-h-64 w-full object-cover rounded-lg border border-[#eae8e7] dark:border-[#333535] cursor-pointer hover:opacity-90 transition-opacity">
                            @else
                                <div class="text-center text-[#5c3f3f] dark:text-[#8c8282] py-8">
                                    <i class="fa-regular fa-image text-3xl mb-2 block opacity-40"></i>
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
                    <div class="rounded-xl border border-[#eae8e7] dark:border-[#333535] overflow-hidden bg-[#f5f3f3] dark:bg-[#262727] p-5 no-print">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded-full bg-[#dc143c] text-white font-semibold text-[10px] uppercase">360° Interactive</span>
                                <h4 class="font-heading font-semibold text-[#1b1c1c] dark:text-white text-sm">Dokumentasi Panorama 360° Ruangan</h4>
                            </div>
                            <span class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5] font-mono">Format Equirectangular 360°</span>
                        </div>
                        <div id="monitoringPannellumContainer" style="width: 100%; height: 420px; border-radius: 12px; overflow: hidden;"></div>
                    </div>
                @else
                    <!-- Standard High-Res Proportional Room Photo -->
                    <div class="rounded-xl border border-[#eae8e7] dark:border-[#333535] overflow-hidden bg-[#f5f3f3] dark:bg-[#262727] p-5 no-print">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded-full bg-[#0284c7] text-white font-semibold text-[10px] uppercase">Foto Ruangan Standar HD</span>
                                <h4 class="font-heading font-semibold text-[#1b1c1c] dark:text-white text-sm">Dokumentasi Visual Fasilitas Ruangan</h4>
                            </div>
                            <button type="button" 
                                    @click="$dispatch('open-image-modal', { src: '{{ $roomPhotoUrl }}', title: 'Foto Fasilitas Ruangan - {{ $monitoring->area->name ?? '' }}' })"
                                    class="text-xs font-semibold text-[#0284c7] dark:text-[#7dd3fc] hover:underline flex items-center gap-1 cursor-pointer">
                                <i class="fa-solid fa-expand"></i> Perbesar Foto
                            </button>
                        </div>
                        <div class="bg-black/90 rounded-lg overflow-hidden flex items-center justify-center p-3 min-h-[360px] max-h-[500px]">
                            <img src="{{ $roomPhotoUrl }}" 
                                 alt="Foto Fasilitas Ruangan" 
                                 @click="$dispatch('open-image-modal', { src: '{{ $roomPhotoUrl }}', title: 'Foto Fasilitas Ruangan - {{ $monitoring->area->name ?? '' }}' })"
                                 class="max-h-[460px] w-auto max-w-full object-contain rounded-lg cursor-pointer hover:opacity-95 transition-opacity">
                        </div>
                        <p class="text-[11px] text-[#5c3f3f] dark:text-[#8c8282] mt-2 text-center">Ditampilkan dengan rasio asli proporsional tanpa distorsi lengkung bola panorama.</p>
                    </div>
                @endif
            @endif

            <!-- Catatan Evaluasi -->
            <div>
                <h3 class="font-heading text-sm font-semibold text-[#1b1c1c] dark:text-white uppercase tracking-wider mb-2 flex items-center gap-2">
                    <i class="fa-solid fa-comment-dots text-[#dc143c]"></i>
                    <span>Catatan Petugas & Tindakan Tambahan</span>
                </h3>
                <div class="p-4 rounded-xl bg-[#f5f3f3] dark:bg-[#262727] border border-[#eae8e7] dark:border-[#333535] text-xs text-[#1b1c1c] dark:text-[#f2f0f0] leading-relaxed">
                    {{ $monitoring->notes ?: 'Tidak ada catatan tambahan untuk pemeriksaan ini.' }}
                </div>
            </div>

            <!-- Tanda Tangan Pengesahan (Print Friendly) -->
            <div class="pt-8 border-t border-[#eae8e7] dark:border-[#333535] grid grid-cols-2 gap-8 text-center text-xs">
                <div>
                    <p class="text-[#5c3f3f] dark:text-[#c8c6c5]">Petugas Pemeriksa,</p>
                    <div class="h-20 flex items-end justify-center">
                        <p class="font-semibold text-[#1b1c1c] dark:text-white border-b border-[#eae8e7] dark:border-[#333535] pb-1 px-8">{{ $monitoring->user->name ?? '............................' }}</p>
                    </div>
                    <p class="text-[10px] text-[#5c3f3f] dark:text-[#8c8282] mt-1">NIP/ID: {{ $monitoring->user->id ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-[#5c3f3f] dark:text-[#c8c6c5]">Mengetahui (Supervisor / Admin),</p>
                    <div class="h-20 flex items-end justify-center">
                        <p class="font-semibold text-[#1b1c1c] dark:text-white border-b border-[#eae8e7] dark:border-[#333535] pb-1 px-8">Administrator Fasilitas</p>
                    </div>
                    <p class="text-[10px] text-[#5c3f3f] dark:text-[#8c8282] mt-1">CleanTrack Management</p>
                </div>
            </div>

        </div>

        <!-- Report Footer -->
        <div class="p-4 bg-[#f5f3f3] dark:bg-[#262727] border-t border-[#eae8e7] dark:border-[#2e3030] text-center text-[11px] text-[#5c3f3f] dark:text-[#8c8282]">
            Dicetak secara otomatis melalui CleanTrack &bull; Sistem Monitoring Kebersihan Digital &bull; {{ date('Y') }}
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
