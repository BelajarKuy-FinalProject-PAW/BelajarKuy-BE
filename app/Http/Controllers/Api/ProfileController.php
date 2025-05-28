<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Http\Requests\Profile\UpdatePasswordRequest;
use App\Http\Requests\Profile\UploadAvatarRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;


class ProfileController extends Controller
{
    /**
     * Display the authenticated user's profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \App\Http\Resources\UserResource
     */
    public function show(Request $request): UserResource
    {
        return new UserResource($request->user());
    }
    public function update(UpdateProfileRequest $request) // Gunakan UpdateProfileRequest
    {
        $user = $request->user();
        $validatedData = $request->validated();
        $user->update($validatedData);

        return response()->json([

            'message' => 'Profil berhasil diperbarui.',
            'user' => new UserResource($user->fresh()) // Kembalikan data user terbaru setelah update
        ]);
    }
    public function updatePassword(UpdatePasswordRequest $request) // Gunakan UpdatePasswordRequest
    {
        $request->user()->update([
            'password' => Hash::make($request->input('password')),
        ]);
        return response()->json(['message' => 'Kata sandi berhasil diubah.']);
    }
     public function uploadAvatar(UploadAvatarRequest $request)
    {
        $user = $request->user(); // Dapatkan user yang terotentikasi
        $file = $request->file('avatar'); // Dapatkan file yang diunggah (sudah divalidasi oleh UploadAvatarRequest)

        // 1. Hapus foto profil lama jika ada (dan jika bukan URL default)
        if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        // 2. Simpan file baru
        // File akan disimpan di 'storage/app/public/avatars'
        // Nama file akan di-generate secara acak oleh Laravel untuk menghindari duplikasi.
        // Method store() mengembalikan path relatif dari file yang disimpan (mis: 'avatars/randomname.jpg')
        $path = $file->store('avatars', 'public');

        // 3. Update path foto profil di database untuk user
        $user->update([
            'profile_photo_path' => $path
        ]);

        // 4. Kembalikan respons sukses dengan URL foto baru
        return response()->json([
            'message' => 'Foto profil berhasil diunggah.',
            'profile_photo_url' => $user->profile_photo_url, // Memanggil accessor untuk URL lengkap
        ]);
    }
    public function destroyAccount(Request $request)
    {
        $user = $request->user();

        // 1. Validasi password saat ini untuk konfirmasi
        $request->validateWithBag('userDeletion', [ // Menggunakan 'userDeletion' error bag (opsional, tapi baik)
            'password' => ['required', 'string'],
        ]);

        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['Kata sandi yang Anda masukkan salah.'],
            ])->errorBag('userDeletion'); // Mengirim error ke bag 'userDeletion'
        }

        // 2. Hapus semua token API milik user ini
        // Ini akan membuat semua sesi aktif pengguna menjadi tidak valid.
        $user->tokens()->delete();

        // 3. Hapus file foto profil dari storage (jika ada)
        if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        // 4. Hapus user dari database
        // Jika model User Anda menggunakan trait SoftDeletes, ini akan melakukan soft delete.
        // Jika tidak, ini akan melakukan hard delete (hapus permanen).
        // Pertimbangkan konsekuensi data terkait lainnya (misalnya postingan, komentar).
        // Untuk hard delete meskipun ada SoftDeletes, gunakan $user->forceDelete();
        $user->delete(); // atau $user->forceDelete();

        // 5. (Opsional) Logout user dari guard 'web' jika Anda juga punya sesi web
        // Auth::guard('web')->logout(); // Jarang diperlukan jika ini murni API

        return response()->json(['message' => 'Akun Anda telah berhasil dihapus.']);
    }
}
