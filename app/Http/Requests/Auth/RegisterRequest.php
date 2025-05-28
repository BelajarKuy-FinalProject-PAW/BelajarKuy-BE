<?php

namespace App\Http\Requests\Auth;

use App\Models\User; 
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules; 

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Set ke true karena siapa saja boleh mencoba untuk mendaftar.
        // Autentikasi untuk akses route akan ditangani oleh middleware di file route.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:'.User::class], // Pastikan username unik di tabel users
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class], // Pastikan email unik
            'password' => ['required', 'confirmed', Rules\Password::defaults()], // 'confirmed' akan mencocokkan dengan field 'password_confirmation'
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan, silakan pilih username lain.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ];
    }
}