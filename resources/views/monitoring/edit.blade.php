@extends('layouts.app')

@section('title', 'Edit Data Monitoring')
@section('header_title', 'Edit Pemeriksaan Kebersihan')
@section('header_subtitle', 'Perbarui data temuan, jam pembersihan, atau ganti foto hasil pemeriksaan')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="card-clean overflow-hidden">
        
        <!-- Form Header -->
        <div class="p-6 border-b border-[#eae8e7] dark:border-[#2e3030] flex items-center justify-between bg-[#f5f3f3] dark:bg-[#262727]">
            <div>
                <h3 class="font-heading text-base font-semibold text-[#1b1c1c] dark:text-white">Perbarui Hasil Pemeriksaan</h3>
                <p class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5] mt-0.5">ID Monitoring: #{{ $monitoring->id }} &bull; {{ $monitoring->area->name ?? '' }}</p>
            </div>
            <a href="{{ route('monitoring.show', $monitoring->id) }}" class="btn-outline-neutral text-xs">
                <i class="fa-solid fa-arrow-left text-[10px]"></i>
                <span>Kembali</span>
            </a>
        </div>

        <form action="{{ route('monitoring.update', $monitoring->id) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Meta Data (Petugas & Waktu) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-xl bg-[#f5f3f3] dark:bg-[#262727] border border-[#eae8e7] dark:border-[#333535]">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-[#dc143c] text-white flex items-center justify-center font-bold text-xs shrink-0">
                        <i class="fa-solid fa-user-check"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-semibold text-[#5c3f3f] dark:text-[#8c8282] uppercase tracking-wider block">Petugas Pencatat</span>
                        <span class="text-xs font-semibold text-[#1b1c1c] dark:text-white">{{ $monitoring->user->name ?? '-' }}</span>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-white dark:bg-[#1f2020] text-[#5c3f3f] dark:text-[#c8c6c5] border border-[#eae8e7] dark:border-[#333535] flex items-center justify-center text-xs shrink-0">
                        <i class="fa-regular fa-calendar-check"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-semibold text-[#5c3f3f] dark:text-[#8c8282] uppercase tracking-wider block">Tanggal Pemeriksaan</span>
                        <span class="text-xs font-semibold text-[#1b1c1c] dark:text-white">{{ \Carbon\Carbon::parse($monitoring->check_date)->translatedFormat('d F Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Pilih Area -->
            <div>
                <label for="area_id" class="label-clean">
                    Area yang Diperiksa <span class="text-[#ba1a1a]">*</span>
                </label>
                <select id="area_id" name="area_id" required class="input-clean @error('area_id') border-[#ba1a1a] @enderror">
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}" {{ old('area_id', $monitoring->area_id) == $area->id ? 'selected' : '' }}>
                            {{ $area->code }} - {{ $area->name }} (Lantai {{ $area->floor }} &bull; {{ $area->location }})
                        </option>
                    @endforeach
                </select>
                @error('area_id')
                    <p class="text-[11px] text-[#ba1a1a] dark:text-[#ffb4ab] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Jam Mulai & Jam Selesai Pembersihan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-xl bg-[#f5f3f3] dark:bg-[#262727] border border-[#eae8e7] dark:border-[#333535]">
                <!-- Jam Mulai (Sebelum) -->
                <div>
                    <label for="start_time" class="label-clean flex items-center gap-1.5">
                        <i class="fa-regular fa-clock text-[#dc143c]"></i>
                        <span>Jam Mulai Pembersihan (Sebelum)</span>
                    </label>
                    <input type="time" 
                           id="start_time" 
                           name="start_time" 
                           value="{{ old('start_time', $monitoring->formatted_start_time ?: substr($monitoring->check_time, 0, 5)) }}" 
                           class="input-clean font-semibold">
                </div>

                <!-- Jam Selesai (Sesudah) -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="end_time" class="label-clean !mb-0 flex items-center gap-1.5">
                            <i class="fa-solid fa-clock-rotate-left text-[#00822d]"></i>
                            <span>Jam Selesai (Sesudah)</span>
                        </label>
                        <button type="button" 
                                onclick="const now = new Date(); const hh = String(now.getHours()).padStart(2,'0'); const mm = String(now.getMinutes()).padStart(2,'0'); document.getElementById('end_time').value = `${hh}:${mm}`;" 
                                class="text-[10px] text-[#dc143c] dark:text-[#ffb3b3] font-semibold hover:underline cursor-pointer">
                            Set Jam Sekarang
                        </button>
                    </div>
                    <input type="time" 
                           id="end_time" 
                           name="end_time" 
                           value="{{ old('end_time', $monitoring->formatted_end_time ?: substr($monitoring->check_time, 0, 5)) }}" 
                           class="input-clean font-semibold">
                </div>
            </div>

            <!-- Kondisi Kebersihan & Status -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                
                <!-- Kondisi Kebersihan -->
                <div>
                    <label class="label-clean">
                        Kondisi Kebersihan <span class="text-[#ba1a1a]">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach(['Bersih', 'Cukup Bersih', 'Kotor', 'Sangat Kotor'] as $cond)
                            <label class="relative flex items-center justify-center p-3 rounded-xl border border-[#eae8e7] dark:border-[#333535] bg-white dark:bg-[#1f2020] cursor-pointer hover:bg-[#f5f3f3] dark:hover:bg-[#262727] transition-colors has-checked:border-[#dc143c] dark:has-checked:border-[#dc143c] has-checked:bg-[#fff1f0] dark:has-checked:bg-[#3b0b14] has-checked:text-[#dc143c] dark:has-checked:text-[#ffb3b3] has-checked:font-semibold">
                                <input type="radio" name="cleanliness_condition" value="{{ $cond }}" class="sr-only" {{ old('cleanliness_condition', $monitoring->cleanliness_condition) === $cond ? 'checked' : '' }} required>
                                <span class="text-xs">{{ $cond }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('cleanliness_condition')
                        <p class="text-[11px] text-[#ba1a1a] dark:text-[#ffb4ab] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status Kebersihan -->
                <div>
                    <label class="label-clean">
                        Status Pengerjaan <span class="text-[#ba1a1a]">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach(['Belum Diperiksa', 'Perlu Dibersihkan', 'Sedang Dibersihkan', 'Sudah Dibersihkan'] as $st)
                            <label class="relative flex items-center justify-center p-3 rounded-xl border border-[#eae8e7] dark:border-[#333535] bg-white dark:bg-[#1f2020] cursor-pointer hover:bg-[#f5f3f3] dark:hover:bg-[#262727] transition-colors has-checked:border-[#dc143c] dark:has-checked:border-[#dc143c] has-checked:bg-[#fff1f0] dark:has-checked:bg-[#3b0b14] has-checked:text-[#dc143c] dark:has-checked:text-[#ffb3b3] has-checked:font-semibold">
                                <input type="radio" name="status" value="{{ $st }}" class="sr-only" {{ old('status', $monitoring->status) === $st ? 'checked' : '' }} required>
                                <span class="text-xs text-center">{{ $st }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('status')
                        <p class="text-[11px] text-[#ba1a1a] dark:text-[#ffb4ab] mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <!-- Upload Foto Dokumentasi (Sebelum, Sesudah, dan Foto 360) -->
            <div class="space-y-4 pt-2">
                <div>
                    <h4 class="label-clean">Dokumentasi Foto Kebersihan & 360°</h4>
                    <p class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5]">Ganti foto jika ada pembaruan dokumen visual</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    
                    <!-- 1. Foto Sebelum Dibersihkan -->
                    <div x-data="{ preview: '{{ $monitoring->photo_before_url }}', fileName: '' }" class="bg-[#f5f3f3] dark:bg-[#262727] border border-[#eae8e7] dark:border-[#333535] rounded-xl p-4 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="text-xs font-semibold text-[#1b1c1c] dark:text-white flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-[#dc143c]"></span>
                                    <span>Foto Sebelum</span>
                                </label>
                                <span class="text-[10px] text-[#5c3f3f] dark:text-[#8c8282]">Maks 10MB</span>
                            </div>

                            <input type="file" 
                                   x-ref="fileInputBefore"
                                   id="photo_before_input"
                                   name="photo_before" 
                                   accept="image/*" 
                                   capture="environment" 
                                   @change="const file = $event.target.files[0]; if(file) { preview = URL.createObjectURL(file); fileName = file.name; }" 
                                   class="hidden">

                            <div x-show="!preview" class="py-4 text-center space-y-2">
                                <div class="w-10 h-10 rounded-xl bg-white dark:bg-[#1f2020] border border-[#eae8e7] dark:border-[#333535] text-[#dc143c] dark:text-[#ffb3b3] flex items-center justify-center mx-auto text-base">
                                    <i class="fa-solid fa-camera"></i>
                                </div>
                                <p class="text-[11px] text-[#5c3f3f] dark:text-[#8c8282]">Belum ada foto</p>
                                <div class="pt-1">
                                    <button type="button" 
                                            @click="$refs.fileInputBefore.click()" 
                                            class="w-full btn-outline-neutral text-xs">
                                        <i class="fa-solid fa-camera text-xs"></i>
                                        <span>Pilih Foto</span>
                                    </button>
                                </div>
                            </div>

                            <div x-show="preview" x-cloak class="space-y-2">
                                <div class="relative rounded-lg overflow-hidden border border-[#eae8e7] dark:border-[#333535] bg-black h-36 flex items-center justify-center">
                                    <img :src="preview" alt="Preview Foto Sebelum" class="w-full h-full object-cover">
                                    <span class="absolute bottom-1 left-1 px-1.5 py-0.5 rounded bg-[#dc143c] text-white font-semibold text-[9px]">Sebelum</span>
                                </div>
                                <div class="flex items-center justify-between gap-2">
                                    <button type="button" @click="$refs.fileInputBefore.click()" class="text-[11px] text-[#5c3f3f] dark:text-[#c8c6c5] hover:underline cursor-pointer">
                                        Ganti Foto
                                    </button>
                                    <button type="button" @click="preview = null; fileName = ''; $refs.fileInputBefore.value = ''" class="text-[#ba1a1a] text-[11px] font-semibold hover:underline cursor-pointer">
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                        @error('photo_before')
                            <p class="text-[11px] text-[#ba1a1a] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- 2. Foto Sesudah Dibersihkan -->
                    <div x-data="{ preview: '{{ $monitoring->photo_after_url }}', fileName: '' }" class="bg-[#f5f3f3] dark:bg-[#262727] border border-[#eae8e7] dark:border-[#333535] rounded-xl p-4 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="text-xs font-semibold text-[#1b1c1c] dark:text-white flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-[#00822d]"></span>
                                    <span>Foto Sesudah</span>
                                </label>
                                <span class="text-[10px] text-[#5c3f3f] dark:text-[#8c8282]">Maks 10MB</span>
                            </div>

                            <input type="file" 
                                   x-ref="fileInputAfter"
                                   id="photo_after_input"
                                   name="photo_after" 
                                   accept="image/*" 
                                   capture="environment" 
                                   @change="const file = $event.target.files[0]; if(file) { preview = URL.createObjectURL(file); fileName = file.name; }" 
                                   class="hidden">

                            <div x-show="!preview" class="py-4 text-center space-y-2">
                                <div class="w-10 h-10 rounded-xl bg-white dark:bg-[#1f2020] border border-[#eae8e7] dark:border-[#333535] text-[#00822d] dark:text-[#83fc8e] flex items-center justify-center mx-auto text-base">
                                    <i class="fa-solid fa-circle-check"></i>
                                </div>
                                <p class="text-[11px] text-[#5c3f3f] dark:text-[#8c8282]">Belum ada foto</p>
                                <div class="pt-1">
                                    <button type="button" 
                                            @click="$refs.fileInputAfter.click()" 
                                            class="w-full btn-outline-neutral text-xs">
                                        <i class="fa-solid fa-camera text-xs"></i>
                                        <span>Pilih Foto</span>
                                    </button>
                                </div>
                            </div>

                            <div x-show="preview" x-cloak class="space-y-2">
                                <div class="relative rounded-lg overflow-hidden border border-[#eae8e7] dark:border-[#333535] bg-black h-36 flex items-center justify-center">
                                    <img :src="preview" alt="Preview Foto Sesudah" class="w-full h-full object-cover">
                                    <span class="absolute bottom-1 left-1 px-1.5 py-0.5 rounded bg-[#00822d] text-white font-semibold text-[9px]">Sesudah</span>
                                </div>
                                <div class="flex items-center justify-between gap-2">
                                    <button type="button" @click="$refs.fileInputAfter.click()" class="text-[11px] text-[#5c3f3f] dark:text-[#c8c6c5] hover:underline cursor-pointer">
                                        Ganti Foto
                                    </button>
                                    <button type="button" @click="preview = null; fileName = ''; $refs.fileInputAfter.value = ''" class="text-[#ba1a1a] text-[11px] font-semibold hover:underline cursor-pointer">
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                        @error('photo_after')
                            <p class="text-[11px] text-[#ba1a1a] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- 3. Foto Ruangan (360° atau Standar) -->
                    <div x-data="{ 
                            preview: '{{ $monitoring->photo_360_url }}', 
                            fileName: '', 
                            photoType: '{{ old('photo_360_type', $monitoring->photo_360_type ?? 'panorama_360') }}',
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
                         class="bg-[#f5f3f3] dark:bg-[#262727] border border-[#eae8e7] dark:border-[#333535] rounded-xl p-4 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="text-xs font-semibold text-[#1b1c1c] dark:text-white flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full" :class="photoType === 'panorama_360' ? 'bg-[#dc143c]' : 'bg-[#0284c7]'"></span>
                                    <span>Foto Ruangan / 360°</span>
                                </label>
                                <span class="text-[10px] text-[#5c3f3f] dark:text-[#8c8282]">Maks 50MB</span>
                            </div>

                            <!-- Photo Type Selector -->
                            <div class="grid grid-cols-2 gap-1.5 mb-2.5">
                                <label class="flex items-center gap-1.5 p-1.5 rounded-lg border cursor-pointer text-[11px] transition-all"
                                       :class="photoType === 'panorama_360' ? 'bg-[#fff1f0] border-[#dc143c] text-[#dc143c] font-semibold dark:bg-[#3b0b14] dark:border-[#dc143c] dark:text-[#ffb3b3]' : 'border-[#eae8e7] text-[#5c3f3f] dark:border-[#333535] dark:text-[#c8c6c5] bg-white dark:bg-[#1f2020]'">
                                    <input type="radio" name="photo_360_type" value="panorama_360" x-model="photoType" class="hidden">
                                    <i class="fa-solid fa-panorama text-[#dc143c]"></i>
                                    <span>360° Panorama</span>
                                </label>
                                <label class="flex items-center gap-1.5 p-1.5 rounded-lg border cursor-pointer text-[11px] transition-all"
                                       :class="photoType === 'standard' ? 'bg-[#e0f2fe] border-[#0284c7] text-[#0284c7] font-semibold dark:bg-[#082f49] dark:border-[#0284c7] dark:text-[#7dd3fc]' : 'border-[#eae8e7] text-[#5c3f3f] dark:border-[#333535] dark:text-[#c8c6c5] bg-white dark:bg-[#1f2020]'">
                                    <input type="radio" name="photo_360_type" value="standard" x-model="photoType" class="hidden">
                                    <i class="fa-solid fa-image text-[#0284c7]"></i>
                                    <span>Standar HD</span>
                                </label>
                            </div>

                            <input type="file" 
                                   x-ref="fileInput360"
                                   id="photo_360_input"
                                   name="photo_360" 
                                   accept="image/*" 
                                   @change="onFileChange($event)" 
                                   class="hidden">

                            <div x-show="!preview" class="py-3 text-center space-y-2">
                                <div class="w-10 h-10 rounded-xl bg-white dark:bg-[#1f2020] border border-[#eae8e7] dark:border-[#333535] text-[#5c3f3f] dark:text-[#c8c6c5] flex items-center justify-center mx-auto text-base">
                                    <i class="fa-solid fa-camera"></i>
                                </div>
                                <p class="text-[11px] text-[#5c3f3f] dark:text-[#8c8282]">Pilih foto panorama atau sudut ruangan</p>
                                <div class="pt-1">
                                    <button type="button" 
                                            @click="$refs.fileInput360.click()" 
                                            class="w-full btn-outline-neutral text-xs">
                                        <i class="fa-solid fa-file-image text-xs"></i>
                                        <span>Pilih Foto</span>
                                    </button>
                                </div>
                            </div>

                            <div x-show="preview" x-cloak class="space-y-2">
                                <div class="relative rounded-lg overflow-hidden border border-[#eae8e7] dark:border-[#333535] bg-black h-32 flex items-center justify-center">
                                    <img :src="preview" alt="Preview Foto" class="w-full h-full object-cover">
                                    <span class="absolute bottom-1 left-1 px-1.5 py-0.5 rounded text-white font-semibold text-[9px]"
                                          :class="photoType === 'panorama_360' ? 'bg-[#dc143c]' : 'bg-[#0284c7]'"
                                          x-text="photoType === 'panorama_360' ? '360° Panorama' : 'Foto Standar HD'"></span>
                                </div>

                                <div x-show="ratioWarning && photoType === 'panorama_360'" class="p-2 rounded-lg bg-[#fef3c7] dark:bg-[#451a03] border border-[#fde68a] dark:border-[#78350f] text-[10px] text-[#b45309] dark:text-[#fcd34d]">
                                    <div class="flex items-center gap-1 font-semibold">
                                        <i class="fa-solid fa-triangle-exclamation"></i>
                                        <span>Rasio <span x-text="detectedRatio + ':1'"></span> bukan 2:1</span>
                                    </div>
                                    <div class="mt-1 flex items-center justify-between">
                                        <span>Ubah ke Foto Standar agar proporsional.</span>
                                        <button type="button" @click="photoType = 'standard'" class="ml-1 px-1.5 py-0.5 bg-[#0284c7] text-white font-semibold rounded text-[9px]">Ubah</button>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between gap-2 pt-1">
                                    <button type="button" @click="$refs.fileInput360.click()" class="text-[11px] text-[#5c3f3f] dark:text-[#c8c6c5] hover:underline cursor-pointer">
                                        Ganti Foto
                                    </button>
                                    <span class="text-[10px] text-[#5c3f3f] dark:text-[#8c8282] truncate max-w-[120px]" x-text="fileName || 'Tersimpan'"></span>
                                </div>
                            </div>
                        </div>
                        @error('photo_360')
                            <p class="text-[11px] text-[#ba1a1a] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </div>

            <!-- Catatan Pemeriksaan -->
            <div>
                <label for="notes" class="label-clean">
                    Catatan Temuan / Tindakan
                </label>
                <textarea id="notes" 
                          name="notes" 
                          rows="3" 
                          class="input-clean @error('notes') border-[#ba1a1a] @enderror">{{ old('notes', $monitoring->notes) }}</textarea>
                @error('notes')
                    <p class="text-[11px] text-[#ba1a1a] dark:text-[#ffb4ab] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="pt-4 border-t border-[#eae8e7] dark:border-[#2e3030] flex items-center justify-end gap-3">
                <a href="{{ route('monitoring.show', $monitoring->id) }}" class="btn-outline-neutral">
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
