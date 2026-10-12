@extends('layouts.app')

@section('title', 'Edit Area - ' . $area->name)
@section('header_title', 'Edit Area Kebersihan')
@section('header_subtitle', 'Perbarui rincian, koordinat denah, dan foto 360° ruangan')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between bg-gradient-to-r from-red-600/10 via-slate-50 dark:via-slate-800/40 to-transparent">
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">Perbarui Data Area & Foto 360°</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $area->name }} ({{ $area->code }})</p>
            </div>
            <a href="{{ route('admin.areas.index') }}" class="text-xs font-semibold text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-slate-200 flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-left text-[10px]"></i>
                <span>Kembali</span>
            </a>
        </div>

        <form action="{{ route('admin.areas.update', $area->id) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
            @csrf
            @method('PUT')

            <!-- Nama Area -->
            <div>
                <label for="name" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    Nama Area <span class="text-red-500">*</span>
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="{{ old('name', $area->name) }}" 
                       required 
                       placeholder="Contoh: Toilet Lantai 1, Lobby Utama, Ruang Meeting VIP" 
                       class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="text-[11px] text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Lokasi & Lantai -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="location" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        Lokasi Gedung / Posisi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="location" 
                           name="location" 
                           value="{{ old('location', $area->location) }}" 
                           required 
                           placeholder="Contoh: Gedung A Sayap Kiri" 
                           class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500 @error('location') border-red-500 @enderror">
                    @error('location')
                        <p class="text-[11px] text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="floor" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        Lantai Gedung <span class="text-red-500">*</span>
                    </label>
                    <select id="floor" name="floor" class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        @for($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}" {{ old('floor', $area->floor ?? 1) == $i ? 'selected' : '' }}>
                                Lantai {{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>
            </div>

            <!-- Posisi Koordinat pada Denah (X% & Y%) -->
            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200/70 dark:border-slate-700/70 space-y-3">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fa-solid fa-map-pin text-red-600"></i>
                        <span>Posisi Pin pada Denah Interaktif (0 - 100%)</span>
                    </label>
                    <span class="text-[11px] text-slate-400">Diperlukan untuk fitur Denah</span>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-[11px] text-slate-500 dark:text-slate-400 block mb-1">Posisi Horizontal (X %)</span>
                        <input type="number" step="0.1" min="0" max="100" name="pos_x" value="{{ old('pos_x', $area->pos_x ?? 50) }}" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-bold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-red-500">
                    </div>
                    <div>
                        <span class="text-[11px] text-slate-500 dark:text-slate-400 block mb-1">Posisi Vertikal (Y %)</span>
                        <input type="number" step="0.1" min="0" max="100" name="pos_y" value="{{ old('pos_y', $area->pos_y ?? 50) }}" class="w-full px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg text-xs font-bold text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-red-500">
                    </div>
                </div>
            </div>

            <!-- Upload Foto Ruangan (Pilihan: Panorama 360° atau Foto Biasa) -->
            <div x-data="{ 
                    photoType: '{{ old('photo_type', $area->photo_type ?? 'panorama_360') }}', 
                    preview: '{{ $area->photo_360_url }}', 
                    fileName: '',
                    aspectRatio: null,
                    ratioWarning: false,
                    onFileChange(e) {
                        const file = e.target.files[0];
                        if (file) {
                            this.preview = URL.createObjectURL(file);
                            this.fileName = file.name;
                            const img = new Image();
                            img.onload = () => {
                                const r = img.naturalWidth / img.naturalHeight;
                                this.aspectRatio = r.toFixed(2);
                                this.ratioWarning = (this.photoType === 'panorama_360' && (r < 1.85 || r > 2.15));
                            };
                            img.src = this.preview;
                        }
                    }
                 }" 
                 class="p-5 rounded-xl bg-slate-50 dark:bg-slate-800/40 border-2 border-dashed border-slate-300 dark:border-slate-700 space-y-4">
                
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fa-solid fa-camera-retro text-red-600"></i>
                        <span>Foto Fasilitas / Ruangan</span>
                    </label>
                    <span class="text-[10px] font-bold text-slate-500 bg-slate-200 dark:bg-slate-700 px-2 py-0.5 rounded">Maks 50MB</span>
                </div>

                <!-- Tipe Foto Selector -->
                <div>
                    <label class="block text-xs font-semibold text-slate-600 dark:text-slate-400 mb-2">Tentukan Format Tipe Foto:</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label :class="photoType === 'panorama_360' ? 'border-purple-500 bg-purple-50/60 dark:bg-purple-950/30' : 'border-slate-200 dark:border-slate-700'"
                               class="flex items-start gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all">
                            <input type="radio" name="photo_type" value="panorama_360" x-model="photoType" 
                                   @change="ratioWarning = (aspectRatio && (aspectRatio < 1.85 || aspectRatio > 2.15))" class="mt-0.5 text-purple-600 focus:ring-purple-500">
                            <div>
                                <span class="text-xs font-bold text-slate-900 dark:text-white block">Panorama 360° Virtual</span>
                                <span class="text-[11px] text-slate-500 dark:text-slate-400 block mt-0.5">Format equirectangular bola 360° (rasio ideal 2:1 dari kamera 360°).</span>
                            </div>
                        </label>

                        <label :class="photoType === 'standard' ? 'border-sky-500 bg-sky-50/60 dark:bg-sky-950/30' : 'border-slate-200 dark:border-slate-700'"
                               class="flex items-start gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all">
                            <input type="radio" name="photo_type" value="standard" x-model="photoType" 
                                   @change="ratioWarning = false" class="mt-0.5 text-sky-600 focus:ring-sky-500">
                            <div>
                                <span class="text-xs font-bold text-slate-900 dark:text-white block">Foto Biasa (Standar HD)</span>
                                <span class="text-[11px] text-slate-500 dark:text-slate-400 block mt-0.5">Foto ruangan biasa dari kamera HP / DSLR. Tampil proporsional tanpa distorsi lengkung.</span>
                            </div>
                        </label>
                    </div>
                </div>

                <template x-if="preview">
                    <div class="space-y-2">
                        <div class="relative rounded-xl overflow-hidden border border-slate-300 dark:border-slate-700 bg-black h-44 flex items-center justify-center">
                            <img :src="preview" alt="Preview Foto" class="max-h-full max-w-full object-contain">
                            <span class="absolute bottom-2 left-2 px-2.5 py-1 rounded text-white font-black text-[10px] uppercase tracking-wider"
                                  :class="photoType === 'standard' ? 'bg-sky-600' : 'bg-purple-600'"
                                  x-text="photoType === 'standard' ? 'Foto Standar' : 'Panorama 360°'"></span>
                        </div>
                    </div>
                </template>

                <!-- Warning jika pilih 360 tapi rasio bukan 2:1 -->
                <div x-show="ratioWarning" x-cloak class="p-3 rounded-xl bg-amber-50 dark:bg-amber-950/40 border border-amber-300 dark:border-amber-800 text-xs text-amber-800 dark:text-amber-300 flex items-start gap-2.5">
                    <i class="fa-solid fa-triangle-exclamation text-amber-600 mt-0.5"></i>
                    <div>
                        <p class="font-bold">Perhatian Rasio Foto (<span x-text="aspectRatio + ':1'"></span>):</p>
                        <p class="mt-0.5">Foto ini bukan berasio 2:1. Jika ini adalah foto ruangan biasa dari kamera HP, silakan pilih opsi <strong>Foto Biasa (Standar HD)</strong> agar gambar tidak melengkung atau gepeng di viewer.</p>
                        <button type="button" @click="photoType = 'standard'; ratioWarning = false" class="mt-1.5 px-2.5 py-1 rounded bg-sky-600 text-white font-bold text-[11px] hover:bg-sky-700 cursor-pointer">
                            Ganti ke Foto Standar
                        </button>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <label class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-white text-xs font-bold shadow-sm cursor-pointer transition-all"
                           :class="photoType === 'standard' ? 'bg-sky-600 hover:bg-sky-700' : 'bg-purple-600 hover:bg-purple-700'">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span>Pilih / Ganti File Foto</span>
                        <input type="file" name="photo_360" accept="image/*" @change="onFileChange($event)" class="hidden">
                    </label>
                    <span class="text-xs text-slate-500 dark:text-slate-400 truncate max-w-xs" x-text="fileName || 'Belum ada file baru dipilih'"></span>
                </div>
                @error('photo_360')
                    <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Keterangan / Catatan Fasilitas -->
            <div>
                <label for="description" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                    Keterangan Fasilitas (Opsional)
                </label>
                <textarea id="description" 
                          name="description" 
                          rows="3" 
                          placeholder="Deskripsi perlengkapan dan spesifikasi fasilitas..." 
                          class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500 @error('description') border-red-500 @enderror">{{ old('description', $area->description) }}</textarea>
                @error('description')
                    <p class="text-[11px] text-red-600 dark:text-red-400 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status Aktif Switch -->
            <div class="pt-2">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $area->is_active) ? 'checked' : '' }} class="w-5 h-5 text-red-600 rounded focus:ring-red-500 border-slate-300 dark:border-slate-600 dark:bg-slate-800">
                    <div>
                        <span class="text-sm font-bold text-slate-800 dark:text-slate-200">Status Area Aktif</span>
                        <p class="text-xs text-slate-500 dark:text-slate-400">Jika dinonaktifkan, area tidak akan muncul dalam formulir pemeriksaan rutin.</p>
                    </div>
                </label>
            </div>

            <!-- Action Buttons -->
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end gap-3">
                <a href="{{ route('admin.areas.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs shadow-md shadow-red-500/20 transition-all flex items-center gap-2 cursor-pointer">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
