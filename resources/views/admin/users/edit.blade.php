@extends('layouts.app')

@section('title', 'Edit Pengguna - ' . $user->name)
@section('header_title', 'Edit Pengguna')
@section('header_subtitle', 'Perbarui data akun dan hak akses pengguna')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Perbarui Akun Pengguna</h3>
                <p class="text-xs text-slate-500 mt-0.5">{{ $user->name }} ({{ $user->email }})</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 flex items-center gap-1.5">
                <i class="fa-solid fa-arrow-left text-[10px]"></i>
                <span>Kembali</span>
            </a>
        </div>

        <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="p-6 space-y-5">
            @csrf
            @method('PUT')

            <!-- Nama Lengkap -->
            <div>
                <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nama Lengkap <span class="text-rose-500">*</span>
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="{{ old('name', $user->name) }}" 
                       required 
                       placeholder="Contoh: Ahmad Faisal" 
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 @error('name') border-rose-500 @enderror">
                @error('name')
                    <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email & No HP -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Alamat Email <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email', $user->email) }}" 
                           required 
                           placeholder="nama@cleantrack.test" 
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 @error('email') border-rose-500 @enderror">
                    @error('email')
                        <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Nomor HP / WhatsApp (Opsional)
                    </label>
                    <input type="text" 
                           id="phone" 
                           name="phone" 
                           value="{{ old('phone', $user->phone) }}" 
                           placeholder="08123456789" 
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 @error('phone') border-rose-500 @enderror">
                    @error('phone')
                        <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Password (Optional) -->
            <div>
                <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Password Baru (Kosongkan jika tidak ingin mengubah)
                </label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       placeholder="Minimal 6 karakter" 
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 @error('password') border-rose-500 @enderror">
                @error('password')
                    <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Role & Status -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="role" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Role Pengguna <span class="text-rose-500">*</span>
                    </label>
                    <select id="role" name="role" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 @error('role') border-rose-500 @enderror">
                        <option value="petugas" {{ old('role', $user->role) === 'petugas' ? 'selected' : '' }}>Petugas Kebersihan</option>
                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Administrator</option>
                    </select>
                    @error('role')
                        <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        Status Akun <span class="text-rose-500">*</span>
                    </label>
                    <select id="status" name="status" required class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 @error('status') border-rose-500 @enderror">
                        <option value="aktif" {{ old('status', $user->status) === 'aktif' ? 'selected' : '' }}>Aktif (Dapat Login)</option>
                        <option value="nonaktif" {{ old('status', $user->status) === 'nonaktif' ? 'selected' : '' }}>Nonaktif (Diblokir)</option>
                    </select>
                    @error('status')
                        <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('admin.users.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white font-bold text-xs shadow-md shadow-teal-500/20 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
