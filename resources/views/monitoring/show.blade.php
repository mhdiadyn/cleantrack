@extends('layouts.app')

@section('title', 'Detail Monitoring #' . $monitoring->id)
@section('header_title', 'Laporan Hasil Pemeriksaan')
@section('header_subtitle', 'Detail monitoring kebersihan dan evaluasi kondisi area')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Top Action Bar -->
    <div class="flex items-center justify-between no-print">
        <a href="{{ Auth::user()->role === 'admin' ? route('admin.monitoring.index') : route('petugas.my-monitoring') }}" 
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 font-bold text-xs shadow-xs transition-colors">
            <i class="fa-solid fa-arrow-left text-[10px]"></i>
            <span>Kembali ke Riwayat</span>
        </a>

        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white font-bold text-xs shadow-xs transition-colors">
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
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs transition-colors">
                        <i class="fa-solid fa-trash"></i>
                        <span>Hapus</span>
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Official Report Card Layout -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-md overflow-hidden print:shadow-none print:border-0">
        
        <!-- Report Header / Letterhead -->
        <div class="p-6 sm:p-8 bg-gradient-to-r from-slate-900 via-teal-950 to-slate-900 text-white flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-teal-800/40">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-teal-500/20 border border-teal-500/40 text-teal-300 flex items-center justify-center text-2xl shrink-0">
                    <i class="fa-solid fa-file-shield"></i>
                </div>
                <div>
                    <span class="text-[11px] font-extrabold uppercase tracking-widest text-teal-400">Lembar Berita Acara</span>
                    <h2 class="text-xl sm:text-2xl font-black">Laporan Pemeriksaan Kebersihan</h2>
                    <p class="text-xs text-slate-300 mt-0.5">Sistem Monitoring Terintegrasi • CleanTrack</p>
                </div>
            </div>

            <div class="text-left sm:text-right bg-white/10 sm:bg-transparent p-3 sm:p-0 rounded-xl">
                <span class="text-[10px] font-bold text-slate-300 uppercase tracking-wider block">Nomor Registrasi Laporan</span>
                <span class="font-mono text-base font-black text-teal-300">#CT-{{ str_pad($monitoring->id, 5, '0', STR_PAD_LEFT) }}</span>
                <span class="text-[11px] text-slate-400 block mt-0.5">{{ \Carbon\Carbon::parse($monitoring->check_date)->format('d F Y') }}</span>
            </div>
        </div>

        <!-- Report Body -->
        <div class="p-6 sm:p-8 space-y-8">
            
            <!-- Metadata Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-slate-50/80 p-6 rounded-2xl border border-slate-100">
                
                <!-- Area & Lokasi -->
                <div class="space-y-3">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Nama Area</span>
                        <h4 class="text-base font-bold text-slate-900 flex items-center gap-2 mt-0.5">
                            <i class="fa-solid fa-map-location-dot text-teal-600"></i>
                            <span>{{ $monitoring->area->name ?? '-' }}</span>
                        </h4>
                    </div>

                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Lokasi Fasilitas</span>
                        <p class="text-xs font-semibold text-slate-700 mt-0.5">{{ $monitoring->area->location ?? '-' }}</p>
                    </div>

                    @if($monitoring->area && $monitoring->area->description)
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Deskripsi Fasilitas</span>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $monitoring->area->description }}</p>
                        </div>
                    @endif
                </div>

                <!-- Petugas, Status & Kondisi -->
                <div class="space-y-3">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Petugas Pemeriksa</span>
                        <div class="flex items-center gap-2 mt-1">
                            <div class="w-7 h-7 rounded-full bg-teal-100 text-teal-800 font-bold text-xs flex items-center justify-center">
                                {{ strtoupper(substr($monitoring->user->name ?? 'P', 0, 1)) }}
                            </div>
                            <span class="text-xs font-bold text-slate-900">{{ $monitoring->user->name ?? '-' }}</span>
                            <span class="text-[11px] text-slate-500">({{ $monitoring->user->email ?? '' }})</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 pt-1">
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Kondisi Kebersihan</span>
                            <x-badge-condition :condition="$monitoring->cleanliness_condition" />
                        </div>

                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Status Pengerjaan</span>
                            <x-badge-status :status="$monitoring->status" />
                        </div>
                    </div>

                    <div class="pt-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Waktu Pelaksanaan</span>
                        <p class="text-xs font-medium text-slate-700 mt-0.5 flex items-center gap-1.5">
                            <i class="fa-regular fa-calendar text-teal-600"></i>
                            <span>{{ \Carbon\Carbon::parse($monitoring->check_date)->translatedFormat('l, d F Y') }}</span>
                            <span class="text-slate-300">•</span>
                            <i class="fa-regular fa-clock text-teal-600"></i>
                            <span>{{ substr($monitoring->check_time, 0, 5) }} WIB</span>
                        </p>
                    </div>
                </div>

            </div>

            <!-- Dokumentasi Foto (Sebelum & Sesudah) -->
            <div>
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-camera text-teal-600"></i>
                    <span>Dokumentasi Foto Kebersihan</span>
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Foto Sebelum -->
                    <div class="rounded-2xl border border-slate-200 overflow-hidden bg-slate-50">
                        <div class="px-4 py-2.5 bg-rose-50 border-b border-rose-100 flex items-center justify-between">
                            <span class="text-xs font-bold text-rose-800 flex items-center gap-1.5">
                                <i class="fa-solid fa-circle-exclamation text-rose-500"></i>
                                Foto Sebelum Tindakan
                            </span>
                            @if($monitoring->photo_before_url)
                                <button type="button" 
                                        @click="$dispatch('open-image-modal', { src: '{{ $monitoring->photo_before_url }}', title: 'Foto Sebelum - {{ $monitoring->area->name ?? '' }}' })"
                                        class="text-[11px] font-bold text-rose-600 hover:text-rose-800 flex items-center gap-1 no-print">
                                    <i class="fa-solid fa-magnifying-glass-plus"></i> Perbesar
                                </button>
                            @endif
                        </div>

                        <div class="p-3 flex items-center justify-center min-h-[220px]">
                            @if($monitoring->photo_before_url)
                                <img src="{{ $monitoring->photo_before_url }}" 
                                     alt="Foto Sebelum" 
                                     @click="$dispatch('open-image-modal', { src: '{{ $monitoring->photo_before_url }}', title: 'Foto Sebelum - {{ $monitoring->area->name ?? '' }}' })"
                                     class="max-h-64 w-full object-cover rounded-xl border border-slate-200 shadow-xs cursor-pointer hover:opacity-90 transition-opacity">
                            @else
                                <div class="text-center text-slate-400 py-8">
                                    <i class="fa-solid fa-image-slash text-3xl mb-2 block"></i>
                                    <p class="text-xs">Tidak ada foto sebelum yang dilampirkan.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Foto Sesudah -->
                    <div class="rounded-2xl border border-slate-200 overflow-hidden bg-slate-50">
                        <div class="px-4 py-2.5 bg-emerald-50 border-b border-emerald-100 flex items-center justify-between">
                            <span class="text-xs font-bold text-emerald-800 flex items-center gap-1.5">
                                <i class="fa-solid fa-circle-check text-emerald-600"></i>
                                Foto Sesudah Tindakan
                            </span>
                            @if($monitoring->photo_after_url)
                                <button type="button" 
                                        @click="$dispatch('open-image-modal', { src: '{{ $monitoring->photo_after_url }}', title: 'Foto Sesudah - {{ $monitoring->area->name ?? '' }}' })"
                                        class="text-[11px] font-bold text-emerald-600 hover:text-emerald-800 flex items-center gap-1 no-print">
                                    <i class="fa-solid fa-magnifying-glass-plus"></i> Perbesar
                                </button>
                            @endif
                        </div>

                        <div class="p-3 flex items-center justify-center min-h-[220px]">
                            @if($monitoring->photo_after_url)
                                <img src="{{ $monitoring->photo_after_url }}" 
                                     alt="Foto Sesudah" 
                                     @click="$dispatch('open-image-modal', { src: '{{ $monitoring->photo_after_url }}', title: 'Foto Sesudah - {{ $monitoring->area->name ?? '' }}' })"
                                     class="max-h-64 w-full object-cover rounded-xl border border-slate-200 shadow-xs cursor-pointer hover:opacity-90 transition-opacity">
                            @else
                                <div class="text-center text-slate-400 py-8">
                                    <i class="fa-solid fa-image-slash text-3xl mb-2 block"></i>
                                    <p class="text-xs">Tidak ada foto sesudah yang dilampirkan.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>

            <!-- Catatan Evaluasi -->
            <div>
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-2 flex items-center gap-2">
                    <i class="fa-solid fa-comment-dots text-teal-600"></i>
                    <span>Catatan Petugas & Tindakan Tambahan</span>
                </h3>
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-700 leading-relaxed">
                    {{ $monitoring->notes ?: 'Tidak ada catatan tambahan untuk pemeriksaan ini.' }}
                </div>
            </div>

            <!-- Tanda Tangan Pengesahan (Print Friendly) -->
            <div class="pt-8 border-t border-slate-200 grid grid-cols-2 gap-8 text-center text-xs">
                <div>
                    <p class="text-slate-500">Petugas Pemeriksa,</p>
                    <div class="h-20 flex items-end justify-center">
                        <p class="font-bold text-slate-900 border-b border-slate-400 pb-1 px-8">{{ $monitoring->user->name ?? '............................' }}</p>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1">NIP/ID: {{ $monitoring->user->id ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-slate-500">Mengetahui (Supervisor / Admin),</p>
                    <div class="h-20 flex items-end justify-center">
                        <p class="font-bold text-slate-900 border-b border-slate-400 pb-1 px-8">Administrator Fasilitas</p>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-1">CleanTrack Management</p>
                </div>
            </div>

        </div>

        <!-- Report Footer -->
        <div class="p-4 bg-slate-50 border-t border-slate-100 text-center text-[11px] text-slate-400">
            Dicetak secara otomatis melalui CleanTrack • Sistem Monitoring Kebersihan Digital • {{ date('Y') }}
        </div>
    </div>

</div>
@endsection
