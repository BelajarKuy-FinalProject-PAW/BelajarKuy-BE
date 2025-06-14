<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User; // Import User model untuk assertDatabaseHas dan factory
use Illuminate\Support\Str; // Untuk Str::random agar username dan email unik saat testing
use PHPUnit\Framework\Attributes\Test; // Untuk menggunakan atribut #[Test]

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    #[Test] // Menggunakan atribut PHP 8
    public function new_users_can_register_successfully_with_valid_data(): void
    {
        $userData = [
            'name' => 'Test User Registrasi',
            'username' => 'testuser' . Str::random(5),
            'email' => 'test.' . strtolower(Str::random(5)) . '@example.com', // <-- DIPERBAIKI DI SINI
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/register', $userData);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'user' => [
                         'id',
                         'name',
                         'username',
                         'email',
                         'no_telp',
                         'profile_photo_url',
                         'email_verified_at',
                     ],
                     'token'
                 ])
                 ->assertJsonPath('message', 'Registrasi berhasil.')
                 ->assertJsonPath('user.name', $userData['name'])
                 ->assertJsonPath('user.email', $userData['email'])
                 ->assertJsonPath('user.username', $userData['username']);
        
        $this->assertDatabaseHas('users', [
            'email' => $userData['email'],
            'username' => $userData['username'],
        ]);

        $responseData = $response->json();
        $this->assertNotNull($responseData['token'], "Token tidak boleh null setelah registrasi.");
        $this->assertEquals($userData['email'], $responseData['user']['email'], "Email user di response tidak sesuai.");
    }

    #[Test] // Menggunakan atribut PHP 8
    public function registration_fails_if_username_is_missing(): void
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'testmissinguser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/register', $userData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['username']);
    }

    #[Test] // Menggunakan atribut PHP 8
    public function registration_fails_if_email_is_invalid(): void
    {
        $userData = [
            'name' => 'Test User',
            'username' => 'testinvalidemailuser',
            'email' => 'bukan-email-valid',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/register', $userData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }
    
    #[Test] // Menggunakan atribut PHP 8
    public function registration_fails_if_email_already_exists(): void
    {
        $existingUser = User::factory()->create([
            'email' => 'exist.' . strtolower(Str::random(5)) . '@example.com' // Pastikan email factory juga konsisten jika perlu
        ]);

        $userData = [
            'name' => 'Another Test User',
            'username' => 'newtestuser'. Str::random(5),
            'email' => $existingUser->email, // Menggunakan email yang sudah ada
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/register', $userData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    #[Test] // Menggunakan atribut PHP 8
    public function registration_fails_if_password_confirmation_does_not_match(): void
    {
        $userData = [
            'name' => 'Test User',
            'username' => 'testuser' . Str::random(5),
            'email' => 'test.' . strtolower(Str::random(5)) . '@example.com', // Sudah benar di sini
            'password' => 'password123',
            'password_confirmation' => 'passwordyangsalah',
        ];

        $response = $this->postJson('/register', $userData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['password']);
    }
}