<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
// Import MaterialResource jika Anda ingin menyertakan detail materi yang lebih lengkap
// dan sudah membuat MaterialResource. Jika tidak, kita bisa ambil field tertentu saja.
// use App\Http\Resources\MaterialResource;

class LearningHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // this->resource merujuk pada instance model LearningHistory
        return [
            'id' => $this->id,
            // 'user_id' => $this->user_id, // Mungkin tidak perlu ditampilkan jika endpoint sudah spesifik untuk user
            'material' => [ // Menyertakan detail materi
                'id' => $this->material->id,
                'title' => $this->material->title,
                // 'description' => $this->material->description, // Opsional
                // Anda bisa menggunakan MaterialResource jika sudah ada dan lebih kompleks:
                // 'material' => new MaterialResource($this->whenLoaded('material')),
            ],
            'completed_at' => $this->completed_at ? $this->completed_at->toIso8601String() : null,
            'recorded_at' => $this->created_at->toIso8601String(), // Kapan record ini dibuat
        ];
    }
}