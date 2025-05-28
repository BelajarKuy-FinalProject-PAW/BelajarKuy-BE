<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File; // Untuk aturan validasi file modern

class UploadAvatarRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // User harus terotentikasi untuk mengunggah avatar,
        // ini sudah ditangani oleh middleware 'auth:sanctum' pada route.
        // Jadi, di sini kita bisa return true.
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
            'avatar' => [
                'required', // File avatar wajib ada
                File::image() // Memastikan file adalah gambar (jpg, png, gif, svg, webp, dll.)
                    ->max(2 * 1024), // Maksimal ukuran file 2MB (2 * 1024 KB)
                    // Anda bisa menambahkan batasan dimensi jika perlu:
                    // ->dimensions(Rule::dimensions()->minWidth(100)->minHeight(100)->maxWidth(2000)->maxHeight(2000)),
            ],
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
            'avatar.required' => 'Mohon pilih file gambar untuk diunggah.',
            'avatar.image' => 'File yang diunggah harus berupa format gambar yang valid (contoh: jpg, png).',
            'avatar.max' => 'Ukuran gambar tidak boleh melebihi 2MB.',
            // 'avatar.dimensions' => 'Dimensi gambar tidak sesuai ketentuan.', // Jika menggunakan rule dimensions
        ];
    }
}