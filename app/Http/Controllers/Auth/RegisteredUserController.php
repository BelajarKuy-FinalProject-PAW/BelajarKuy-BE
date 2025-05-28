<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest; 
use App\Models\User;
use App\Http\Resources\UserResource; 
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Response; 
use Illuminate\Support\Facades\Hash;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @param  \App\Http\Requests\Auth\RegisterRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RegisterRequest $request): Response
    {
        // Validasi sudah otomatis ditangani oleh RegisterRequest.
        // Jika validasi gagal, RegisterRequest akan otomatis mengembalikan respons error 422.

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        $tokenName = 'api-token-' . $user->username; 
        $token = $user->createToken($tokenName)->plainTextToken;


        return response([
            'message' => 'Registrasi berhasil.', // Pesan sukses opsional
            'user' => new UserResource($user),   // Menggunakan UserResource
            'token' => $token
        ], 201); 
    }
}