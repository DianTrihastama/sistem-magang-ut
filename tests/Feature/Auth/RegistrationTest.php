<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'no_hp' => '081234567890',
            'instansi_pendidikan' => 'SMK Negeri 1 Samarinda',
            'jurusan' => 'Teknik Komputer dan Jaringan',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'role' => 'user',
        ]);

        $this->assertDatabaseHas('peserta', [
            'nama_lengkap' => 'Test User',
            'instansi_pendidikan' => 'SMK Negeri 1 Samarinda',
            'status_tahap' => 'pendaftaran',
        ]);
    }
}
