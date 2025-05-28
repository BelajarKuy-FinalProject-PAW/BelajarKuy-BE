<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\User; // Import User model

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // User harus terotentikasi (ditangani oleh middleware route)
    }

    public function rules(): array
    {
        $userId = $this->user()->id; // Dapatkan ID user yang sedang login

        return [
            // Gunakan 'sometimes' agar field bersifat opsional saat update.
            // Artinya, jika field tidak dikirim, validasi untuk field itu tidak dijalankan.
            // 'required' setelah 'sometimes' berarti JIKA field itu dikirim, nilainya tidak boleh kosong.
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($userId)],
            'no_telp' => ['nullable', 'string', 'max:20'], // no_telp boleh dikirim kosong atau tidak dikirim sama sekali
            'username' => ['sometimes', 'required', 'string', 'max:255', Rule::unique(User::class)->ignore($userId)],
        ];
    }

    public function messages(): array // Pesan error kustom
    {
        return [
            'name.required' => 'Nama tidak boleh kosong jika diisi.',
            'email.required' => 'Email tidak boleh kosong jika diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan oleh pengguna lain.',
            'username.required' => 'Username tidak boleh kosong jika diisi.',
            'username.unique' => 'Username sudah digunakan oleh pengguna lain.',
        ];
    }
}