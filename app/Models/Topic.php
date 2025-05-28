<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str; // Untuk helper Str::slug()

class Topic extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    /**
     * Boot the model.
     * Otomatis membuat slug dari nama topik saat data dibuat atau nama diubah,
     * jika slug tidak diisi secara manual.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($topic) {
            if (empty($topic->slug)) {
                $topic->slug = Str::slug($topic->name);
            }
        });

        static::updating(function ($topic) {
            // Hanya update slug jika nama berubah dan slug tidak diisi manual,
            // atau jika slug memang dikosongkan untuk di-generate ulang.
            if ($topic->isDirty('name') && (empty($topic->slug) || $topic->getOriginal('slug') === Str::slug($topic->getOriginal('name')))) {
                $topic->slug = Str::slug($topic->name);
            }
        });
    }

    /**
     * Relasi many-to-many dengan User.
     * Mendapatkan semua pengguna yang memiliki preferensi topik ini.
     */
    public function users(){
        // Argumen kedua adalah nama tabel pivot (konvensi: urutan alfabet nama model, singular)
        // Argumen ketiga dan keempat (opsional) adalah foreign key di tabel pivot
        return $this->belongsToMany(User::class, 'topic_user');
    }

    public function materials(){
        return $this->hasMany(Material::class);
    }
}