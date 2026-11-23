@extends('layouts.app')

@section('title', 'Pemeriksaan Kebersihan Baru')
@section('header_title', 'Form Pemeriksaan Kebersihan')
@section('header_subtitle', 'Catat hasil monitoring kondisi fisik area, jam pengerjaan, dan upload foto bukti kebersihan')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="card-clean overflow-hidden">
        
        <!-- Form Header (Corporate Clean) -->
        <div class="p-6 border-b border-[#eae8e7] dark:border-[#2e3030] flex items-center justify-between bg-[#f5f3f3] dark:bg-[#262727]">
            <div>
                <h3 class="font-heading text-base font-semibold text-[#1b1c1c] dark:text-white">Formulir Pemeriksaan Area</h3>
                <p class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5] mt-0.5">Lengkapi data kondisi kebersihan, jam pembersihan, dan lampirkan foto sebelum dan sesudah</p>
            </div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#fff1f0] text-[#dc143c] border border-[#fecdd3] dark:bg-[#3b0b14] dark:text-[#ffb3b3] dark:border-[#831843]">
                <i class="fa-regular fa-clock"></i>
                <span>Waktu Real-Time</span>
            </span>
        </div>

        <form action="{{ route('monitoring.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf

            <!-- Meta Data Otomatis (Petugas & Tanggal) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-xl bg-[#f5f3f3] dark:bg-[#262727] border border-[#eae8e7] dark:border-[#333535]">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-[#dc143c] text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-xs">
                        <i class="fa-solid fa-user-check"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-semibold text-[#5c3f3f] dark:text-[#8c8282] uppercase tracking-wider block">Petugas Pemeriksa</span>
                        <span class="text-xs font-semibold text-[#1b1c1c] dark:text-white">{{ Auth::user()->name }} ({{ ucfirst(Auth::user()->role) }})</span>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-white dark:bg-[#1f2020] text-[#5c3f3f] dark:text-[#c8c6c5] border border-[#eae8e7] dark:border-[#333535] flex items-center justify-center text-xs shrink-0">
                        <i class="fa-regular fa-calendar-check"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-semibold text-[#5c3f3f] dark:text-[#8c8282] uppercase tracking-wider block">Tanggal Pemeriksaan</span>
                        <span class="text-xs font-semibold text-[#1b1c1c] dark:text-white">{{ $currentTime->translatedFormat('d F Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Pilih Area -->
            <div>
                <label for="area_combobox" class="label-clean">
                    Pilih Area yang Diperiksa <span class="text-[#ba1a1a]">*</span>
                </label>
                @include('monitoring._area_combobox', [
                    'areas'          => $areas,
                    'selectedAreaId' => $selectedAreaId ?? null,
                    'customAreaName' => old('custom_area_name'),
                ])
            </div>

            <!-- Jam Mulai & Jam Selesai Pembersihan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-xl bg-[#f5f3f3] dark:bg-[#262727] border border-[#eae8e7] dark:border-[#333535]">
                <!-- Jam Mulai (Sebelum Pembersihan) -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="start_time" class="label-clean !mb-0 flex items-center gap-1.5">
                            <i class="fa-regular fa-clock text-[#dc143c]"></i>
                            <span>Jam Mulai (Sebelum)</span>
                        </label>
                        <span class="text-[10px] text-[#5c3f3f] dark:text-[#8c8282]">Mulai pembersihan</span>
                    </div>
                    <input type="time" 
                           id="start_time" 
                           name="start_time" 
                           value="{{ old('start_time', $currentTime->copy()->subMinutes(25)->format('H:i')) }}" 
                           class="input-clean font-semibold">
                    <p class="text-[10px] text-[#5c3f3f] dark:text-[#8c8282] mt-1">Waktu mulai tindakan pembersihan.</p>
                </div>

                <!-- Jam Selesai (Sesudah Pembersihan) -->
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
                           value="{{ old('end_time', $currentTime->format('H:i')) }}" 
                           class="input-clean font-semibold">
                    <p class="text-[10px] text-[#5c3f3f] dark:text-[#8c8282] mt-1">Waktu selesai seluruh tindakan pembersihan.</p>
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
                                <input type="radio" name="cleanliness_condition" value="{{ $cond }}" class="sr-only" {{ old('cleanliness_condition', 'Bersih') === $cond ? 'checked' : '' }} required>
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
                                <input type="radio" name="status" value="{{ $st }}" class="sr-only" {{ old('status', 'Sudah Dibersihkan') === $st ? 'checked' : '' }} required>
                                <span class="text-xs text-center">{{ $st }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('status')
                        <p class="text-[11px] text-[#ba1a1a] dark:text-[#ffb4ab] mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <!-- Upload Foto Dokumentasi (Sebelum dan Sesudah) -->
            <div class="space-y-4 pt-2">
                <div>
                    <h4 class="label-clean">Dokumentasi Foto Kebersihan</h4>
                    <p class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5]">Lampirkan foto fisik sebelum dan sesudah pembersihan (Maks 10MB)</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    
                    <!-- 1. Foto Sebelum Dibersihkan -->
                    <div x-data="{ preview: null, fileName: '' }" class="bg-[#f5f3f3] dark:bg-[#262727] border border-[#eae8e7] dark:border-[#333535] rounded-xl p-4 flex flex-col justify-between">
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
                                <p class="text-[11px] text-[#5c3f3f] dark:text-[#8c8282]">Kondisi ruangan awal</p>
                                <div class="pt-1">
                                    <button type="button" 
                                            @click="$refs.fileInputBefore.click()" 
                                            class="w-full btn-outline-neutral text-xs">
                                        <i class="fa-solid fa-camera text-xs"></i>
                                        <span>Ambil Foto</span>
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
                    <div x-data="{ preview: null, fileName: '' }" class="bg-[#f5f3f3] dark:bg-[#262727] border border-[#eae8e7] dark:border-[#333535] rounded-xl p-4 flex flex-col justify-between">
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
                                <p class="text-[11px] text-[#5c3f3f] dark:text-[#8c8282]">Hasil akhir bersih</p>
                                <div class="pt-1">
                                    <button type="button" 
                                            @click="$refs.fileInputAfter.click()" 
                                            class="w-full btn-outline-neutral text-xs">
                                        <i class="fa-solid fa-camera text-xs"></i>
                                        <span>Ambil Foto</span>
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

                </div>
            </div>

            <!-- Catatan Pemeriksaan -->
            <div>
                <label for="notes" class="label-clean">
                    Catatan Temuan / Tindakan (Opsional)
                </label>
                <textarea id="notes" 
                          name="notes" 
                          rows="3" 
                          placeholder="Tuliskan keterangan detail hasil pemeriksaan, misal: 'Lantai telah dipel dengan desinfektan, sabun wastafel telah diisi ulang'..." 
                          class="input-clean @error('notes') border-[#ba1a1a] @enderror">{{ old('notes') }}</textarea>
                @error('notes')
                    <p class="text-[11px] text-[#ba1a1a] dark:text-[#ffb4ab] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="pt-4 border-t border-[#eae8e7] dark:border-[#2e3030] flex items-center justify-end gap-3">
                <a href="{{ url()->previous() }}" class="btn-outline-neutral">
                    Batal
                </a>
                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-cloud-arrow-up"></i>
                    <span>Simpan Hasil Pemeriksaan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
