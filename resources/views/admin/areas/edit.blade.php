@extends('layouts.app')

@section('title', 'Edit Area - ' . $area->name)
@section('header_title', 'Edit Area Kebersihan')
@section('header_subtitle', 'Perbarui nama, lokasi, lantai, atau keterangan ruangan')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="card-clean overflow-hidden">
        <div class="p-6 border-b border-[#eae8e7] dark:border-[#2e3030] flex items-center justify-between bg-[#f5f3f3] dark:bg-[#262727]">
            <div>
                <h3 class="font-heading text-base font-semibold text-[#1b1c1c] dark:text-white">Perbarui Data Area</h3>
                <p class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5] mt-0.5">{{ $area->name }} ({{ $area->code }})</p>
            </div>
            <a href="{{ route('admin.areas.index') }}" class="btn-outline-neutral text-xs">
                <i class="fa-solid fa-arrow-left text-[10px]"></i>
                <span>Kembali</span>
            </a>
        </div>

        <form action="{{ route('admin.areas.update', $area->id) }}" method="POST" class="p-6 space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="label-clean">Nama Area <span class="text-[#ba1a1a]">*</span></label>
                <input type="text" id="name" name="name" value="{{ old('name', $area->name) }}" required
                       class="input-clean @error('name') border-[#ba1a1a] @enderror">
                @error('name')
                    <p class="text-[11px] text-[#ba1a1a] dark:text-[#ffb4ab] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="location" class="label-clean">Lokasi Gedung <span class="text-[#ba1a1a]">*</span></label>
                    <input type="text" id="location" name="location" value="{{ old('location', $area->location) }}" required
                           class="input-clean @error('location') border-[#ba1a1a] @enderror">
                    @error('location')
                        <p class="text-[11px] text-[#ba1a1a] dark:text-[#ffb4ab] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="floor" class="label-clean">Lantai Gedung <span class="text-[#ba1a1a]">*</span></label>
                    <select id="floor" name="floor" required class="input-clean">
                        @foreach($floorLabels as $floor => $label)
                            <option value="{{ $floor }}" {{ old('floor', $area->floor ?? 1) == $floor ? 'selected' : '' }}>{{ $label }}</option>
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
                          class="input-clean @error('description') border-[#ba1a1a] @enderror">{{ old('description', $area->description) }}</textarea>
                @error('description')
                    <p class="text-[11px] text-[#ba1a1a] dark:text-[#ffb4ab] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $area->is_active) ? 'checked' : '' }}
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
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
