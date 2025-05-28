<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'topic_id', 
    ];

    public function learningHistories()
    {
        return $this->hasMany(LearningHistory::class);
    }

    /**
     * Relasi belongsTo dengan Topic.
     * Materi ini milik sebuah Topic.
     */
    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }
}