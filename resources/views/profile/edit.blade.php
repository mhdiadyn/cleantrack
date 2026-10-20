@extends('layouts.app')

@section('title', 'Profil Pengguna')
@section('header_title', 'Pengaturan Profil')
@section('header_subtitle', 'Perbarui informasi identitas akun dan kata sandi Anda')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Left: User Identity Card -->
        <div class="card-clean p-6 flex flex-col items-center text-center">
            <div class="w-18 h-18 rounded-2xl bg-[#dc143c] text-white font-bold text-2xl flex items-center justify-center shadow-xs mb-4">
                {{ strtoupper(substr($user->name, 0, 2)) }}
            </div>
            <h3 class="font-heading text-base font-semibold text-[#1b1c1c] dark:text-white">{{ $user->name }}</h3>
            <p class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5] mt-0.5">{{ $user->email }}</p>

            <div class="mt-4 pt-4 border-t border-[#eae8e7] dark:border-[#2e3030] w-full space-y-2 text-xs">
                <div class="flex items-center justify-between py-1">
                    <span class="text-[#5c3f3f] dark:text-[#8c8282]">Role Sistem</span>
                    <span class="font-semibold text-[#dc143c] dark:text-[#ffb3b3] uppercase">{{ $user->role }}</span>
                </div>
                <div class="flex items-center justify-between py-1">
                    <span class="text-[#5c3f3f] dark:text-[#8c8282]">Status Akun</span>
                    <span class="font-semibold text-[#006622] dark:text-[#83fc8e] uppercase">{{ $user->status }}</span>
                </div>
                <div class="flex items-center justify-between py-1">
                    <span class="text-[#5c3f3f] dark:text-[#8c8282]">No. Telepon</span>
                    <span class="font-medium text-[#1b1c1c] dark:text-white">{{ $user->phone ?: '-' }}</span>
                </div>
                <div class="flex items-center justify-between py-1">
                    <span class="text-[#5c3f3f] dark:text-[#8c8282]">Terdaftar</span>
                    <span class="font-medium text-[#1b1c1c] dark:text-white">{{ $user->created_at->format('d/m/Y') }}</span>
                </div>
            </div>
        </div>

        <!-- Right: Edit Profile & Password Form -->
        <div class="md:col-span-2 card-clean overflow-hidden">
            <div class="p-6 border-b border-[#eae8e7] dark:border-[#2e3030] bg-[#f5f3f3] dark:bg-[#262727]">
                <h3 class="font-heading text-sm font-semibold text-[#1b1c1c] dark:text-white">Perbarui Informasi Akun</h3>
                <p class="text-xs text-[#5c3f3f] dark:text-[#c8c6c5] mt-0.5">Ubah nama, email, nomor kontak atau ganti kata sandi</p>
            </div>

            <form action="{{ route('profile.update') }}" method="POST" class="p-6 space-y-5">
                @csrf
                @method('PATCH')

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
                               class="input-clean @error('email') border-[#ba1a1a] @enderror">
                        @error('email')
                            <p class="text-[11px] text-[#ba1a1a] dark:text-[#ffb4ab] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="label-clean">
                            Nomor HP / WhatsApp
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

                <!-- Ganti Password Section -->
                <div class="pt-4 border-t border-[#eae8e7] dark:border-[#2e3030] space-y-4">
                    <h4 class="font-heading text-xs font-semibold text-[#1b1c1c] dark:text-white uppercase tracking-wider">Ganti Kata Sandi (Opsional)</h4>

                    <div>
                        <label for="current_password" class="label-clean">
                            Password Saat Ini
                        </label>
                        <input type="password" 
                               id="current_password" 
                               name="current_password" 
                               placeholder="Masukkan password saat ini untuk konfirmasi" 
                               class="input-clean @error('current_password') border-[#ba1a1a] @enderror">
                        @error('current_password')
                            <p class="text-[11px] text-[#ba1a1a] dark:text-[#ffb4ab] mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="new_password" class="label-clean">
                                Password Baru
                            </label>
                            <input type="password" 
                                   id="new_password" 
                                   name="new_password" 
                                   placeholder="Minimal 6 karakter" 
                                   class="input-clean @error('new_password') border-[#ba1a1a] @enderror">
                            @error('new_password')
                                <p class="text-[11px] text-[#ba1a1a] dark:text-[#ffb4ab] mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="new_password_confirmation" class="label-clean">
                                Konfirmasi Password Baru
                            </label>
                            <input type="password" 
                                   id="new_password_confirmation" 
                                   name="new_password_confirmation" 
                                   placeholder="Ulangi password baru" 
                                   class="input-clean">
                        </div>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="pt-4 border-t border-[#eae8e7] dark:border-[#2e3030] flex items-center justify-end">
                    <button type="submit" class="btn-primary">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Simpan Perubahan Profil</span>
                    </button>
                </div>
            </form>
        </div>

    </div>

</div>
@endsection
