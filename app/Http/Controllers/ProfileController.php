<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman profil
     */
    public function index()
    {
        return view('profile.index');
    }

    /**
     * Update profil pengguna
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
        ], [
            'name.required' => 'Nama tidak boleh kosong',
            'email.required' => 'Email tidak boleh kosong',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
        ]);

        $user->update($request->only('name', 'email'));

        return back()->with('profile_success', 'Profil berhasil diperbarui');
    }

    /**
     * Update password pengguna
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'current_password.current_password' => 'Password saat ini tidak sesuai',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok',
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password)
        ]);

        return back()->with('success', 'Password berhasil diperbarui');
    }

    /**
     * Upload foto profil
     */
    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'photo' => ['required', 'string'],
        ], [
            'photo.required' => 'Foto belum di-crop',
        ]);

        $user = auth()->user();

        // Hapus foto lama jika ada
        if ($user->photo && !str_starts_with($user->photo, 'avatars/')) {
            Storage::disk('public')->delete($user->photo);
        }

        // Decode base64 image
        $imageData = $request->photo;

        // Remove data:image/...;base64, prefix
        if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
            $imageData = substr($imageData, strpos($imageData, ',') + 1);
            $type = strtolower($type[1]); // jpg, png, gif
        } else {
            return back()->with('profile_error', 'Format foto tidak valid');
        }

        // Decode base64
        $imageData = base64_decode($imageData);

        if ($imageData === false) {
            return back()->with('profile_error', 'Gagal decode foto');
        }

        // Generate unique filename
        $filename = 'profile-' . $user->id . '-' . time() . '.' . $type;
        $path = 'profile-photos/' . $filename;

        // Save to storage
        Storage::disk('public')->put($path, $imageData);

        $user->update(['photo' => $path]);

        return back()->with('profile_success', 'Foto profil berhasil diperbarui');
    }

    /**
     * Pilih avatar default
     */
    public function selectAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'string'],
        ]);

        $user = auth()->user();

        // Hapus foto lama jika bukan avatar
        if ($user->photo && !str_starts_with($user->photo, 'avatars/')) {
            Storage::disk('public')->delete($user->photo);
        }

        $user->update(['photo' => $request->avatar]);

        return back()->with('profile_success', 'Avatar berhasil dipilih');
    }

    /**
     * Hapus foto profil (kembali ke default)
     */
    public function deletePhoto()
    {
        $user = auth()->user();

        // Hapus foto jika bukan avatar
        if ($user->photo && !str_starts_with($user->photo, 'avatars/')) {
            Storage::disk('public')->delete($user->photo);
        }

        $user->update(['photo' => null]);

        return back()->with('profile_success', 'Foto profil berhasil dihapus');
    }
}