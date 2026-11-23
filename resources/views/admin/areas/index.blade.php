@extends('layouts.app')

@section('title', 'Data Master Area')
@section('header_title', 'Master Data Area Kebersihan')
@section('header_subtitle', 'Kelola daftar seluruh ruangan dan fasilitas yang dipantau')

@section('content')
<div class="space-y-6">

    <!-- Action Bar & Filters -->
    <div class="card-clean p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <form method="GET" action="{{ route('admin.areas.index') }}" class="flex-1 flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-[#5c3f3f] dark:text-[#8c8282] text-xs"></i>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Cari nama area, lokasi, keterangan..." 
                       class="input-clean pl-9 text-xs">
            </div>

            <select name="status" onchange="this.form.submit()" class="input-clean text-xs sm:w-40">
                <option value="">Semua Status</option>
                <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif Saja</option>
                <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif Saja</option>
            </select>

            <button type="submit" class="btn-primary">
                <i class="fa-solid fa-filter text-xs"></i>
                <span>Filter</span>
            </button>

            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('admin.areas.index') }}" class="btn-outline-neutral">
                    <span>Reset</span>
                </a>
            @endif
        </form>

        <div class="flex items-center gap-2">
            <a href="{{ route('facility.index') }}" class="btn-secondary">
                <i class="fa-solid fa-map-location-dot text-xs"></i>
                <span>Denah & Galeri Ruangan</span>
            </a>
            <a href="{{ route('admin.areas.create') }}" class="btn-primary">
                <i class="fa-solid fa-plus text-xs"></i>
                <span>Tambah Area</span>
            </a>
        </div>
    </div>

    <!-- Area Table Card -->
    <div class="card-clean overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-[#1b1c1c] dark:text-[#f2f0f0]">
                <thead class="bg-[#f5f3f3] dark:bg-[#262727] text-[#5c3f3f] dark:text-[#c8c6c5] font-semibold uppercase text-[11px] tracking-wider border-b border-[#eae8e7] dark:border-[#2e3030]">
                    <tr>
                        <th class="py-3 px-4 text-center w-12">No</th>
                        <th class="py-3 px-4">Nama Area & Kode</th>
                        <th class="py-3 px-4">Lokasi & Lantai</th>
                        <th class="py-3 px-4 text-center">Foto Ruangan</th>
                        <th class="py-3 px-4 text-center">Total Riwayat</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#eae8e7] dark:divide-[#2e3030]">
                    @forelse($areas as $index => $area)
                        <tr class="hover:bg-[#fbf9f8] dark:hover:bg-[#262727]/60 transition-colors">
                            <td class="py-3.5 px-4 text-center font-medium text-[#5c3f3f] dark:text-[#8c8282]">
                                {{ $areas->firstItem() + $index }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-semibold text-[#1b1c1c] dark:text-white">
                                    <a href="{{ route('admin.areas.show', $area->id) }}" class="hover:text-[#dc143c] dark:hover:text-[#ffb3b3] transition-colors">
                                        {{ $area->name }}
                                    </a>
                                </div>
                                <span class="text-[11px] font-mono font-semibold text-[#dc143c] dark:text-[#ffb3b3]">{{ $area->code }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-[#5c3f3f] dark:text-[#c8c6c5]">
                                <div>
                                    <i class="fa-solid fa-location-dot text-[#dc143c] dark:text-[#ffb3b3] mr-1 text-[11px]"></i>
                                    {{ $area->location }}
                                </div>
                                <span class="text-[11px] text-[#5c3f3f] dark:text-[#8c8282]">Lantai {{ $area->floor ?? 1 }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($area->photo_url)
                                    <img src="{{ $area->photo_url }}" alt="Foto {{ $area->name }}" class="mx-auto h-12 w-16 rounded-lg object-cover">
                                @else
                                    <span class="text-[#5c3f3f] dark:text-[#8c8282] text-[11px]">-</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2.5 py-1 rounded-full bg-[#f5f3f3] dark:bg-[#262727] font-semibold text-[11px] text-[#1b1c1c] dark:text-[#f2f0f0] border border-[#eae8e7] dark:border-[#333535]">
                                    {{ $area->cleaning_monitorings_count }} kali
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($area->is_active)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#daffd5] text-[#006622] border border-[#b2e5be] dark:bg-[#003912] dark:text-[#83fc8e] dark:border-[#00531a]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#00822d]"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-[#f5f3f3] text-[#5c3f3f] border border-[#eae8e7] dark:bg-[#262727] dark:text-[#c8c6c5] dark:border-[#333535]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#916f6e]"></span>
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('admin.areas.show', $area->id) }}" 
                                       class="p-1.5 rounded-lg text-[#5c3f3f] hover:text-[#dc143c] hover:bg-[#fff1f0] dark:text-[#c8c6c5] dark:hover:text-[#ffb3b3] dark:hover:bg-[#3b0b14] transition-colors" title="Detail Area">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('admin.areas.qr.print', $area->id) }}" target="_blank"
                                       class="p-1.5 rounded-lg text-[#5c3f3f] hover:text-[#dc143c] hover:bg-[#fff1f0] dark:text-[#c8c6c5] dark:hover:text-[#ffb3b3] dark:hover:bg-[#3b0b14] transition-colors" title="Cetak QR Code">
                                        <i class="fa-solid fa-qrcode text-xs"></i>
                                    </a>
                                    <a href="{{ route('admin.areas.edit', $area->id) }}" 
                                       class="p-1.5 rounded-lg text-[#5c3f3f] hover:text-[#b45309] hover:bg-[#fef3c7] dark:text-[#c8c6c5] dark:hover:text-[#fcd34d] dark:hover:bg-[#451a03] transition-colors" title="Edit Area">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </a>
                                    <form action="{{ route('admin.areas.destroy', $area->id) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus area {{ $area->name }}? Seluruh riwayat pemeriksaan pada area ini juga akan terhapus.')" 
                                          class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-[#5c3f3f] hover:text-[#ba1a1a] hover:bg-[#ffdad6] dark:text-[#c8c6c5] dark:hover:text-[#ffb4ab] dark:hover:bg-[#410002] transition-colors cursor-pointer" title="Hapus Area">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-[#5c3f3f] dark:text-[#c8c6c5]">
                                <i class="fa-solid fa-map-location-dot text-3xl mb-2 block opacity-40"></i>
                                Tidak ada data area yang sesuai dengan kriteria pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($areas->hasPages())
            <div class="p-4 border-t border-[#eae8e7] dark:border-[#2e3030]">
                {{ $areas->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
