<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show user profile edit form.
     */
    public function edit(): View
    {
        $user = Auth::user();

        return view('profile.edit', compact('user'));
    }

    /**
     * Update user profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'profile_photo' => [
                'nullable',
                Rule::when($request->hasFile('profile_photo'), ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048']),
            ],
            'current_password' => ['nullable', 'required_with:new_password', 'string'],
            'new_password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
            'current_password.required_with' => 'Password saat ini wajib diisi jika ingin mengganti password.',
            'new_password.min' => 'Password baru minimal 6 karakter.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'profile_photo.image' => 'Foto profil harus berupa gambar.',
            'profile_photo.mimes' => 'Foto profil harus berformat JPG, JPEG, PNG, atau WebP.',
            'profile_photo.max' => 'Ukuran foto profil maksimal 2 MB.',
        ]);

        if (! empty($validated['new_password'])) {
            if (! Hash::check($validated['current_password'], $user->password)) {
                return back()->withErrors(['current_password' => 'Password saat ini yang Anda masukkan salah.']);
            }

            $user->password = Hash::make($validated['new_password']);
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? null;

        if ($request->hasFile('profile_photo')) {
            $profilePhoto = $validated['profile_photo'] ?? null;

            $temporaryPath = $profilePhoto instanceof UploadedFile
                ? $profilePhoto->getPathname()
                : '';

            if (
                ! $profilePhoto instanceof UploadedFile
                || ! $profilePhoto->isValid()
                || $temporaryPath === ''
                || ! is_file($temporaryPath)
                || ! is_readable($temporaryPath)
            ) {
                return back()
                    ->withErrors(['profile_photo' => 'Foto profil gagal diunggah atau file sementara tidak dapat dibaca. Silakan pilih ulang file foto.'])
                    ->withInput();
            }

            $stream = fopen($temporaryPath, 'rb');

            if (! is_resource($stream)) {
                return back()
                    ->withErrors(['profile_photo' => 'Foto profil tidak dapat dibaca. Silakan pilih ulang file foto.'])
                    ->withInput();
            }

            $path = 'profile-photos/'.$profilePhoto->hashName();

            try {
                $stored = Storage::disk('public')->put($path, $stream);
            } finally {
                fclose($stream);
            }

            if (! $stored) {
                return back()
                    ->withErrors(['profile_photo' => 'Foto profil gagal disimpan. Silakan coba lagi.'])
                    ->withInput();
            }

            $previousPath = $user->profile_photo_path;
            $user->profile_photo_path = $path;
        }

        $user->save();

        if (isset($previousPath) && $previousPath !== $user->profile_photo_path && Storage::disk('public')->exists($previousPath)) {
            if (! Storage::disk('public')->delete($previousPath)) {
                return back()->with('error', 'Profil berhasil diperbarui, tetapi foto profil sebelumnya gagal dihapus.');
            }
        }

        return back()->with('success', 'Profil Anda berhasil diperbarui!');
    }
}
