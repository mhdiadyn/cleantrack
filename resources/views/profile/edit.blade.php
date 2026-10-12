@extends('layouts.app')

@section('title', 'Profil Pengguna')
@section('header_title', 'Pengaturan Profil')
@section('header_subtitle', 'Perbarui informasi identitas akun dan kata sandi Anda')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Left: User Identity Card -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 border border-slate-200/80 dark:border-slate-800 shadow-xs flex flex-col items-center text-center">
            <div class="w-20 h-20 rounded-2xl bg-gradient-to-tr from-red-600 to-rose-500 text-white font-black text-2xl flex items-center justify-center shadow-lg shadow-red-500/20 mb-4">
                {{ strtoupper(substr($user->name, 0, 2)) }}
            </div>
            <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">{{ $user->name }}</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $user->email }}</p>

            <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800 w-full space-y-2 text-xs">
                <div class="flex items-center justify-between py-1">
                    <span class="text-slate-400 dark:text-slate-500">Role Sistem</span>
                    <span class="font-bold text-red-600 dark:text-red-400 uppercase">{{ $user->role }}</span>
                </div>
                <div class="flex items-center justify-between py-1">
                    <span class="text-slate-400 dark:text-slate-500">Status Akun</span>
                    <span class="font-bold text-emerald-600 dark:text-emerald-400 uppercase">{{ $user->status }}</span>
                </div>
                <div class="flex items-center justify-between py-1">
                    <span class="text-slate-400 dark:text-slate-500">No. Telepon</span>
                    <span class="font-medium text-slate-700 dark:text-slate-300">{{ $user->phone ?: '-' }}</span>
                </div>
                <div class="flex items-center justify-between py-1">
                    <span class="text-slate-400 dark:text-slate-500">Terdaftar</span>
                    <span class="font-medium text-slate-700 dark:text-slate-300">{{ $user->created_at->format('d/m/Y') }}</span>
                </div>
            </div>
        </div>

        <!-- Right: Edit Profile & Password Form -->
        <div class="md:col-span-2 bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-slate-800 shadow-xs overflow-hidden">
            <div class="p-6 border-b border-slate-100 dark:border-slate-800 bg-gradient-to-r from-red-500/10 via-slate-50 dark:via-slate-800/40 to-transparent">
                <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">Perbarui Informasi Akun</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Ubah nama, email, nomor kontak atau ganti kata sandi</p>
            </div>

            <form action="{{ route('profile.update') }}" method="POST" class="p-6 space-y-5">
                @csrf
                @method('PATCH')

                <!-- Nama Lengkap -->
                <div>
                    <label for="name" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        Nama Lengkap <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           value="{{ old('name', $user->name) }}" 
                           required 
                           class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500 @error('name') border-rose-500 @enderror">
                    @error('name')
                        <p class="text-[11px] text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email & No HP -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            Alamat Email <span class="text-rose-500">*</span>
                        </label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email', $user->email) }}" 
                               required 
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500 @error('email') border-rose-500 @enderror">
                        @error('email')
                            <p class="text-[11px] text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            Nomor HP / WhatsApp
                        </label>
                        <input type="text" 
                               id="phone" 
                               name="phone" 
                               value="{{ old('phone', $user->phone) }}" 
                               placeholder="08123456789" 
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500 @error('phone') border-rose-500 @enderror">
                        @error('phone')
                            <p class="text-[11px] text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Ganti Password Section -->
                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 space-y-4">
                    <h4 class="text-xs font-bold text-slate-800 dark:text-slate-200 uppercase tracking-wider">Ganti Kata Sandi (Opsional)</h4>

                    <div>
                        <label for="current_password" class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">
                            Password Saat Ini
                        </label>
                        <input type="password" 
                               id="current_password" 
                               name="current_password" 
                               placeholder="Masukkan password saat ini untuk konfirmasi" 
                               class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500 @error('current_password') border-rose-500 @enderror">
                        @error('current_password')
                            <p class="text-[11px] text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="new_password" class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">
                                Password Baru
                            </label>
                            <input type="password" 
                                   id="new_password" 
                                   name="new_password" 
                                   placeholder="Minimal 6 karakter" 
                                   class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500 @error('new_password') border-rose-500 @enderror">
                            @error('new_password')
                                <p class="text-[11px] text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="new_password_confirmation" class="block text-xs font-medium text-slate-600 dark:text-slate-400 mb-1">
                                Konfirmasi Password Baru
                            </label>
                            <input type="password" 
                                   id="new_password_confirmation" 
                                   name="new_password_confirmation" 
                                   placeholder="Ulangi password baru" 
                                   class="w-full px-4 py-2.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-900 dark:text-slate-100 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-red-500">
                        </div>
                    </div>
                </div>

                <!-- Action Button -->
                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-end">
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs shadow-md shadow-red-500/20 transition-all flex items-center gap-2 cursor-pointer">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Simpan Perubahan Profil</span>
                    </button>
                </div>
            </form>
        </div>

    </div>

</div>
@endsection
