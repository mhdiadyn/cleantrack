@extends('layouts.app')

@section('title', 'Kelola Pengguna')
@section('header_title', 'Manajemen Data Pengguna')
@section('header_subtitle', 'Kelola akun Administrator dan Petugas Kebersihan sistem')

@section('content')
<div class="space-y-6">

    <!-- Action Bar & Filters -->
    <div class="card-clean p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <form method="GET" action="{{ route('admin.users.index') }}" class="flex-1 flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-[#5c3f3f] dark:text-[#8c8282] text-xs"></i>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Cari nama, email, no HP..." 
                       class="input-clean pl-9 text-xs">
            </div>

            <select name="role" onchange="this.form.submit()" class="input-clean text-xs sm:w-36">
                <option value="">Semua Role</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="petugas" {{ request('role') === 'petugas' ? 'selected' : '' }}>Petugas</option>
            </select>

            <select name="status" onchange="this.form.submit()" class="input-clean text-xs sm:w-36">
                <option value="">Semua Status</option>
                <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Aktif</option>
                <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
            </select>

            <button type="submit" class="btn-primary text-xs">
                <i class="fa-solid fa-filter text-xs"></i>
                <span>Filter</span>
            </button>

            @if(request()->hasAny(['search', 'role', 'status']))
                <a href="{{ route('admin.users.index') }}" class="btn-outline-neutral text-xs">
                    Reset
                </a>
            @endif
        </form>

        <div>
            <a href="{{ route('admin.users.create') }}" class="btn-primary">
                <i class="fa-solid fa-user-plus text-xs"></i>
                <span>Tambah Pengguna</span>
            </a>
        </div>
    </div>

    <!-- User Table Card -->
    <div class="card-clean overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-[#1b1c1c] dark:text-[#f2f0f0]">
                <thead class="bg-[#f5f3f3] dark:bg-[#262727] text-[#5c3f3f] dark:text-[#c8c6c5] font-semibold uppercase text-[11px] tracking-wider border-b border-[#eae8e7] dark:border-[#2e3030]">
                    <tr>
                        <th class="py-3 px-4 text-center w-12">No</th>
                        <th class="py-3 px-4">Nama Lengkap & Kontak</th>
                        <th class="py-3 px-4">Role</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-center">Total Input Laporan</th>
                        <th class="py-3 px-4">Terdaftar Pada</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#eae8e7] dark:divide-[#2e3030]">
                    @forelse($users as $index => $u)
                        <tr class="hover:bg-[#fbf9f8] dark:hover:bg-[#262727]/60 transition-colors">
                            <td class="py-3.5 px-4 text-center font-medium text-[#5c3f3f] dark:text-[#8c8282]">
                                {{ $users->firstItem() + $index }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg {{ $u->role === 'admin' ? 'bg-[#fff1f0] text-[#dc143c] dark:bg-[#3b0b14] dark:text-[#ffb3b3]' : 'bg-[#f5f3f3] text-[#5c3f3f] dark:bg-[#262727] dark:text-[#c8c6c5]' }} font-semibold text-xs flex items-center justify-center shrink-0 border border-[#eae8e7] dark:border-[#333535]">
                                        {{ strtoupper(substr($u->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-[#1b1c1c] dark:text-white flex items-center gap-2">
                                            <span>{{ $u->name }}</span>
                                            @if($u->id === Auth::id())
                                                <span class="px-1.5 py-0.2 rounded text-[10px] font-semibold bg-[#fff1f0] text-[#dc143c] border border-[#fecdd3] dark:bg-[#3b0b14] dark:text-[#ffb3b3] dark:border-[#831843]">Anda</span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] text-[#5c3f3f] dark:text-[#c8c6c5] flex items-center gap-2 mt-0.5">
                                            <span>{{ $u->email }}</span>
                                            @if($u->phone)
                                                <span>&bull; {{ $u->phone }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($u->role === 'admin')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#fff1f0] text-[#dc143c] border border-[#fecdd3] dark:bg-[#3b0b14] dark:text-[#ffb3b3] dark:border-[#831843]">
                                        <i class="fa-solid fa-shield-halved text-[10px]"></i>
                                        Admin
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium bg-[#f5f3f3] text-[#5c3f3f] border border-[#eae8e7] dark:bg-[#262727] dark:text-[#c8c6c5] dark:border-[#333535]">
                                        <i class="fa-solid fa-broom text-[10px]"></i>
                                        Petugas
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                @if($u->status === 'aktif')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#daffd5] text-[#006622] border border-[#b2e5be] dark:bg-[#003912] dark:text-[#83fc8e] dark:border-[#00531a]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#00822d]"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#ffdad6] text-[#ba1a1a] border border-[#ffb4ab] dark:bg-[#410002] dark:text-[#ffb4ab] dark:border-[#93000a]">
                                        <span class="w-1.5 h-1.5 rounded-full bg-[#ba1a1a]"></span>
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <span class="px-2.5 py-1 rounded-full bg-[#f5f3f3] dark:bg-[#262727] font-semibold text-[11px] text-[#1b1c1c] dark:text-[#f2f0f0] border border-[#eae8e7] dark:border-[#333535]">
                                    {{ $u->cleaning_monitorings_count }} data
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-[#5c3f3f] dark:text-[#c8c6c5]">
                                {{ $u->created_at->format('d/m/Y') }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('admin.users.edit', $u->id) }}" 
                                       class="p-1.5 rounded-lg text-[#5c3f3f] hover:text-[#b45309] hover:bg-[#fef3c7] dark:text-[#c8c6c5] dark:hover:text-[#fcd34d] dark:hover:bg-[#451a03] transition-colors" title="Edit Pengguna">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </a>

                                    @if($u->id !== Auth::id())
                                        <form action="{{ route('admin.users.destroy', $u->id) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $u->name }}?')" 
                                              class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg text-[#5c3f3f] hover:text-[#ba1a1a] hover:bg-[#ffdad6] dark:text-[#c8c6c5] dark:hover:text-[#ffb4ab] dark:hover:bg-[#410002] transition-colors cursor-pointer" title="Hapus Pengguna">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-[#5c3f3f] dark:text-[#c8c6c5]">
                                <i class="fa-solid fa-users text-3xl mb-2 block opacity-40"></i>
                                Tidak ada data pengguna yang sesuai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-[#eae8e7] dark:border-[#2e3030]">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
