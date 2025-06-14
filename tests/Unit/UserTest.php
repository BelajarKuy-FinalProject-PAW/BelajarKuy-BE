<?php

namespace Tests\Unit;

use App\Models\User; // Import User model Anda
use Illuminate\Support\Facades\Storage; // Untuk mock Storage facade
use Mockery; // Untuk menggunakan Mockery
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration; // Untuk integrasi Mockery dengan PHPUnit (cleanup)
use PHPUnit\Framework\Attributes\Test; // Menggunakan Atribut Test PHPUnit
use Tests\TestCase; // Menggunakan TestCase Laravel agar facade Storage bisa di-mock dengan benar

class UserTest extends TestCase // Ganti menjadi Tests\TestCase
{
    // Menggunakan trait ini membantu membersihkan mock Mockery setelah setiap test
    use MockeryPHPUnitIntegration;

    #[Test]
    public function it_returns_storage_url_when_profile_photo_path_exists_and_file_exists(): void
    {
        // 1. Arrange (Persiapan)
        $fakePhotoPath = 'avatars/photo.jpg';
        $expectedUrl = config('app.url').'/storage/' . $fakePhotoPath;


        // Membuat mock untuk Storage facade
        Storage::shouldReceive('disk')
            ->with('public')
            ->once()
            ->andReturnSelf();

        Storage::shouldReceive('exists')
            ->with($fakePhotoPath)
            ->once()
            ->andReturn(true);

        Storage::shouldReceive('url')
            ->with($fakePhotoPath)
            ->once()
            ->andReturn($expectedUrl); // Mengembalikan URL yang sudah kita tentukan

        $user = new User([
            'name' => 'Test User',
            'profile_photo_path' => $fakePhotoPath,
        ]);

        // 2. Act (Aksi)
        $profilePhotoUrl = $user->profile_photo_url;

        // 3 Verifikasi 
        $this->assertEquals($expectedUrl, $profilePhotoUrl);
    }

    #[Test]
    public function it_returns_ui_avatars_url_when_profile_photo_path_is_null(): void
    {
        // 1. Arrange
        $userName = 'John Doe';
        $user = new User([
            'name' => $userName,
            'profile_photo_path' => null,
        ]);

        $expectedUiAvatarsUrl = 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&color=7F9CF5&background=EBF4FF&format=svg';

        // 2. Act
        $profilePhotoUrl = $user->profile_photo_url;

        // 3 Verifikasi
        $this->assertEquals($expectedUiAvatarsUrl, $profilePhotoUrl);
    }

    #[Test]
    public function it_returns_ui_avatars_url_when_profile_photo_file_does_not_exist(): void
    {
        // 1. Arrange
        $userName = 'Jane Doe';
        $fakePhotoPath = 'avatars/non_existent_photo.jpg';
        
        Storage::shouldReceive('disk')
            ->with('public')
            ->once()
            ->andReturnSelf();

        Storage::shouldReceive('exists')
            ->with($fakePhotoPath)
            ->once()
            ->andReturn(false);

        $user = new User([
            'name' => $userName,
            'profile_photo_path' => $fakePhotoPath,
        ]);

        $expectedUiAvatarsUrl = 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&color=7F9CF5&background=EBF4FF&format=svg';

        // 2. Act
        $profilePhotoUrl = $user->profile_photo_url;

        // 3 Verifikasi
        $this->assertEquals($expectedUiAvatarsUrl, $profilePhotoUrl);
    }
}