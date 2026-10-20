@extends('layouts.app')

@section('title', 'Edit Area - ' . $area->name)
@section('header_title', 'Edit Area Kebersihan')
@section('header_subtitle', 'Perbarui rincian, koordinat denah, dan foto 360° ruangan')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="card-clean overflow-hidden">
        <div class="p-6 border-b border-[#eae8e7] dark:border-[#2e3030] flex items-center justify-between bg-[#f5f3f3] dark:bg-[#262727]">
            <div>
                <h3 class="font-heading text-base font-semibold text-[#1b1c1c] dark:text-white">Perbarui Data Area & Foto 360°</h3>
                <p class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5] mt-0.5">{{ $area->name }} ({{ $area->code }})</p>
            </div>
            <a href="{{ route('admin.areas.index') }}" class="btn-outline-neutral text-xs">
                <i class="fa-solid fa-arrow-left text-[10px]"></i>
                <span>Kembali</span>
            </a>
        </div>

        <form action="{{ route('admin.areas.update', $area->id) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
            @csrf
            @method('PUT')

            <!-- Nama Area -->
            <div>
                <label for="name" class="label-clean">
                    Nama Area <span class="text-[#ba1a1a]">*</span>
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="{{ old('name', $area->name) }}" 
                       required 
                       placeholder="Contoh: Toilet Lantai 1, Lobby Utama, Ruang Meeting VIP" 
                       class="input-clean @error('name') border-[#ba1a1a] @enderror">
                @error('name')
                    <p class="text-[11px] text-[#ba1a1a] dark:text-[#ffb4ab] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Lokasi & Lantai -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="location" class="label-clean">
                        Lokasi Gedung / Posisi <span class="text-[#ba1a1a]">*</span>
                    </label>
                    <input type="text" 
                           id="location" 
                           name="location" 
                           value="{{ old('location', $area->location) }}" 
                           required 
                           placeholder="Contoh: Gedung A Sayap Kiri" 
                           class="input-clean @error('location') border-[#ba1a1a] @enderror">
                    @error('location')
                        <p class="text-[11px] text-[#ba1a1a] dark:text-[#ffb4ab] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="floor" class="label-clean">
                        Lantai Gedung <span class="text-[#ba1a1a]">*</span>
                    </label>
                    <select id="floor" name="floor" class="input-clean">
                        @for($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}" {{ old('floor', $area->floor ?? 1) == $i ? 'selected' : '' }}>
                                Lantai {{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>
            </div>

            <!-- Posisi Koordinat pada Denah (X% & Y%) -->
            <div class="p-4 rounded-xl bg-[#f5f3f3] dark:bg-[#262727] border border-[#eae8e7] dark:border-[#333535] space-y-3">
                <div class="flex items-center justify-between">
                    <label class="label-clean !mb-0 flex items-center gap-1.5">
                        <i class="fa-solid fa-map-pin text-[#dc143c]"></i>
                        <span>Posisi Pin pada Denah Interaktif (0 - 100%)</span>
                    </label>
                    <span class="text-[11px] text-[#5c3f3f] dark:text-[#8c8282]">Diperlukan untuk fitur Denah</span>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <span class="text-[11px] text-[#5c3f3f] dark:text-[#c8c6c5] block mb-1">Posisi Horizontal (X %)</span>
                        <input type="number" step="0.1" min="0" max="100" name="pos_x" value="{{ old('pos_x', $area->pos_x ?? 50) }}" class="input-clean text-xs font-semibold">
                    </div>
                    <div>
                        <span class="text-[11px] text-[#5c3f3f] dark:text-[#c8c6c5] block mb-1">Posisi Vertikal (Y %)</span>
                        <input type="number" step="0.1" min="0" max="100" name="pos_y" value="{{ old('pos_y', $area->pos_y ?? 50) }}" class="input-clean text-xs font-semibold">
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
                 class="p-4 rounded-xl bg-[#f5f3f3] dark:bg-[#262727] border border-[#eae8e7] dark:border-[#333535] space-y-4">
                
                <div class="flex items-center justify-between">
                    <label class="label-clean !mb-0 flex items-center gap-1.5">
                        <i class="fa-solid fa-camera text-[#dc143c]"></i>
                        <span>Foto Fasilitas / Ruangan</span>
                    </label>
                    <span class="text-[10px] font-semibold text-[#5c3f3f] bg-white dark:bg-[#1f2020] border border-[#eae8e7] dark:border-[#333535] px-2 py-0.5 rounded">Maks 50MB</span>
                </div>

                <!-- Tipe Foto Selector -->
                <div>
                    <label class="block text-xs font-semibold text-[#5c3f3f] dark:text-[#c8c6c5] mb-2">Tentukan Format Tipe Foto:</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label :class="photoType === 'panorama_360' ? 'border-[#dc143c] bg-[#fff1f0] dark:bg-[#3b0b14]' : 'border-[#eae8e7] dark:border-[#333535] bg-white dark:bg-[#1f2020]'"
                               class="flex items-start gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all">
                            <input type="radio" name="photo_type" value="panorama_360" x-model="photoType" 
                                   @change="ratioWarning = (aspectRatio && (aspectRatio < 1.85 || aspectRatio > 2.15))" class="mt-0.5 text-[#dc143c] focus:ring-[#dc143c]">
                            <div>
                                <span class="text-xs font-semibold text-[#1b1c1c] dark:text-white block">Panorama 360° Virtual</span>
                                <span class="text-[11px] text-[#5c3f3f] dark:text-[#c8c6c5] block mt-0.5">Format equirectangular bola 360° (rasio 2:1 dari kamera 360°).</span>
                            </div>
                        </label>

                        <label :class="photoType === 'standard' ? 'border-[#0284c7] bg-[#e0f2fe] dark:bg-[#082f49]' : 'border-[#eae8e7] dark:border-[#333535] bg-white dark:bg-[#1f2020]'"
                                class="flex items-start gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all">
                            <input type="radio" name="photo_type" value="standard" x-model="photoType" 
                                   @change="ratioWarning = false" class="mt-0.5 text-[#0284c7] focus:ring-[#0284c7]">
                            <div>
                                <span class="text-xs font-semibold text-[#1b1c1c] dark:text-white block">Foto Biasa (Standar HD)</span>
                                <span class="text-[11px] text-[#5c3f3f] dark:text-[#c8c6c5] block mt-0.5">Foto ruangan biasa dari kamera HP / DSLR. Tampil proporsional.</span>
                            </div>
                        </label>
                    </div>
                </div>

                <template x-if="preview">
                    <div class="space-y-2">
                        <div class="relative rounded-xl overflow-hidden border border-[#eae8e7] dark:border-[#333535] bg-black h-44 flex items-center justify-center">
                            <img :src="preview" alt="Preview Foto" class="max-h-full max-w-full object-contain">
                            <span class="absolute bottom-2 left-2 px-2.5 py-1 rounded text-white font-semibold text-[10px] uppercase tracking-wider"
                                  :class="photoType === 'standard' ? 'bg-[#0284c7]' : 'bg-[#dc143c]'"
                                  x-text="photoType === 'standard' ? 'Foto Standar' : 'Panorama 360°'"></span>
                        </div>
                    </div>
                </template>

                <!-- Warning jika pilih 360 tapi rasio bukan 2:1 -->
                <div x-show="ratioWarning" x-cloak class="p-3 rounded-xl bg-[#fef3c7] dark:bg-[#451a03] border border-[#fde68a] dark:border-[#78350f] text-xs text-[#b45309] dark:text-[#fcd34d] flex items-start gap-2.5">
                    <i class="fa-solid fa-triangle-exclamation text-amber-600 mt-0.5"></i>
                    <div>
                        <p class="font-bold">Perhatian Rasio Foto (<span x-text="aspectRatio + ':1'"></span>):</p>
                        <p class="mt-0.5">Foto ini bukan berasio 2:1. Jika ini adalah foto ruangan biasa dari kamera HP, silakan pilih opsi <strong>Foto Biasa (Standar HD)</strong> agar gambar tidak terdistorsi di viewer.</p>
                        <button type="button" @click="photoType = 'standard'; ratioWarning = false" class="mt-1.5 px-2.5 py-1 rounded-lg bg-[#0284c7] text-white font-semibold text-[11px] hover:bg-[#0369a1] cursor-pointer">
                            Ganti ke Foto Standar
                        </button>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <label class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl text-white text-xs font-semibold cursor-pointer transition-colors"
                           :class="photoType === 'standard' ? 'bg-[#0284c7] hover:bg-[#0369a1]' : 'bg-[#dc143c] hover:bg-[#b1002c]'">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                        <span>Pilih / Ganti File Foto</span>
                        <input type="file" name="photo_360" accept="image/*" @change="onFileChange($event)" class="hidden">
                    </label>
                    <span class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5] truncate max-w-xs" x-text="fileName || 'Belum ada file baru dipilih'"></span>
                </div>
                @error('photo_360')
                    <p class="text-[11px] text-[#ba1a1a] dark:text-[#ffb4ab] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Keterangan / Catatan Fasilitas -->
            <div>
                <label for="description" class="label-clean">
                    Keterangan Fasilitas (Opsional)
                </label>
                <textarea id="description" 
                          name="description" 
                          rows="3" 
                          placeholder="Deskripsi perlengkapan dan spesifikasi fasilitas..." 
                          class="input-clean @error('description') border-[#ba1a1a] @enderror">{{ old('description', $area->description) }}</textarea>
                @error('description')
                    <p class="text-[11px] text-[#ba1a1a] dark:text-[#ffb4ab] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status Aktif Switch -->
            <div class="pt-2">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $area->is_active) ? 'checked' : '' }} class="w-4 h-4 text-[#dc143c] rounded focus:ring-[#dc143c] border-[#eae8e7] dark:border-[#333535] dark:bg-[#1f2020]">
                    <div>
                        <span class="text-sm font-semibold text-[#1b1c1c] dark:text-white">Status Area Aktif</span>
                        <p class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5]">Jika dinonaktifkan, area tidak akan muncul dalam formulir pemeriksaan rutin.</p>
                    </div>
                </label>
            </div>

            <!-- Action Buttons -->
            <div class="pt-4 border-t border-[#eae8e7] dark:border-[#2e3030] flex items-center justify-end gap-3">
                <a href="{{ route('admin.areas.index') }}" class="btn-outline-neutral">
                    Batal
                </a>
                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
