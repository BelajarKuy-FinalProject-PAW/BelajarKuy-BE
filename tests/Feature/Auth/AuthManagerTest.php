<?php

namespace Tests\Feature\Auth; // Atau namespace yang sesuai jika di file baru

// use Illuminate\Support\Facades\Auth; // Jika menggunakan Auth::
use Tests\TestCase;

class AuthManagerTest extends TestCase // Atau nama class yang sesuai
{
    public function test_can_call_forget_resolved_users_on_auth_manager()
    {
        // Opsi 1: Menggunakan $this->app['auth']
        $authManager = $this->app['auth'];
        $this->assertInstanceOf(\Illuminate\Auth\AuthManager::class, $authManager);
        $authManager->forgetResolvedUsers(); // Memanggil method
        $this->assertTrue(true); // Jika sampai sini tanpa error, pemanggilan berhasil

        // Opsi 2: Menggunakan resolve()
        // $authManagerResolved = resolve('auth');
        // $this->assertInstanceOf(\Illuminate\Auth\AuthManager::class, $authManagerResolved);
        // $authManagerResolved->forgetResolvedUsers();
        // $this->assertTrue(true);

        // Opsi 3: Menggunakan Facade (pastikan 'use Illuminate\Support\Facades\Auth;' ada di atas)
        // \Illuminate\Support\Facades\Auth::forgetResolvedUsers();
        // $this->assertTrue(true);
    }
}