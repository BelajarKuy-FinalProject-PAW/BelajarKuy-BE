<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', // Sebelumnya title
        'category',
        'description',
    ];

    /**
     * Sebuah materi memiliki banyak sub-materi.
     */
    public function subMaterials()
    {
        return $this->hasMany(SubMaterial::class);
    }
    public function preferredByUsers(){
    return $this->belongsToMany(User::class, 'material_user');
    }
}