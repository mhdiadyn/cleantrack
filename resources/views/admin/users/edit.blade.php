@extends('layouts.app')

@section('title', 'Edit Pengguna - ' . $user->name)
@section('header_title', 'Edit Pengguna')
@section('header_subtitle', 'Perbarui data akun dan hak akses pengguna')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="card-clean overflow-hidden">
        <div class="p-6 border-b border-[#eae8e7] dark:border-[#2e3030] flex items-center justify-between bg-[#f5f3f3] dark:bg-[#262727]">
            <div>
                <h3 class="font-heading text-base font-semibold text-[#1b1c1c] dark:text-white">Perbarui Akun Pengguna</h3>
                <p class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5] mt-0.5">{{ $user->name }} ({{ $user->email }})</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="btn-outline-neutral text-xs">
                <i class="fa-solid fa-arrow-left text-[10px]"></i>
                <span>Kembali</span>
            </a>
        </div>

        <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="p-6 space-y-5">
            @csrf
            @method('PUT')

            <!-- Nama Lengkap -->
            <div>
                <label for="name" class="label-clean">
                    Nama Lengkap <span class="text-[#ba1a1a]">*</span>
                </label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="{{ old('name', $user->name) }}" 
                       required 
                       placeholder="Contoh: Ahmad Faisal" 
                       class="input-clean @error('name') border-[#ba1a1a] @enderror">
                @error('name')
                    <p class="text-[11px] text-[#ba1a1a] dark:text-[#ffb4ab] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email & No HP -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="email" class="label-clean">
                        Alamat Email <span class="text-[#ba1a1a]">*</span>
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           value="{{ old('email', $user->email) }}" 
                           required 
                           placeholder="nama@cleantrack.test" 
                           class="input-clean @error('email') border-[#ba1a1a] @enderror">
                    @error('email')
                        <p class="text-[11px] text-[#ba1a1a] dark:text-[#ffb4ab] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="label-clean">
                        Nomor HP / WhatsApp (Opsional)
                    </label>
                    <input type="text" 
                           id="phone" 
                           name="phone" 
                           value="{{ old('phone', $user->phone) }}" 
                           placeholder="08123456789" 
                           class="input-clean @error('phone') border-[#ba1a1a] @enderror">
                    @error('phone')
                        <p class="text-[11px] text-[#ba1a1a] dark:text-[#ffb4ab] mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Password (Optional) -->
            <div>
                <label for="password" class="label-clean">
                    Password Baru (Kosongkan jika tidak ingin mengubah)
                </label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       placeholder="Minimal 6 karakter" 
                       class="input-clean @error('password') border-[#ba1a1a] @enderror">
                @error('password')
                    <p class="text-[11px] text-[#ba1a1a] dark:text-[#ffb4ab] mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Role & Status -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="role" class="label-clean">
                        Role Pengguna <span class="text-[#ba1a1a]">*</span>
                    </label>
                    <select id="role" name="role" required class="input-clean @error('role') border-[#ba1a1a] @enderror">
                        <option value="petugas" {{ old('role', $user->role) === 'petugas' ? 'selected' : '' }}>Petugas Kebersihan</option>
                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Administrator</option>
                    </select>
                    @error('role')
                        <p class="text-[11px] text-[#ba1a1a] dark:text-[#ffb4ab] mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="label-clean">
                        Status Akun <span class="text-[#ba1a1a]">*</span>
                    </label>
                    <select id="status" name="status" required class="input-clean @error('status') border-[#ba1a1a] @enderror">
                        <option value="aktif" {{ old('status', $user->status) === 'aktif' ? 'selected' : '' }}>Aktif (Dapat Login)</option>
                        <option value="nonaktif" {{ old('status', $user->status) === 'nonaktif' ? 'selected' : '' }}>Nonaktif (Diblokir)</option>
                    </select>
                    @error('status')
                        <p class="text-[11px] text-[#ba1a1a] dark:text-[#ffb4ab] mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-4 border-t border-[#eae8e7] dark:border-[#2e3030] flex items-center justify-end gap-3">
                <a href="{{ route('admin.users.index') }}" class="btn-outline-neutral">
                    Batal
                </a>
                <button type="submit" class="btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
