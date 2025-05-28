<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest; // Pastikan ini adalah LoginRequest yang sudah kita kustomisasi
use App\Http\Resources\UserResource;     // Untuk memformat data user dalam respons
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;     // Ini tetap digunakan untuk Auth::attempt

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(LoginRequest $request): Response
    {
        // Method authenticate() dari LoginRequest akan menangani validasi
        // dan percobaan login (Auth::attempt). Jika gagal, akan throw ValidationException.
        $request->authenticate();

        // Jika authenticate() berhasil, kita bisa mendapatkan user yang terotentikasi
        $user = Auth::user(); // Atau $user = $request->user(); juga bisa setelah authenticate()

        // Membuat token API untuk user
        // Anda bisa memberi nama token yang lebih spesifik jika mau
        $tokenName = 'api-token-' . $user->username; // Menggunakan username agar lebih unik
        $token = $user->createToken($tokenName)->plainTextToken;

        // Mengembalikan data user (menggunakan UserResource) dan token
        return response([
            'message' => 'Login berhasil.', // Pesan sukses opsional
            'user' => new UserResource($user),
            'token' => $token
        ]); // Status default 200 OK
    }

    /**
     * Destroy an authenticated session (logout untuk API).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request): Response
    {
        // Untuk API, logout berarti menghapus token yang digunakan untuk request saat ini
        // Pastikan route ini dilindungi oleh middleware 'auth:sanctum'
        if ($request->user()) { // Pastikan ada user yang terotentikasi
            $request->user()->currentAccessToken()->delete();
        }

        return response()->noContent(); // HTTP status 204 No Content, menandakan sukses tanpa body
    }
}