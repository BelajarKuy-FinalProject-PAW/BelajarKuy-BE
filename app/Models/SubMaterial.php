<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'material_id',
        'unique_code',
        'title',
        'file_path',
        'video_path',
    ];

    /**
     * Sebuah sub-materi dimiliki oleh satu materi.
     */
    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}