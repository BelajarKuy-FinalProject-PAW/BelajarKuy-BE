<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSubMaterialRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $subMaterialId = $this->route('sub_material')->id;

        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            // 'unique_code' biasanya tidak diubah, tapi jika boleh, pastikan unik
            'unique_code' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('sub_materials')->ignore($subMaterialId)],
            'file_path' => ['nullable', 'string', 'max:2048'],
            'video_path' => ['nullable', 'string', 'max:2048'],
        ];
    }
}