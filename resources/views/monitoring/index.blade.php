@extends('layouts.app')

@section('title', 'Riwayat Monitoring Kebersihan')
@section('header_title', 'Riwayat Seluruh Monitoring')
@section('header_subtitle', 'Pantau dan telusuri seluruh catatan pemeriksaan kebersihan yang dilakukan petugas')

@section('content')
<div class="space-y-6">

    <!-- Filters & Search Bar -->
    <div class="card-clean p-5">
        <form method="GET" action="{{ route('admin.monitoring.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
                
                <!-- Search Input -->
                <div class="relative lg:col-span-2">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-[#5c3f3f] dark:text-[#8c8282] text-xs"></i>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Cari area, petugas, catatan..." 
                           class="input-clean pl-9 text-xs">
                </div>

                <!-- Area Filter -->
                <div>
                    <select name="area_id" class="input-clean text-xs">
                        <option value="">Semua Area</option>
                        @foreach($areas as $area)
                            <option value="{{ $area->id }}" {{ request('area_id') == $area->id ? 'selected' : '' }}>
                                {{ $area->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <select name="status" class="input-clean text-xs">
                        <option value="">Semua Status</option>
                        @foreach(['Belum Diperiksa', 'Perlu Dibersihkan', 'Sedang Dibersihkan', 'Sudah Dibersihkan'] as $st)
                            <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ $st }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Date Filter -->
                <div>
                    <input type="date" 
                           name="date" 
                           value="{{ request('date') }}" 
                           class="input-clean text-xs">
                </div>
            </div>

            <!-- Submit & Reset -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-3 border-t border-[#eae8e7] dark:border-[#2e3030]">
                <div class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5]">
                    Menampilkan <span class="font-semibold text-[#1b1c1c] dark:text-white">{{ $monitorings->total() }}</span> data hasil pemeriksaan
                </div>
                <div class="flex items-center gap-2">
                    @if(request()->hasAny(['search', 'area_id', 'status', 'date']))
                        <a href="{{ route('admin.monitoring.index') }}" class="btn-outline-neutral text-xs">
                            Reset Filter
                        </a>
                    @endif
                    <button type="submit" class="btn-primary text-xs">
                        <i class="fa-solid fa-filter text-xs"></i>
                        <span>Terapkan Filter</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Monitoring Records Table -->
    <div class="card-clean overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-[#1b1c1c] dark:text-[#f2f0f0]">
                <thead class="bg-[#f5f3f3] dark:bg-[#262727] text-[#5c3f3f] dark:text-[#c8c6c5] font-semibold uppercase text-[11px] tracking-wider border-b border-[#eae8e7] dark:border-[#2e3030]">
                    <tr>
                        <th class="py-3 px-4 text-center w-12">No</th>
                        <th class="py-3 px-4">Area & Lokasi</th>
                        <th class="py-3 px-4">Petugas</th>
                        <th class="py-3 px-4">Kondisi</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4">Waktu</th>
                        <th class="py-3 px-4 text-center">Foto Bukti</th>
                        <th class="py-3 px-4">Catatan</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#eae8e7] dark:divide-[#2e3030]">
                    @forelse($monitorings as $index => $item)
                        <tr class="hover:bg-[#fbf9f8] dark:hover:bg-[#262727]/60 transition-colors">
                            <td class="py-3.5 px-4 text-center font-medium text-[#5c3f3f] dark:text-[#8c8282]">
                                {{ $monitorings->firstItem() + $index }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-semibold text-[#1b1c1c] dark:text-white">{{ $item->area->name ?? '-' }}</div>
                                <div class="text-[11px] text-[#5c3f3f] dark:text-[#c8c6c5]">{{ $item->area->location ?? '-' }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-medium text-[#1b1c1c] dark:text-white">{{ $item->user->name ?? '-' }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <x-badge-condition :condition="$item->cleanliness_condition" />
                            </td>
                            <td class="py-3.5 px-4">
                                <x-badge-status :status="$item->status" />
                            </td>
                            <td class="py-3.5 px-4 text-[#5c3f3f] dark:text-[#c8c6c5]">
                                <div class="font-semibold text-[#1b1c1c] dark:text-white flex items-center gap-1">
                                    <i class="fa-regular fa-clock text-[10px] text-[#5c3f3f] dark:text-[#8c8282]"></i>
                                    <span>{{ $item->formatted_start_time ?: substr($item->check_time, 0, 5) }}</span>
                                    <span>-</span>
                                    <span>{{ $item->formatted_end_time ?: substr($item->check_time, 0, 5) }}</span>
                                </div>
                                <div class="text-[11px] text-[#5c3f3f] dark:text-[#c8c6c5] mt-0.5">
                                    {{ \Carbon\Carbon::parse($item->check_date)->format('d/m/Y') }}
                                    @if($item->duration_text)
                                        &bull; <span class="text-[#006622] dark:text-[#83fc8e] font-semibold">{{ $item->duration_text }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    @if($item->photo_before_url)
                                        <button type="button" 
                                                @click="$dispatch('open-image-modal', { src: '{{ $item->photo_before_url }}', title: 'Foto Sebelum - {{ $item->area->name ?? '' }}' })"
                                                class="w-7 h-7 rounded-lg overflow-hidden border border-[#eae8e7] dark:border-[#333535] hover:border-[#dc143c] transition-all shrink-0 cursor-pointer" title="Foto Sebelum">
                                            <img src="{{ $item->photo_before_url }}" alt="Sebelum" class="w-full h-full object-cover">
                                        </button>
                                    @endif

                                    @if($item->photo_after_url)
                                        <button type="button" 
                                                @click="$dispatch('open-image-modal', { src: '{{ $item->photo_after_url }}', title: 'Foto Sesudah - {{ $item->area->name ?? '' }}' })"
                                                class="w-7 h-7 rounded-lg overflow-hidden border border-[#b2e5be] dark:border-[#00531a] hover:border-[#00822d] transition-all shrink-0 cursor-pointer" title="Foto Sesudah">
                                            <img src="{{ $item->photo_after_url }}" alt="Sesudah" class="w-full h-full object-cover">
                                        </button>
                                    @endif

                                    @if(!$item->photo_before_url && !$item->photo_after_url)
                                        <span class="text-[#5c3f3f] dark:text-[#8c8282] text-xs">-</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-[#5c3f3f] dark:text-[#c8c6c5] max-w-xs truncate">
                                {{ $item->notes ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('monitoring.show', $item->id) }}" 
                                       class="p-1.5 rounded-lg text-[#5c3f3f] hover:text-[#dc143c] hover:bg-[#fff1f0] dark:text-[#c8c6c5] dark:hover:text-[#ffb3b3] dark:hover:bg-[#3b0b14] transition-colors" title="Lihat Detail">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('monitoring.edit', $item->id) }}" 
                                       class="p-1.5 rounded-lg text-[#5c3f3f] hover:text-[#b45309] hover:bg-[#fef3c7] dark:text-[#c8c6c5] dark:hover:text-[#fcd34d] dark:hover:bg-[#451a03] transition-colors" title="Edit">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </a>
                                    <form action="{{ route('monitoring.destroy', $item->id) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Hapus rekaman monitoring ini?')" 
                                          class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-[#5c3f3f] hover:text-[#ba1a1a] hover:bg-[#ffdad6] dark:text-[#c8c6c5] dark:hover:text-[#ffb4ab] dark:hover:bg-[#410002] transition-colors cursor-pointer" title="Hapus">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-[#5c3f3f] dark:text-[#c8c6c5]">
                                <i class="fa-regular fa-clipboard text-4xl mb-2 block opacity-40"></i>
                                Tidak ditemukan data monitoring yang sesuai dengan filter.
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
