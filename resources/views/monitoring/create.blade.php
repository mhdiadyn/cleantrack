@extends('layouts.app')

@section('title', 'Pemeriksaan Kebersihan Baru')
@section('header_title', 'Form Pemeriksaan Kebersihan')
@section('header_subtitle', 'Catat hasil monitoring kondisi fisik area, jam pengerjaan, dan upload foto bukti serta panorama 360°')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
        
        <!-- Form Header Merah Putih -->
        <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-gradient-to-r from-red-600/10 via-slate-50 dark:via-slate-800/40 to-transparent">
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Formulir Pemeriksaan Area</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Lengkapi data kondisi kebersihan, jam pembersihan, dan lampirkan foto sebelum/sesudah serta foto 360°</p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-red-100 dark:bg-red-950/60 text-red-800 dark:text-red-300 border border-red-200 dark:border-red-800/60">
                <i class="fa-regular fa-clock"></i>
                <span>Waktu Real-Time</span>
            </span>
        </div>

        <form action="{{ route('monitoring.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf

            <!-- Meta Data Otomatis (Petugas & Tanggal) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200/70 dark:border-slate-700/70">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-red-600 text-white flex items-center justify-center font-bold text-sm shrink-0 shadow-sm">
                        <i class="fa-solid fa-user-check"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Petugas Pemeriksa</span>
                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200">{{ Auth::user()->name }} ({{ ucfirst(Auth::user()->role) }})</span>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-slate-200 flex items-center justify-center text-sm shrink-0">
                        <i class="fa-regular fa-calendar-check"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">Tanggal Pemeriksaan</span>
                        <span class="text-xs font-bold text-slate-800 dark:text-slate-200">{{ $currentTime->translatedFormat('d F Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Pilih Area -->
            <div>
                <label for="area_id" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    Pilih Area yang Diperiksa <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <select id="area_id" name="area_id" required class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500 @error('area_id') border-red-500 @enderror">
                        <option value="">-- Pilih Ruangan / Fasilitas --</option>
                        @foreach($areas as $area)
                            <option value="{{ $area->id }}" {{ old('area_id', $selectedAreaId) == $area->id ? 'selected' : '' }}>
                                {{ $area->code }} - {{ $area->name }} (Lantai {{ $area->floor }} • {{ $area->location }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('area_id')
                    <p class="text-[11px] text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Jam Mulai & Jam Selesai Pembersihan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 p-4 rounded-xl bg-slate-50/80 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-700">
                <!-- Jam Mulai (Sebelum Pembersihan) -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="start_time" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                            <i class="fa-regular fa-clock text-red-500 mr-1"></i> Jam Mulai (Sebelum)
                        </label>
                        <span class="text-[10px] text-slate-400">Waktu mulai tindakan</span>
                    </div>
                    <input type="time" 
                           id="start_time" 
                           name="start_time" 
                           value="{{ old('start_time', $currentTime->copy()->subMinutes(25)->format('H:i')) }}" 
                           class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-red-500">
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1">Tercatat saat petugas mulai membersihkan ruangan.</p>
                </div>

                <!-- Jam Selesai (Sesudah Pembersihan) -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="end_time" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                            <i class="fa-solid fa-clock-rotate-left text-emerald-500 mr-1"></i> Jam Selesai (Sesudah)
                        </label>
                        <button type="button" 
                                onclick="const now = new Date(); const hh = String(now.getHours()).padStart(2,'0'); const mm = String(now.getMinutes()).padStart(2,'0'); document.getElementById('end_time').value = `${hh}:${mm}`;" 
                                class="text-[10px] text-red-600 dark:text-red-400 font-bold hover:underline cursor-pointer">
                            Set Jam Sekarang
                        </button>
                    </div>
                    <input type="time" 
                           id="end_time" 
                           name="end_time" 
                           value="{{ old('end_time', $currentTime->format('H:i')) }}" 
                           class="w-full px-4 py-2.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-red-500">
                    <p class="text-[10px] text-slate-500 dark:text-slate-400 mt-1">Tercatat saat petugas telah menyelesaikan pembersihan.</p>
                </div>
            </div>

            <!-- Kondisi Kebersihan & Status -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                
                <!-- Kondisi Kebersihan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        Kondisi Kebersihan <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach(['Bersih', 'Cukup Bersih', 'Kotor', 'Sangat Kotor'] as $cond)
                            <label class="relative flex items-center justify-center p-3 rounded-xl border border-slate-200 dark:border-slate-700 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors has-checked:border-red-500 dark:has-checked:border-red-400 has-checked:bg-red-50/50 dark:has-checked:bg-red-950/40 has-checked:ring-2 has-checked:ring-red-500">
                                <input type="radio" name="cleanliness_condition" value="{{ $cond }}" class="sr-only" {{ old('cleanliness_condition', 'Bersih') === $cond ? 'checked' : '' }} required>
                                <span class="text-xs font-bold text-slate-700 dark:text-slate-200">{{ $cond }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('cleanliness_condition')
                        <p class="text-[11px] text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status Kebersihan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        Status Pengerjaan <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach(['Belum Diperiksa', 'Perlu Dibersihkan', 'Sedang Dibersihkan', 'Sudah Dibersihkan'] as $st)
                            <label class="relative flex items-center justify-center p-3 rounded-xl border border-slate-200 dark:border-slate-700 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors has-checked:border-red-500 dark:has-checked:border-red-400 has-checked:bg-red-50/50 dark:has-checked:bg-red-950/40 has-checked:ring-2 has-checked:ring-red-500">
                                <input type="radio" name="status" value="{{ $st }}" class="sr-only" {{ old('status', 'Sudah Dibersihkan') === $st ? 'checked' : '' }} required>
                                <span class="text-xs font-bold text-slate-700 dark:text-slate-200 text-center">{{ $st }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('status')
                        <p class="text-[11px] text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <!-- Upload Foto Dokumentasi (Sebelum, Sesudah, dan Foto 360) -->
            <div class="space-y-4 pt-2">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">Dokumentasi Foto Kebersihan & 360°</h4>
                        <p class="text-[11px] text-slate-500 dark:text-slate-400">Lampirkan foto fisik sebelum/sesudah (Maks 10MB) dan foto panorama 360° (Maks 50MB)</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    
                    <!-- 1. Foto Sebelum Dibersihkan -->
                    <div x-data="{ preview: null, fileName: '' }" class="bg-slate-50/70 dark:bg-slate-800/40 border-2 border-dashed border-slate-300 dark:border-slate-700 hover:border-red-500 rounded-2xl p-4 transition-all flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="text-xs font-bold text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                    <span>Foto Sebelum</span>
                                </label>
                                <span class="text-[10px] font-medium text-slate-400">Maks 10MB</span>
                            </div>

                            <!-- Persistent File Input ALWAYS in DOM -->
                            <input type="file" 
                                   x-ref="fileInputBefore"
                                   id="photo_before_input"
                                   name="photo_before" 
                                   accept="image/*" 
                                   capture="environment" 
                                   @change="const file = $event.target.files[0]; if(file) { preview = URL.createObjectURL(file); fileName = file.name; }" 
                                   class="hidden">

                            <div x-show="!preview" class="py-4 text-center space-y-2">
                                <div class="w-10 h-10 rounded-xl bg-red-50 dark:bg-red-950/60 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 flex items-center justify-center mx-auto text-lg shadow-xs">
                                    <i class="fa-solid fa-camera-retro"></i>
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">Kondisi kotoran / debu awal</p>
                                <div class="pt-1">
                                    <button type="button" 
                                            @click="$refs.fileInputBefore.click()" 
                                            class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 dark:bg-slate-700 text-white text-xs font-bold shadow-sm cursor-pointer transition-all w-full">
                                        <i class="fa-solid fa-camera"></i>
                                        <span>Ambil Foto</span>
                                    </button>
                                </div>
                            </div>

                            <div x-show="preview" x-cloak class="space-y-2">
                                <div class="relative rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-black h-36 flex items-center justify-center">
                                    <img :src="preview" alt="Preview Foto Sebelum" class="w-full h-full object-cover">
                                    <span class="absolute bottom-1 left-1 px-1.5 py-0.5 rounded bg-red-600/90 text-white font-bold text-[9px]">Sebelum</span>
                                </div>
                                <div class="flex items-center justify-between gap-2">
                                    <button type="button" @click="$refs.fileInputBefore.click()" class="text-[11px] text-slate-600 dark:text-slate-300 hover:underline">
                                        Ganti Foto
                                    </button>
                                    <button type="button" @click="preview = null; fileName = ''; $refs.fileInputBefore.value = ''" class="text-red-600 text-[11px] font-bold hover:underline">
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                        @error('photo_before')
                            <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- 2. Foto Sesudah Dibersihkan -->
                    <div x-data="{ preview: null, fileName: '' }" class="bg-slate-50/70 dark:bg-slate-800/40 border-2 border-dashed border-slate-300 dark:border-slate-700 hover:border-emerald-500 rounded-2xl p-4 transition-all flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="text-xs font-bold text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                    <span>Foto Sesudah</span>
                                </label>
                                <span class="text-[10px] font-medium text-slate-400">Maks 10MB</span>
                            </div>

                            <!-- Persistent File Input ALWAYS in DOM -->
                            <input type="file" 
                                   x-ref="fileInputAfter"
                                   id="photo_after_input"
                                   name="photo_after" 
                                   accept="image/*" 
                                   capture="environment" 
                                   @change="const file = $event.target.files[0]; if(file) { preview = URL.createObjectURL(file); fileName = file.name; }" 
                                   class="hidden">

                            <div x-show="!preview" class="py-4 text-center space-y-2">
                                <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mx-auto text-lg shadow-xs">
                                    <i class="fa-solid fa-circle-check"></i>
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">Hasil akhir bersih & rapi</p>
                                <div class="pt-1">
                                    <button type="button" 
                                            @click="$refs.fileInputAfter.click()" 
                                            class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm cursor-pointer transition-all w-full">
                                        <i class="fa-solid fa-camera"></i>
                                        <span>Ambil Foto</span>
                                    </button>
                                </div>
                            </div>

                            <div x-show="preview" x-cloak class="space-y-2">
                                <div class="relative rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-black h-36 flex items-center justify-center">
                                    <img :src="preview" alt="Preview Foto Sesudah" class="w-full h-full object-cover">
                                    <span class="absolute bottom-1 left-1 px-1.5 py-0.5 rounded bg-emerald-600/90 text-white font-bold text-[9px]">Sesudah</span>
                                </div>
                                <div class="flex items-center justify-between gap-2">
                                    <button type="button" @click="$refs.fileInputAfter.click()" class="text-[11px] text-slate-600 dark:text-slate-300 hover:underline">
                                        Ganti Foto
                                    </button>
                                    <button type="button" @click="preview = null; fileName = ''; $refs.fileInputAfter.value = ''" class="text-red-600 text-[11px] font-bold hover:underline">
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                        @error('photo_after')
                            <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- 3. Foto Ruangan (360° atau Standar) -->
                    <div x-data="{ 
                            preview: null, 
                            fileName: '', 
                            photoType: '{{ old('photo_360_type', 'panorama_360') }}',
                            detectedRatio: null,
                            ratioWarning: false,
                            onFileChange(e) {
                                const file = e.target.files[0];
                                if (!file) return;
                                this.fileName = file.name;
                                this.preview = URL.createObjectURL(file);
                                const img = new Image();
                                img.onload = () => {
                                    const ratio = img.naturalWidth / img.naturalHeight;
                                    this.detectedRatio = ratio.toFixed(2);
                                    if (ratio < 1.85 || ratio > 2.15) {
                                        this.ratioWarning = true;
                                    } else {
                                        this.ratioWarning = false;
                                        this.photoType = 'panorama_360';
                                    }
                                };
                                img.src = this.preview;
                            }
                         }" 
                         class="bg-slate-50/70 dark:bg-slate-800/40 border-2 border-dashed border-slate-300 dark:border-slate-700 hover:border-purple-500 rounded-2xl p-4 transition-all flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="text-xs font-bold text-slate-800 dark:text-slate-200 flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full" :class="photoType === 'panorama_360' ? 'bg-purple-500' : 'bg-sky-500'"></span>
                                    <span>Foto Ruangan / 360°</span>
                                </label>
                                <span class="text-[10px] font-medium text-purple-600 bg-purple-50 dark:bg-purple-950/40 px-1.5 py-0.5 rounded">Maks 50MB</span>
                            </div>

                            <!-- Photo Type Selector -->
                            <div class="grid grid-cols-2 gap-1.5 mb-2.5">
                                <label class="flex items-center gap-1.5 p-1.5 rounded-lg border cursor-pointer text-[11px] transition-all"
                                       :class="photoType === 'panorama_360' ? 'bg-purple-50/80 border-purple-400 text-purple-900 font-bold dark:bg-purple-950/40 dark:border-purple-700 dark:text-purple-300' : 'border-slate-200 text-slate-600 dark:border-slate-700 dark:text-slate-400'">
                                    <input type="radio" name="photo_360_type" value="panorama_360" x-model="photoType" class="hidden">
                                    <i class="fa-solid fa-panorama text-purple-600"></i>
                                    <span>360° Panorama</span>
                                </label>
                                <label class="flex items-center gap-1.5 p-1.5 rounded-lg border cursor-pointer text-[11px] transition-all"
                                       :class="photoType === 'standard' ? 'bg-sky-50/80 border-sky-400 text-sky-900 font-bold dark:bg-sky-950/40 dark:border-sky-700 dark:text-sky-300' : 'border-slate-200 text-slate-600 dark:border-slate-700 dark:text-slate-400'">
                                    <input type="radio" name="photo_360_type" value="standard" x-model="photoType" class="hidden">
                                    <i class="fa-solid fa-image text-sky-600"></i>
                                    <span>Foto Standar HD</span>
                                </label>
                            </div>

                            <!-- Persistent File Input ALWAYS in DOM -->
                            <input type="file" 
                                   x-ref="fileInput360"
                                   id="photo_360_input"
                                   name="photo_360" 
                                   accept="image/*" 
                                   @change="onFileChange($event)" 
                                   class="hidden">

                            <div x-show="!preview" class="py-3 text-center space-y-2">
                                <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-950/60 border border-purple-200 dark:border-purple-800 text-purple-600 dark:text-purple-400 flex items-center justify-center mx-auto text-lg shadow-xs">
                                    <i class="fa-solid fa-camera"></i>
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-slate-400">Pilih foto panorama atau foto sudut ruangan</p>
                                <div class="pt-1">
                                    <button type="button" 
                                            @click="$refs.fileInput360.click()" 
                                            class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold shadow-sm cursor-pointer transition-all w-full">
                                        <i class="fa-solid fa-file-image"></i>
                                        <span>Pilih Foto</span>
                                    </button>
                                </div>
                            </div>

                            <div x-show="preview" x-cloak class="space-y-2">
                                <div class="relative rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-black h-32 flex items-center justify-center">
                                    <img :src="preview" alt="Preview Foto" class="w-full h-full object-cover">
                                    <span class="absolute bottom-1 left-1 px-1.5 py-0.5 rounded text-white font-bold text-[9px]"
                                          :class="photoType === 'panorama_360' ? 'bg-purple-600/90' : 'bg-sky-600/90'"
                                          x-text="photoType === 'panorama_360' ? '360° Panorama' : 'Foto Standar HD'"></span>
                                </div>

                                <div x-show="ratioWarning && photoType === 'panorama_360'" class="p-2 rounded-lg bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-[10px] text-amber-800 dark:text-amber-300">
                                    <div class="flex items-center gap-1 font-bold">
                                        <i class="fa-solid fa-triangle-exclamation"></i>
                                        <span>Rasio <span x-text="detectedRatio + ':1'"></span> bukan 2:1</span>
                                    </div>
                                    <div class="mt-1 flex items-center justify-between">
                                        <span>Disarankan ubah ke Foto Standar agar tidak gepeng/melengkung.</span>
                                        <button type="button" @click="photoType = 'standard'" class="ml-1 px-1.5 py-0.5 bg-sky-600 text-white font-bold rounded text-[9px]">Ubah</button>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between gap-2 pt-1">
                                    <button type="button" @click="$refs.fileInput360.click()" class="text-[11px] text-slate-600 dark:text-slate-300 hover:underline">
                                        Ganti Foto
                                    </button>
                                    <button type="button" @click="preview = null; fileName = ''; detectedRatio = null; ratioWarning = false; $refs.fileInput360.value = ''" class="text-red-600 text-[11px] font-bold hover:underline">
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                        @error('photo_360')
                            <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </div>

            <!-- Catatan Pemeriksaan -->
            <div>
                <label for="notes" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    Catatan Temuan / Tindakan (Opsional)
                </label>
                <textarea id="notes" 
                          name="notes" 
                          rows="3" 
                          placeholder="Tuliskan keterangan detail hasil pemeriksaan, misal: 'Lantai telah dipel dengan desinfektan, sabun wastafel telah diisi ulang'..." 
                          class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500 @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="text-[11px] text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Buttons Merah Putih -->
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
                <a href="{{ url()->previous() }}" class="px-5 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-7 py-3 rounded-xl bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white font-bold text-xs shadow-lg shadow-red-500/25 transition-all flex items-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    <span>Simpan Hasil Pemeriksaan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
