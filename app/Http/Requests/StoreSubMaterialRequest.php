<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSubMaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Asumsi hanya user terautentikasi yang bisa membuat sub-materi
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'unique_code' => ['required', 'string', 'max:255', 'unique:sub_materials,unique_code'],
            'file_path' => ['nullable', 'string', 'max:2048'], // Validasi untuk file upload akan lebih kompleks
            'video_path' => ['nullable', 'string', 'max:2048'], // Untuk sekarang, kita validasi sebagai string path
        ];
    }
}