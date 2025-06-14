<?php

namespace Tests\Unit;

use App\Http\Resources\UserResource; // Import UserResource Anda
use App\Models\User;                // Import User model
use Carbon\Carbon;                  // Untuk bekerja dengan Carbon instance
use Illuminate\Http\Request;        // Untuk membuat mock request
use Tests\TestCase;                 // Menggunakan TestCase Laravel
// use Illuminate\Foundation\Testing\RefreshDatabase; // Bisa diaktifkan jika menggunakan create()
use PHPUnit\Framework\Attributes\Test;

class UserResourceTest extends TestCase // Pastikan extends Tests\TestCase
{
    // use RefreshDatabase; // Aktifkan jika Anda menggunakan User::factory()->create()

    #[Test]
    public function it_transforms_user_correctly_when_all_fields_are_present_and_email_is_verified(): void
    {
        // 1. Arrange: Buat instance User dengan data lengkap
        // Menggunakan make() agar tidak menyimpan ke database, cocok untuk unit test resource
        $now = Carbon::now();
        $user = User::factory()->make([
            'id' => 1,
            'name' => 'John Doe',
            'username' => 'johndoe',
            'email' => 'john.doe@example.com',
            'no_telp' => '08123456789',
            'profile_photo_path' => 'avatars/johndoe.jpg', // Path foto
            'email_verified_at' => $now,
        ]);

        // Buat mock request sederhana
        $request = Request::create('/');

        // Buat instance UserResource
        $userResource = new UserResource($user);

        // 2. Act: Transformasi resource menjadi array
        $resourceArray = $userResource->toArray($request);

        // 3. Assert: Verifikasi struktur dan nilai
        $this->assertEquals([
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'no_telp' => $user->no_telp,
            'profile_photo_url' => $user->profile_photo_url, // Memanggil accessor
            'email_verified_at' => $now->toIso8601String(),
        ], $resourceArray);
    }

    #[Test]
    public function it_transforms_user_correctly_when_optional_fields_are_null_and_email_not_verified(): void
    {
        // 1. Arrange
        $user = User::factory()->make([
            'id' => 2,
            'name' => 'Jane Doe',
            'username' => 'janedoe',
            'email' => 'jane.doe@example.com',
            'no_telp' => null,
            'profile_photo_path' => null, // Tidak ada path foto
            'email_verified_at' => null,  // Email belum terverifikasi
        ]);

        $request = Request::create('/');
        $userResource = new UserResource($user);

        // 2. Act
        $resourceArray = $userResource->toArray($request);

        // 3. Assert
        $this->assertEquals([
            'id' => $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'no_telp' => null,
            'profile_photo_url' => 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&color=7F9CF5&background=EBF4FF&format=svg', // Fallback UI Avatars
            'email_verified_at' => null,
        ], $resourceArray);
    }
}