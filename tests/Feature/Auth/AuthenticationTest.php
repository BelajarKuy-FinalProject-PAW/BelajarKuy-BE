<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'), // Pastikan password di factory sesuai
        ]);

        // Gunakan postJson untuk request API
        $response = $this->postJson('/login', [ // Atau /api/login jika itu endpoint Anda
            // Pastikan field untuk login sesuai dengan LoginRequest Anda ('login' atau 'email')
            'login' => $user->email, // Jika LoginRequest Anda mengharapkan 'login' yang bisa berisi email/username
            // 'email' => $user->email, // Jika LoginRequest Anda mengharapkan 'email' secara spesifik
            'password' => 'password',
        ]);

        $this->assertAuthenticated(); // Tetap penting untuk memastikan user terautentikasi

        // Sesuaikan assertion dengan response dari AuthenticatedSessionController@store
        $response->assertStatus(200) // Controller Anda mengembalikan status 200
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
                 ->assertJsonPath('message', 'Login berhasil.') // Cocokkan dengan pesan di controller Anda
                 ->assertJsonPath('user.id', $user->id)
                 ->assertJsonPath('user.email', $user->email);
                 // Anda bisa tambahkan assertJsonPath lain untuk field user jika perlu
    }

    // ... test method lainnya tetap sama ...

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        // Sebaiknya gunakan postJson juga di sini
        $this->postJson('/login', [ // Atau /api/login
            'login' => $user->email, // Sesuaikan dengan field yang diharapkan LoginRequest
            // 'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        // Jika LoginRequest mengembalikan 422 dengan pesan error:
        // $response->assertStatus(422)
        //          ->assertJsonValidationErrors(['login']); // atau field yang sesuai
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $newAccessToken = $user->createToken('test-logout-token');
        $plainTextToken = $newAccessToken->plainTextToken;
        $tokenId = $newAccessToken->accessToken->id;
        // 1
        $this->withHeaders([
            'Authorization' => 'Bearer ' . $plainTextToken,
        ])->postJson('/api/logout') 
          ->assertNoContent(); 
        // 2
        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $tokenId
        ]);

        $this->app['auth']->forgetGuards(); 
        
        $responseAfterLogout = $this->withHeaders([
            'Authorization' => 'Bearer ' . $plainTextToken,
        ])->getJson('/api/user'); 

        $responseAfterLogout->assertUnauthorized(); 
    }
}
