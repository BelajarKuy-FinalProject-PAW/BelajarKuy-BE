<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // this->resource merujuk pada instance model User yang di-pass ke resource ini.
        return [
            'id' => $this->id,
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'no_telp' => $this->no_telp, // Akan null jika belum diisi
            'profile_photo_url' => $this->profile_photo_url, // Memanggil accessor getProfilePhotoUrlAttribute() dari model User
            'email_verified_at' => $this->email_verified_at ? $this->email_verified_at->toIso8601String() : null,
            // Anda bisa tambahkan field lain jika perlu, misalnya:
            // 'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}