<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Storage; // Import Storage
use App\Models\LearningHistory;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'no_telp',
        'password',
        'profile_photo_path', 
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Accessor untuk URL foto profil
    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo_path && Storage::disk('public')->exists($this->profile_photo_path)) {
            return Storage::url($this->profile_photo_path);
        }
        // Fallback ke UI Avatars jika tidak ada foto
        return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&color=7F9CF5&background=EBF4FF&format=svg';
    }
    
    public function topics(){
    return $this->belongsToMany(Topic::class, 'topic_user');
    }

    public function learningHistories(){
    return $this->hasMany(LearningHistory::class)->orderBy('completed_at', 'desc'); // Urutkan berdasarkan yang terbaru diselesaikan
    }
}