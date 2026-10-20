@extends('layouts.app')

@section('title', 'Detail Area - ' . $area->name)
@section('header_title', 'Detail Area Kebersihan')
@section('header_subtitle', 'Informasi lengkap dan histori pemeriksaan pada area')

@section('content')
<div class="space-y-6">

    <!-- Area Information Card -->
    <div class="card-clean p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-[#eae8e7] dark:border-[#2e3030]">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-[#fff1f0] text-[#dc143c] dark:bg-[#3b0b14] dark:text-[#ffb3b3] border border-[#fecdd3] dark:border-[#831843] flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-map-location-dot"></i>
                </div>
                <div>
                    <div class="flex items-center gap-3">
                        <h2 class="font-heading text-xl font-semibold text-[#1b1c1c] dark:text-white">{{ $area->name }}</h2>
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
                    </div>
                    <p class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5] mt-1 flex items-center gap-1.5">
                        <i class="fa-solid fa-location-dot text-[#dc143c] dark:text-[#ffb3b3]"></i>
                        <span>{{ $area->location }} &bull; Lantai {{ $area->floor ?? 1 }} &bull; Kode: {{ $area->code }}</span>
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('monitoring.create', ['area_id' => $area->id]) }}" class="btn-primary">
                    <i class="fa-solid fa-clipboard-check text-xs"></i>
                    <span>Catat Pemeriksaan</span>
                </a>
                <a href="{{ route('admin.areas.edit', $area->id) }}" class="btn-outline-neutral">
                    <i class="fa-solid fa-pen-to-square text-xs"></i>
                    <span>Edit</span>
                </a>
                <a href="{{ route('admin.areas.index') }}" class="btn-outline-neutral">
                    <span>Kembali</span>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-6">
            <div>
                <span class="text-[11px] font-semibold text-[#5c3f3f] dark:text-[#8c8282] uppercase tracking-wider block">Keterangan / Fasilitas</span>
                <p class="text-xs text-[#1b1c1c] dark:text-[#f2f0f0] mt-1.5 leading-relaxed bg-[#f5f3f3] dark:bg-[#262727] p-3 rounded-xl border border-[#eae8e7] dark:border-[#333535]">
                    {{ $area->description ?: 'Tidak ada catatan atau deskripsi tambahan untuk area ini.' }}
                </p>
            </div>

            <div>
                <span class="text-[11px] font-semibold text-[#5c3f3f] dark:text-[#8c8282] uppercase tracking-wider block">Total Histori Pemeriksaan</span>
                <p class="font-heading text-2xl font-semibold text-[#1b1c1c] dark:text-white mt-1">
                    {{ $area->cleaning_monitorings_count }} <span class="font-sans text-xs font-normal text-[#5c3f3f] dark:text-[#c8c6c5]">kali diperiksa</span>
                </p>
            </div>

            <div>
                <span class="text-[11px] font-semibold text-[#5c3f3f] dark:text-[#8c8282] uppercase tracking-wider block">Terdaftar Sejak</span>
                <p class="text-sm font-medium text-[#1b1c1c] dark:text-[#f2f0f0] mt-1">
                    {{ $area->created_at->translatedFormat('d F Y (H:i)') }}
                </p>
            </div>
        </div>
    </div>

    <!-- Riwayat Pemeriksaan di Area Ini -->
    <div class="card-clean overflow-hidden">
        <div class="p-5 border-b border-[#eae8e7] dark:border-[#2e3030]">
            <h3 class="font-heading text-sm font-semibold text-[#1b1c1c] dark:text-white">Riwayat Pemeriksaan Pada Area Ini</h3>
            <p class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5]">Daftar rekaman kebersihan khusus untuk {{ $area->name }}</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-[#1b1c1c] dark:text-[#f2f0f0]">
                <thead class="bg-[#f5f3f3] dark:bg-[#262727] text-[#5c3f3f] dark:text-[#c8c6c5] font-semibold uppercase text-[11px] tracking-wider border-b border-[#eae8e7] dark:border-[#2e3030]">
                    <tr>
                        <th class="py-3 px-4 text-center w-12">No</th>
                        <th class="py-3 px-4">Petugas</th>
                        <th class="py-3 px-4">Kondisi</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Tanggal & Jam</th>
                        <th class="py-3 px-4">Catatan</th>
                        <th class="py-3 px-4 text-center">Foto</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#eae8e7] dark:divide-[#2e3030]">
                    @forelse($monitorings as $index => $item)
                        <tr class="hover:bg-[#fbf9f8] dark:hover:bg-[#262727]/60 transition-colors">
                            <td class="py-3.5 px-4 text-center font-medium text-[#5c3f3f] dark:text-[#8c8282]">{{ $index + 1 }}</td>
                            <td class="py-3.5 px-4 font-semibold text-[#1b1c1c] dark:text-white">{{ $item->user->name ?? '-' }}</td>
                            <td class="py-3.5 px-4">
                                <x-badge-condition :condition="$item->cleanliness_condition" />
                            </td>
                            <td class="py-3.5 px-4">
                                <x-badge-status :status="$item->status" />
                            </td>
                            <td class="py-3.5 px-4 text-[#5c3f3f] dark:text-[#c8c6c5]">
                                {{ \Carbon\Carbon::parse($item->check_date)->format('d/m/Y') }}
                                <span class="text-[11px] text-[#5c3f3f] dark:text-[#8c8282] block">{{ substr($item->check_time, 0, 5) }} WIB</span>
                            </td>
                            <td class="py-3.5 px-4 text-[#5c3f3f] dark:text-[#c8c6c5] max-w-xs truncate">
                                {{ $item->notes ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($item->photo_before || $item->photo_after)
                                    <span class="inline-flex items-center gap-1 text-[#dc143c] dark:text-[#ffb3b3] font-semibold text-[11px]">
                                        <i class="fa-solid fa-image"></i>
                                        <span>Tersedia</span>
                                    </span>
                                @else
                                    <span class="text-[#5c3f3f] dark:text-[#8c8282] text-xs">-</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('monitoring.show', $item->id) }}" 
                                   class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold text-[#dc143c] dark:text-[#ffb3b3] hover:bg-[#fff1f0] dark:hover:bg-[#3b0b14] transition-colors">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                    <span>Detail</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-10 text-center text-[#5c3f3f] dark:text-[#c8c6c5]">
                                Belum ada riwayat pemeriksaan pada area ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($monitorings->hasPages())
            <div class="p-4 border-t border-[#eae8e7] dark:border-[#2e3030]">
                {{ $monitorings->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
