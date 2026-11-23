@extends('layouts.app')

@section('title', 'Tambah Area Baru')
@section('header_title', 'Tambah Area Kebersihan')
@section('header_subtitle', 'Daftarkan ruangan atau zona baru pada salah satu dari empat lantai gedung')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="card-clean overflow-hidden">
        <div class="p-6 border-b border-[#eae8e7] dark:border-[#2e3030] flex items-center justify-between bg-[#f5f3f3] dark:bg-[#262727]">
            <div>
                <h3 class="font-heading text-base font-semibold text-[#1b1c1c] dark:text-white">Formulir Pendaftaran Area Baru</h3>
                <p class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5] mt-0.5">Lengkapi nama, lokasi, dan lantai ruangan.</p>
            </div>
            <a href="{{ route('admin.areas.index') }}" class="btn-outline-neutral text-xs">
                <i class="fa-solid fa-arrow-left text-[10px]"></i>
                <span>Kembali</span>
            </a>
        </div>

        <form action="{{ route('admin.areas.store') }}" method="POST" class="p-6 space-y-5">
            @csrf

            <div>
                <label for="name" class="label-clean">Nama Area <span class="text-[#ba1a1a]">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                       placeholder="Contoh: Toilet Lantai 1 atau Lobby Utama"
                       class="input-clean @error('name') border-[#ba1a1a] @enderror">
                @error('name')
                    <p class="text-[11px] text-[#ba1a1a] dark:text-[#ffb4ab] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="location" class="label-clean">Lokasi Gedung <span class="text-[#ba1a1a]">*</span></label>
                    <input type="text" id="location" name="location" value="{{ old('location') }}" required
                           placeholder="Contoh: Gedung A Sayap Kiri"
                           class="input-clean @error('location') border-[#ba1a1a] @enderror">
                    @error('location')
                        <p class="text-[11px] text-[#ba1a1a] dark:text-[#ffb4ab] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="floor" class="label-clean">Lantai Gedung <span class="text-[#ba1a1a]">*</span></label>
                    <select id="floor" name="floor" required class="input-clean">
                        @foreach(range(1, 4) as $floor)
                            <option value="{{ $floor }}" {{ old('floor', 1) == $floor ? 'selected' : '' }}>Lantai {{ $floor }}</option>
                        @endforeach
                    </select>
                    @error('floor')
                        <p class="text-[11px] text-[#ba1a1a] dark:text-[#ffb4ab] mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="description" class="label-clean">Keterangan Fasilitas (Opsional)</label>
                <textarea id="description" name="description" rows="3"
                          placeholder="Deskripsi fasilitas di dalam area..."
                          class="input-clean @error('description') border-[#ba1a1a] @enderror">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-[11px] text-[#ba1a1a] dark:text-[#ffb4ab] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                       class="w-4 h-4 text-[#dc143c] rounded focus:ring-[#dc143c] border-[#eae8e7] dark:border-[#333535] dark:bg-[#1f2020]">
                <span>
                    <span class="text-sm font-semibold text-[#1b1c1c] dark:text-white">Status Area Aktif</span>
                    <span class="block text-xs text-[#5c3f3f] dark:text-[#c8c6c5]">Area aktif muncul dalam formulir pemeriksaan rutin.</span>
                </span>
            </label>

            <div class="pt-4 border-t border-[#eae8e7] dark:border-[#2e3030] flex items-center justify-end gap-3">
                <a href="{{ route('admin.areas.index') }}" class="btn-outline-neutral">Batal</a>
                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Area</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
