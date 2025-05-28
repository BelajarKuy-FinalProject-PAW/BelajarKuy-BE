<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'material_id',
        'completed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'completed_at' => 'datetime',
    ];

    /**
     * Relasi belongsTo dengan User.
     * Riwayat belajar ini milik seorang User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi belongsTo dengan Material.
     * Riwayat belajar ini untuk sebuah Material.
     */
    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}