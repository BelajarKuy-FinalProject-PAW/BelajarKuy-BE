<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubMaterialResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
{
    return [
        'id_sub_materi' => $this->id,
        'kode_unik' => $this->unique_code,
        'judul' => $this->title,
        'file_pdf' => $this->file_path ? Storage::url($this->file_path) : null, // Contoh jika disimpan di storage
        'file_video' => $this->video_path ? Storage::url($this->video_path) : null, // Contoh jika disimpan di storage
    ];
}
}
