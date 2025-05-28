<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash; // Untuk mengecek current_password
use Illuminate\Validation\Rules\Password; // Untuk aturan password default Laravel

class UpdatePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // User harus terotentikasi (ditangani oleh middleware route)
    }

    public function rules(): array
    {
        return [
            'current_password' => [
                'required',
                'string',
                // Validasi kustom untuk memastikan password saat ini cocok
                function ($attribute, $value, $fail) {
                    if (!Hash::check($value, $this->user()->password)) {
                        // Anda bisa menggunakan pesan kustom di method messages() juga
                        $fail('Kata sandi Anda saat ini tidak cocok.');
                    }
                },
            ],
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
            // 'password_confirmation' field akan otomatis dicek oleh 'confirmed' rule pada 'password'
        ];
    }

    public function messages(): array // Pesan error kustom (opsional, tapi bagus)
    {
        return [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'password.required' => 'Kata sandi baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
            // Anda juga bisa menambahkan pesan untuk Password::defaults() jika perlu,
            // misalnya 'password.min' jika Anda tahu panjang minimalnya.
        ];
    }
}