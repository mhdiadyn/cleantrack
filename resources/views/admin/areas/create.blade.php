@extends('layouts.app')

@section('title', 'Tambah Area Baru')
@section('header_title', 'Tambah Area Kebersihan')
@section('header_subtitle', 'Daftarkan ruangan atau zona baru yang perlu dimonitor')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-900">Formulir Pendaftaran Area</h3>
            <a href="{{ route('admin.areas.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-left text-[10px]"></i>
                <span>Kembali</span>
            </a>
        </div>

        <form action="{{ route('admin.areas.store') }}" method="POST" class="p-6 space-y-5">
            @csrf

            <!-- Nama Area -->
            <div>
                <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nama Area <span class="text-rose-500">*</span>
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="{{ old('name') }}" 
                       required 
                       placeholder="Contoh: Toilet Lantai 3, Ruang Rapat Kartini, dsb" 
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 @error('name') border-rose-500 @enderror">
                @error('name')
                    <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Lokasi -->
            <div>
                <label for="location" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Lokasi Gedung / Posisi <span class="text-rose-500">*</span>
                </label>
                <input type="text" 
                       id="location" 
                       name="location" 
                       value="{{ old('location') }}" 
                       required 
                       placeholder="Contoh: Gedung B Lantai 2 Sayap Kanan" 
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 @error('location') border-rose-500 @enderror">
                @error('location')
                    <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Keterangan / Catatan Fasilitas -->
            <div>
                <label for="description" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Keterangan Fasilitas (Opsional)
                </label>
                <textarea id="description" 
                          name="description" 
                          rows="3" 
                          placeholder="Deskripsi fasilitas di dalam area (misal: 2 wastafel, 3 bilik toilet, karpet lantai)..." 
                          class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 @error('description') border-rose-500 @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Status Aktif Switch -->
            <div class="pt-2">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-5 h-5 text-teal-600 rounded focus:ring-teal-500 border-slate-300">
                    <div>
                        <span class="text-sm font-bold text-slate-800">Status Area Aktif</span>
                        <p class="text-xs text-slate-500">Area aktif akan muncul dalam daftar pemeriksaan harian petugas.</p>
                    </div>
                </label>
            </div>

            <!-- Action Buttons -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.areas.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-md shadow-teal-500/20 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Area</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
