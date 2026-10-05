@extends('layouts.app')

@section('title', 'Edit Data Monitoring')
@section('header_title', 'Edit Pemeriksaan Kebersihan')
@section('header_subtitle', 'Perbarui data temuan atau ganti foto hasil pemeriksaan')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        
        <!-- Form Header -->
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Perbarui Hasil Pemeriksaan</h3>
                <p class="text-xs text-slate-500 mt-0.5">ID Monitoring: #{{ $monitoring->id }} • {{ $monitoring->area->name ?? '' }}</p>
            </div>
            <a href="{{ route('monitoring.show', $monitoring->id) }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-left text-[10px]"></i>
                <span>Kembali</span>
            </a>
        </div>

        <form action="{{ route('monitoring.update', $monitoring->id) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <!-- Meta Data (Petugas & Waktu) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-xl bg-slate-50 border border-slate-200/70">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-teal-600 text-white flex items-center justify-center font-bold text-sm shrink-0">
                        <i class="fa-solid fa-user-check"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Petugas Pencatat</span>
                        <span class="text-xs font-bold text-slate-800">{{ $monitoring->user->name ?? '-' }}</span>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-slate-200 text-slate-700 flex items-center justify-center text-sm shrink-0">
                        <i class="fa-regular fa-calendar-check"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Waktu Pemeriksaan</span>
                        <span class="text-xs font-bold text-slate-800">{{ \Carbon\Carbon::parse($monitoring->check_date)->format('d F Y') }} • {{ substr($monitoring->check_time, 0, 5) }} WIB</span>
                    </div>
                </div>
            </div>

            <!-- Pilih Area -->
            <div>
                <label for="area_id" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Area yang Diperiksa <span class="text-rose-500">*</span>
                </label>
                <select id="area_id" name="area_id" required class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 @error('area_id') border-rose-500 @enderror">
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}" {{ old('area_id', $monitoring->area_id) == $area->id ? 'selected' : '' }}>
                            {{ $area->name }} (Lokasi: {{ $area->location }})
                        </option>
                    @endforeach
                </select>
                @error('area_id')
                    <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Kondisi Kebersihan & Status -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                
                <!-- Kondisi Kebersihan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Kondisi Kebersihan <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach(['Bersih', 'Cukup Bersih', 'Kotor', 'Sangat Kotor'] as $cond)
                            <label class="relative flex items-center justify-center p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 transition-colors has-checked:border-teal-500 has-checked:bg-teal-50/50 has-checked:ring-2 has-checked:ring-teal-500">
                                <input type="radio" name="cleanliness_condition" value="{{ $cond }}" class="sr-only" {{ old('cleanliness_condition', $monitoring->cleanliness_condition) === $cond ? 'checked' : '' }} required>
                                <span class="text-xs font-bold text-slate-700">{{ $cond }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('cleanliness_condition')
                        <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Status Kebersihan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Status Pengerjaan <span class="text-rose-500">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach(['Belum Diperiksa', 'Perlu Dibersihkan', 'Sedang Dibersihkan', 'Sudah Dibersihkan'] as $st)
                            <label class="relative flex items-center justify-center p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50 transition-colors has-checked:border-teal-500 has-checked:bg-teal-50/50 has-checked:ring-2 has-checked:ring-teal-500">
                                <input type="radio" name="status" value="{{ $st }}" class="sr-only" {{ old('status', $monitoring->status) === $st ? 'checked' : '' }} required>
                                <span class="text-xs font-bold text-slate-700 text-center">{{ $st }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('status')
                        <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <!-- Upload Foto Sebelum & Sesudah (Mobile Optimized) -->
            <div class="space-y-4 pt-2">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider">Dokumentasi Foto Kebersihan</h4>
                        <p class="text-[11px] text-slate-500">Ambil foto baru menggunakan kamera HP atau upload dari galeri untuk mengganti</p>
                    </div>
                    <span class="text-[10px] font-bold text-teal-700 bg-teal-50 border border-teal-200 px-2.5 py-1 rounded-full flex items-center gap-1">
                        <i class="fa-solid fa-camera"></i>
                        <span>Kamera HP Siap</span>
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    
                    <!-- 1. Foto Sebelum -->
                    <div x-data="{ preview: '{{ $monitoring->photo_before_url }}', fileName: '' }" class="bg-slate-50/70 border-2 border-dashed border-slate-300 hover:border-teal-500 rounded-2xl p-4 transition-all">
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                <span>Foto Sebelum Tindakan</span>
                            </label>
                            <span class="text-[10px] font-medium text-slate-400">JPG/PNG (Maks 2MB)</span>
                        </div>

                        <!-- Empty State -->
                        <template x-if="!preview">
                            <div class="py-4 text-center space-y-3">
                                <div class="w-12 h-12 rounded-2xl bg-rose-50 border border-rose-200 text-rose-600 flex items-center justify-center mx-auto text-xl shadow-xs">
                                    <i class="fa-solid fa-camera-retro"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-slate-700">Foto Kondisi Awal</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Belum ada foto sebelum yang dilampirkan</p>
                                </div>
                                <div class="pt-1">
                                    <label class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 active:bg-black text-white text-xs font-bold shadow-md cursor-pointer transition-all transform active:scale-95 w-full sm:w-auto">
                                        <i class="fa-solid fa-camera"></i>
                                        <span>Ambil Foto Sebelum</span>
                                        <input type="file" 
                                               name="photo_before" 
                                               accept="image/*" 
                                               capture="environment"
                                               @change="const file = $event.target.files[0]; if(file) { preview = URL.createObjectURL(file); fileName = file.name; }"
                                               class="hidden">
                                    </label>
                                </div>
                            </div>
                        </template>

                        <!-- Preview State -->
                        <template x-if="preview">
                            <div class="space-y-3">
                                <div class="relative rounded-xl overflow-hidden border border-slate-200 bg-black max-h-56 flex items-center justify-center">
                                    <img :src="preview" alt="Foto Sebelum" class="w-full h-48 object-cover">
                                    <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/80 to-transparent p-2.5 flex items-center justify-between text-white text-[11px]">
                                        <span class="truncate font-medium max-w-[180px]" x-text="fileName || 'Foto Sebelum Tersimpan'"></span>
                                        <span class="px-1.5 py-0.5 rounded bg-rose-500/80 font-bold text-[10px]">Sebelum</span>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between gap-2 pt-1">
                                    <label class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg bg-teal-50 hover:bg-teal-100 text-teal-700 text-xs font-bold border border-teal-200 cursor-pointer transition-colors">
                                        <i class="fa-solid fa-camera-rotate"></i>
                                        <span>Ambil / Ganti Foto</span>
                                        <input type="file" 
                                               name="photo_before" 
                                               accept="image/*" 
                                               capture="environment"
                                               @change="const file = $event.target.files[0]; if(file) { preview = URL.createObjectURL(file); fileName = file.name; }"
                                               class="hidden">
                                    </label>
                                </div>
                            </div>
                        </template>

                        @error('photo_before')
                            <p class="text-[11px] text-rose-600 mt-2 font-medium flex items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                    <!-- 2. Foto Sesudah -->
                    <div x-data="{ preview: '{{ $monitoring->photo_after_url }}', fileName: '' }" class="bg-slate-50/70 border-2 border-dashed border-slate-300 hover:border-emerald-500 rounded-2xl p-4 transition-all">
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <span>Foto Sesudah Tindakan</span>
                            </label>
                            <span class="text-[10px] font-medium text-slate-400">JPG/PNG (Maks 2MB)</span>
                        </div>

                        <!-- Empty State -->
                        <template x-if="!preview">
                            <div class="py-4 text-center space-y-3">
                                <div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-600 flex items-center justify-center mx-auto text-xl shadow-xs">
                                    <i class="fa-solid fa-circle-check"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-slate-700">Foto Hasil Akhir</p>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Belum ada foto sesudah yang dilampirkan</p>
                                </div>
                                <div class="pt-1">
                                    <label class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 active:bg-teal-800 text-white text-xs font-bold shadow-md shadow-teal-500/20 cursor-pointer transition-all transform active:scale-95 w-full sm:w-auto">
                                        <i class="fa-solid fa-camera"></i>
                                        <span>Ambil Foto Sesudah</span>
                                        <input type="file" 
                                               name="photo_after" 
                                               accept="image/*" 
                                               capture="environment"
                                               @change="const file = $event.target.files[0]; if(file) { preview = URL.createObjectURL(file); fileName = file.name; }"
                                               class="hidden">
                                    </label>
                                </div>
                            </div>
                        </template>

                        <!-- Preview State -->
                        <template x-if="preview">
                            <div class="space-y-3">
                                <div class="relative rounded-xl overflow-hidden border border-slate-200 bg-black max-h-56 flex items-center justify-center">
                                    <img :src="preview" alt="Foto Sesudah" class="w-full h-48 object-cover">
                                    <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/80 to-transparent p-2.5 flex items-center justify-between text-white text-[11px]">
                                        <span class="truncate font-medium max-w-[180px]" x-text="fileName || 'Foto Sesudah Tersimpan'"></span>
                                        <span class="px-1.5 py-0.5 rounded bg-emerald-500/80 font-bold text-[10px]">Sesudah</span>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between gap-2 pt-1">
                                    <label class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-lg bg-teal-50 hover:bg-teal-100 text-teal-700 text-xs font-bold border border-teal-200 cursor-pointer transition-colors">
                                        <i class="fa-solid fa-camera-rotate"></i>
                                        <span>Ambil / Ganti Foto</span>
                                        <input type="file" 
                                               name="photo_after" 
                                               accept="image/*" 
                                               capture="environment"
                                               @change="const file = $event.target.files[0]; if(file) { preview = URL.createObjectURL(file); fileName = file.name; }"
                                               class="hidden">
                                    </label>
                                </div>
                            </div>
                        </template>

                        @error('photo_after')
                            <p class="text-[11px] text-rose-600 mt-2 font-medium flex items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation"></i>
                                <span>{{ $message }}</span>
                            </p>
                        @enderror
                    </div>

                </div>
            </div>

            <!-- Catatan Pemeriksaan -->
            <div>
                <label for="notes" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Catatan Temuan / Tindakan
                </label>
                <textarea id="notes" 
                          name="notes" 
                          rows="3" 
                          class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 @error('notes') border-rose-500 @enderror">{{ old('notes', $monitoring->notes) }}</textarea>
                @error('notes')
                    <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Action Buttons -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('monitoring.show', $monitoring->id) }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-7 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-md shadow-teal-500/20 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
