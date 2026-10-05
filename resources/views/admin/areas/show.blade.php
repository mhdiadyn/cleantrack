@extends('layouts.app')

@section('title', 'Detail Area - ' . $area->name)
@section('header_title', 'Detail Area Kebersihan')
@section('header_subtitle', 'Informasi lengkap dan histori pemeriksaan pada area')

@section('content')
<div class="space-y-6">

    <!-- Area Information Card -->
    <div class="bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-100">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-2xl bg-teal-50 text-teal-600 border border-teal-200 flex items-center justify-center text-xl shrink-0">
                    <i class="fa-solid fa-map-location-dot"></i>
                </div>
                <div>
                    <div class="flex items-center gap-3">
                        <h2 class="text-xl font-black text-slate-900">{{ $area->name }}</h2>
                        @if($area->is_active)
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-500 border border-slate-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                Nonaktif
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 mt-1 flex items-center gap-1.5">
                        <i class="fa-solid fa-location-dot text-teal-600"></i>
                        <span>{{ $area->location }}</span>
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('monitoring.create', ['area_id' => $area->id]) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-xs transition-colors">
                    <i class="fa-solid fa-clipboard-check"></i>
                    <span>Catat Pemeriksaan</span>
                </a>
                <a href="{{ route('admin.areas.edit', $area->id) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors">
                    <i class="fa-solid fa-pen-to-square"></i>
                    <span>Edit</span>
                </a>
                <a href="{{ route('admin.areas.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 font-semibold text-xs transition-colors">
                    <span>Kembali</span>
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-6">
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Keterangan / Fasilitas</span>
                <p class="text-xs text-slate-700 mt-1.5 leading-relaxed bg-slate-50 p-3 rounded-xl border border-slate-100">
                    {{ $area->description ?: 'Tidak ada catatan atau deskripsi tambahan untuk area ini.' }}
                </p>
            </div>

            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Total Histori Pemeriksaan</span>
                <p class="text-2xl font-black text-slate-800 mt-1">
                    {{ $area->cleaning_monitorings_count }} <span class="text-xs font-normal text-slate-500">kali diperiksa</span>
                </p>
            </div>

            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Terdaftar Sejak</span>
                <p class="text-sm font-semibold text-slate-700 mt-1">
                    {{ $area->created_at->translatedFormat('d F Y (H:i)') }}
                </p>
            </div>
        </div>
    </div>

    <!-- Riwayat Pemeriksaan di Area Ini -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-5 sm:px-6 border-b border-slate-100">
            <h3 class="text-sm font-bold text-slate-800">Riwayat Pemeriksaan Pada Area Ini</h3>
            <p class="text-xs text-slate-500">Daftar rekaman kebersihan khusus untuk {{ $area->name }}</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-700">
                <thead class="bg-slate-50 text-slate-500 font-bold uppercase text-[10px] tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-4 text-center w-12">No</th>
                        <th class="py-3.5 px-4">Petugas</th>
                        <th class="py-3.5 px-4">Kondisi</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4">Tanggal & Jam</th>
                        <th class="py-3.5 px-4">Catatan</th>
                        <th class="py-3.5 px-4 text-center">Foto</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($monitorings as $index => $item)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-4 text-center font-medium text-slate-400">{{ $index + 1 }}</td>
                            <td class="py-3.5 px-4 font-bold text-slate-800">{{ $item->user->name ?? '-' }}</td>
                            <td class="py-3.5 px-4">
                                <x-badge-condition :condition="$item->cleanliness_condition" />
                            </td>
                            <td class="py-3.5 px-4">
                                <x-badge-status :status="$item->status" />
                            </td>
                            <td class="py-3.5 px-4 font-medium text-slate-600">
                                {{ \Carbon\Carbon::parse($item->check_date)->format('d/m/Y') }}
                                <span class="text-[11px] text-slate-400 block">{{ substr($item->check_time, 0, 5) }} WIB</span>
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 max-w-xs truncate">
                                {{ $item->notes ?? '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($item->photo_before || $item->photo_after)
                                    <span class="inline-flex items-center gap-1 text-teal-600 font-bold text-[11px]">
                                        <i class="fa-solid fa-image"></i>
                                        <span>Tersedia</span>
                                    </span>
                                @else
                                    <span class="text-slate-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('monitoring.show', $item->id) }}" 
                                   class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-teal-50 hover:bg-teal-100 text-teal-700 font-bold text-[11px] transition-colors">
                                    <i class="fa-solid fa-eye"></i>
                                    <span>Detail</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400">
                                Belum ada riwayat pemeriksaan pada area ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($monitorings->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $monitorings->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
