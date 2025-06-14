<?php

namespace Tests\Feature;

use App\Models\User;
// use App\Http\Resources\UserResource; // Tidak perlu di sini, kita cek JSON response
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Facades\Storage; 
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str; 
use Illuminate\Support\Facades\Hash;

class ProfileApiTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function authenticated_user_can_get_their_profile(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/user');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'data' => [
                         'id',
                         'name',
                         'username',
                         'email',
                         'no_telp',
                         'profile_photo_url',
                         'email_verified_at',
                     ]
                 ])
                 ->assertJson([
                     'data' => [
                         'id' => $user->id,
                         'name' => $user->name,
                         'username' => $user->username,
                         'email' => $user->email,
                     ]
                 ]);
    }

    #[Test]
    public function unauthenticated_user_cannot_get_profile(): void
    {
        $response = $this->getJson('/api/user');
        $response->assertUnauthorized();
    }

    // ================================================
    // METHOD TEST BARU UNTUK UPDATE PROFIL
    // ================================================

    #[Test]
    public function authenticated_user_can_update_their_profile_with_valid_data(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $updatedData = [
            'name' => 'Updated Name',
            'username' => 'updated' . Str::lower(Str::random(5)), // Username unik baru
            'email' => 'updated.' . Str::lower(Str::random(5)) . '@example.com', // Email unik baru
            'no_telp' => '08987654321',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/user/profile', $updatedData);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'message',
                     'user' => [
                         'id',
                         'name',
                         'username',
                         'email',
                         'no_telp',
                         // ... field lain dari UserResource
                     ]
                 ])
                 ->assertJsonPath('message', 'Profil berhasil diperbarui.')
                 ->assertJsonPath('user.name', $updatedData['name'])
                 ->assertJsonPath('user.username', $updatedData['username'])
                 ->assertJsonPath('user.email', $updatedData['email'])
                 ->assertJsonPath('user.no_telp', $updatedData['no_telp']);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => $updatedData['name'],
            'username' => $updatedData['username'],
            'email' => $updatedData['email'],
            'no_telp' => $updatedData['no_telp'],
        ]);
    }

    #[Test]
    public function authenticated_user_cannot_update_profile_with_email_already_taken(): void
    {
        $user1 = User::factory()->create();
        $token1 = $user1->createToken('token-user1')->plainTextToken;

        $user2 = User::factory()->create(['email' => 'taken@example.com']); // User dengan email yang akan dicoba diambil

        $updatedData = [
            'name' => 'User One Updated',
            'email' => $user2->email, // Mencoba menggunakan email milik user2
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token1,
        ])->postJson('/api/user/profile', $updatedData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function authenticated_user_cannot_update_profile_with_username_already_taken(): void
    {
        $user1 = User::factory()->create();
        $token1 = $user1->createToken('token-user1')->plainTextToken;

        $user2 = User::factory()->create(['username' => 'takenusername']); // User dengan username yang akan dicoba diambil

        $updatedData = [
            'name' => 'User One Updated Again',
            'username' => $user2->username, // Mencoba menggunakan username milik user2
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token1,
        ])->postJson('/api/user/profile', $updatedData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['username']);
    }

    #[Test]
    public function unauthenticated_user_cannot_update_profile(): void
    {
        $updatedData = [
            'name' => 'New Name For No One',
        ];

        $response = $this->postJson('/api/user/profile', $updatedData);

        $response->assertUnauthorized();
    }
    #[Test]
    public function authenticated_user_can_update_their_password_with_valid_data(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('old_password'),
        ]);
        $token = $user->createToken('test-token')->plainTextToken;

        $passwordData = [
            'current_password' => 'old_password',
            'password' => 'new_secure_password',
            'password_confirmation' => 'new_secure_password',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/user/password', $passwordData);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Kata sandi berhasil diubah.']);

        // Verifikasi bahwa password di database sudah berubah
        // Kita perlu mengambil user lagi dari database untuk memastikan data terbaru
        $this->assertTrue(Hash::check('new_secure_password', $user->fresh()->password));
    }

    #[Test]
    public function authenticated_user_cannot_update_password_if_current_password_is_incorrect(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('old_password'),
        ]);
        $token = $user->createToken('test-token')->plainTextToken;

        $passwordData = [
            'current_password' => 'wrong_current_password',
            'password' => 'new_secure_password',
            'password_confirmation' => 'new_secure_password',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/user/password', $passwordData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['current_password']);
        
        // Pastikan password lama tidak berubah
        $this->assertTrue(Hash::check('old_password', $user->fresh()->password));
    }

    #[Test]
    public function authenticated_user_cannot_update_password_if_new_password_confirmation_does_not_match(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('old_password'),
        ]);
        $token = $user->createToken('test-token')->plainTextToken;

        $passwordData = [
            'current_password' => 'old_password',
            'password' => 'new_secure_password',
            'password_confirmation' => 'mismatched_new_password',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/user/password', $passwordData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['password']); // Aturan 'confirmed' pada password
    }

    #[Test]
    public function authenticated_user_cannot_update_password_if_new_password_is_too_short(): void
    {
        // Asumsi Password::defaults() memerlukan minimal 8 karakter (standar Laravel)
        $user = User::factory()->create([
            'password' => Hash::make('old_password'),
        ]);
        $token = $user->createToken('test-token')->plainTextToken;

        $passwordData = [
            'current_password' => 'old_password',
            'password' => 'short', // Password baru terlalu pendek
            'password_confirmation' => 'short',
        ];

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/user/password', $passwordData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['password']); // Akan error karena aturan panjang minimal dari Password::defaults()
    }

    #[Test]
    public function unauthenticated_user_cannot_update_password(): void
    {
        $passwordData = [
            'current_password' => 'any_password',
            'password' => 'new_password',
            'password_confirmation' => 'new_password',
        ];

        $response = $this->postJson('/api/user/password', $passwordData);

        $response->assertUnauthorized();
    }
    #[Test]
    public function authenticated_user_can_upload_avatar_with_valid_image(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        Storage::fake('public'); // Gunakan fake storage untuk disk 'public'

        $file = UploadedFile::fake()->image('avatar.jpg', 200, 200)->size(500); // Buat file gambar dummy (500KB)

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/user/avatar', [
            'avatar' => $file,
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['message', 'profile_photo_url'])
                 ->assertJsonPath('message', 'Foto profil berhasil diunggah.');

        // Ambil path foto dari response untuk verifikasi
        $newPhotoPath = $user->fresh()->profile_photo_path;
        $this->assertNotNull($newPhotoPath, "Path foto profil seharusnya tidak null di database.");
        
        // Pastikan file "tersimpan" di fake storage
        Storage::disk('public')->assertExists($newPhotoPath);

        // Pastikan URL yang dikembalikan mengandung path yang baru
        $this->assertStringContainsString(Storage::url($newPhotoPath), $response->json('profile_photo_url'));

        // Hapus file lama jika ada (controller Anda sudah menangani ini)
        // Untuk test ini, kita hanya pastikan file baru ada dan path di DB terupdate
    }

    #[Test]
    public function authenticated_user_cannot_upload_avatar_if_file_is_not_provided(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/user/avatar', []); // Tidak mengirim field 'avatar'

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['avatar']); // Sesuai UploadAvatarRequest: 'avatar' => ['required', ...]
    }

    #[Test]
    public function authenticated_user_cannot_upload_avatar_if_file_is_not_an_image(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        Storage::fake('public');
        $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'); // File bukan gambar

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/user/avatar', [
            'avatar' => $file,
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['avatar']); // Sesuai UploadAvatarRequest: File::image()
    }

    #[Test]
    public function authenticated_user_cannot_upload_avatar_if_file_is_too_large(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        Storage::fake('public');
        // File 3MB (UploadAvatarRequest Anda memiliki max 2MB)
        $file = UploadedFile::fake()->image('large_avatar.jpg')->size(3 * 1024); 

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/user/avatar', [
            'avatar' => $file,
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['avatar']); // Sesuai UploadAvatarRequest: max(2 * 1024)
    }

    #[Test]
    public function unauthenticated_user_cannot_upload_avatar(): void
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->postJson('/api/user/avatar', [
            'avatar' => $file,
        ]);

        $response->assertUnauthorized();
    }
    #[Test]
    public function authenticated_user_can_delete_their_account_with_correct_password(): void
    {
        $userPassword = 'password123';
        $user = User::factory()->create([
            'password' => Hash::make($userPassword),
        ]);
        $token = $user->createToken('test-token')->plainTextToken;
        $userId = $user->id; // Simpan ID user sebelum dihapus

        // Simulasikan ada foto profil yang mungkin perlu dihapus
        // Storage::fake('public'); // Sudah di-fake di test upload avatar, jika dijalankan dalam satu class, ini mungkin tidak perlu di-ulang
        // $user->update(['profile_photo_path' => UploadedFile::fake()->image('old_avatar.jpg')->store('avatars', 'public')]);
        // $oldAvatarPath = $user->profile_photo_path;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson('/api/user', [
            'password' => $userPassword,
        ]);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Akun Anda telah berhasil dihapus.']);

        // Verifikasi bahwa user sudah tidak ada di database
        $this->assertDatabaseMissing('users', ['id' => $userId]);
        
        // Verifikasi bahwa semua token personal access milik user tersebut juga sudah dihapus
        // Controller Anda memanggil $user->tokens()->delete();
        $this->assertDatabaseMissing('personal_access_tokens', ['tokenable_id' => $userId, 'tokenable_type' => User::class]);

        // (Opsional) Jika Anda ingin memastikan file avatar juga terhapus:
        // if ($oldAvatarPath) {
        //     Storage::disk('public')->assertMissing($oldAvatarPath);
        // }
    }

    #[Test]
    public function authenticated_user_cannot_delete_their_account_with_incorrect_password(): void
    {
        $userPassword = 'password123';
        $user = User::factory()->create([
            'password' => Hash::make($userPassword),
        ]);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->deleteJson('/api/user', [
            'password' => 'wrong_password', // Password salah
        ]);

        $response->assertStatus(422) // Atau 403 jika Anda menangani berbeda
                 ->assertJsonValidationErrors(['password']); // Sesuai dengan ValidationException di controller Anda

        // Pastikan user masih ada di database
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    #[Test]
    public function unauthenticated_user_cannot_delete_account(): void
    {
        // Buat user dummy agar ada user ID yang bisa coba dihapus (meskipun tidak akan berhasil)
        $user = User::factory()->create();

        $response = $this->deleteJson('/api/user', [
            'password' => 'any_password',
        ]);

        $response->assertUnauthorized();
        
        // Pastikan user dummy masih ada di database
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }
}