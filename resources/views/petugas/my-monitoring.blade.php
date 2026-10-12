@extends('layouts.app')

@section('title', 'Riwayat Pemeriksaan Saya')
@section('header_title', 'Riwayat Pemeriksaan Saya')
@section('header_subtitle', 'Daftar riwayat monitoring kebersihan yang telah Anda laporkan')

@section('content')
<div class="space-y-6">

    <!-- Filters & Action Bar -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl p-5 border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <form method="GET" action="{{ route('petugas.my-monitoring') }}" class="flex-1 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <!-- Search -->
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-slate-500 text-xs"></i>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Cari area, catatan..." 
                       class="w-full pl-9 pr-4 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-red-500">
            </div>

            <!-- Area Filter -->
            <div>
                <select name="area_id" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-red-500">
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
                <select name="status" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl text-xs focus:outline-none focus:ring-2 focus:ring-red-500">
                    <option value="">Semua Status</option>
                    @foreach(['Belum Diperiksa', 'Perlu Dibersihkan', 'Sedang Dibersihkan', 'Sudah Dibersihkan'] as $st)
                        <option value="{{ $st }}" {{ request('status') === $st ? 'selected' : '' }}>{{ $st }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex items-center gap-2">
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-bold transition-colors cursor-pointer shadow-xs">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'area_id', 'status', 'date']))
                    <a href="{{ route('petugas.my-monitoring') }}" class="px-3 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl text-xs font-semibold transition-colors">
                        Reset
                    </a>
                @endif
            </div>
        </form>

        <div>
            <a href="{{ route('monitoring.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs shadow-md shadow-red-500/20 transition-all">
                <i class="fa-solid fa-circle-plus"></i>
                <span>Pemeriksaan Baru</span>
            </a>
        </div>
    </div>

    <!-- My Monitoring Table -->
    <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700 dark:text-slate-300">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 font-bold uppercase text-[10px] tracking-wider border-b border-slate-100 dark:border-slate-800">
                    <tr>
                        <th class="py-3.5 px-4 text-center w-12">No</th>
                        <th class="py-3.5 px-4">Nama Area</th>
                        <th class="py-3.5 px-4">Kondisi</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4">Tanggal & Jam</th>
                        <th class="py-3.5 px-4 text-center">Foto Bukti</th>
                        <th class="py-3.5 px-4">Catatan</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($monitorings as $index => $item)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="py-3.5 px-4 text-center font-medium text-slate-400 dark:text-slate-500">
                                {{ $monitorings->firstItem() + $index }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900 dark:text-slate-100">{{ $item->area->name ?? '-' }}</div>
                                <div class="text-[11px] text-slate-400 dark:text-slate-500">{{ $item->area->location ?? '-' }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <x-badge-condition :condition="$item->cleanliness_condition" />
                            </td>
                            <td class="py-3.5 px-4">
                                <x-badge-status :status="$item->status" />
                            </td>
                            <td class="py-3.5 px-4 font-medium text-slate-600 dark:text-slate-400">
                                <div class="font-bold text-slate-800 dark:text-slate-200 flex items-center gap-1">
                                    <i class="fa-regular fa-clock text-[10px] text-slate-400"></i>
                                    <span>{{ $item->formatted_start_time ?: substr($item->check_time, 0, 5) }}</span>
                                    <span>-</span>
                                    <span>{{ $item->formatted_end_time ?: substr($item->check_time, 0, 5) }}</span>
                                </div>
                                <div class="text-[11px] text-slate-400 dark:text-slate-500">
                                    {{ \Carbon\Carbon::parse($item->check_date)->format('d/m/Y') }}
                                    @if($item->duration_text)
                                        • <span class="text-emerald-600 dark:text-emerald-400 font-semibold">{{ $item->duration_text }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    @if($item->photo_before_url)
                                        <button type="button" 
                                                @click="$dispatch('open-image-modal', { src: '{{ $item->photo_before_url }}', title: 'Foto Sebelum - {{ $item->area->name ?? '' }}' })"
                                                class="w-7 h-7 rounded-lg overflow-hidden border border-rose-200 dark:border-rose-800 hover:ring-2 hover:ring-rose-400 transition-all shrink-0" title="Foto Sebelum">
                                            <img src="{{ $item->photo_before_url }}" alt="Sebelum" class="w-full h-full object-cover">
                                        </button>
                                    @endif

                                    @if($item->photo_after_url)
                                        <button type="button" 
                                                @click="$dispatch('open-image-modal', { src: '{{ $item->photo_after_url }}', title: 'Foto Sesudah - {{ $item->area->name ?? '' }}' })"
                                                class="w-7 h-7 rounded-lg overflow-hidden border border-emerald-200 dark:border-emerald-800 hover:ring-2 hover:ring-emerald-400 transition-all shrink-0" title="Foto Sesudah">
                                            <img src="{{ $item->photo_after_url }}" alt="Sesudah" class="w-full h-full object-cover">
                                        </button>
                                    @endif

                                    @if(!$item->photo_before_url && !$item->photo_after_url)
                                        <span class="text-slate-300 dark:text-slate-600 text-xs">-</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 dark:text-slate-400 max-w-xs truncate">
                                {{ $item->notes ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('monitoring.show', $item->id) }}" 
                                       class="p-1.5 rounded-lg bg-red-50 dark:bg-red-950/60 hover:bg-red-100 dark:hover:bg-red-900/60 text-red-600 dark:text-red-400 transition-colors" title="Lihat Detail">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('monitoring.edit', $item->id) }}" 
                                       class="p-1.5 rounded-lg bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 transition-colors" title="Edit Data">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400 dark:text-slate-500">
                                <i class="fa-solid fa-clipboard-question text-4xl mb-2 block"></i>
                                Anda belum memiliki rekaman data pemeriksaan yang cocok.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($monitorings->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $monitorings->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
