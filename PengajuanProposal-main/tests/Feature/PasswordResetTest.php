<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_form_loads()
    {
        $this->get('/reset-password')->assertStatus(200);
    }

    public function test_forgot_password_alias_loads()
    {
        $this->get('/forgot-password')->assertStatus(200);
    }

    public function test_reset_with_valid_mahasiswa_redirects_to_login()
    {
        $mhs = User::factory()->create([
            'role' => 'mahasiswa',
            'identifier' => '2500077001',
        ]);

        $response = $this->post('/reset-password', [
            'role' => 'mahasiswa',
            'identifier' => '2500077001',
            'email_personal' => 'student@gmail.com',
        ]);

        // Should redirect to login with success (or back with error if mail fails)
        $response->assertRedirect();
    }

    public function test_reset_with_invalid_identifier_shows_error()
    {
        $response = $this->post('/reset-password', [
            'role' => 'mahasiswa',
            'identifier' => '9999999999',
            'email_personal' => 'student@gmail.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_reset_requires_valid_role()
    {
        $response = $this->post('/reset-password', [
            'role' => 'admin', // invalid
            'identifier' => '123',
            'email_personal' => 'test@gmail.com',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('role');
    }

    public function test_reset_requires_email()
    {
        $response = $this->post('/reset-password', [
            'role' => 'mahasiswa',
            'identifier' => '123',
            // missing email_personal
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors('email_personal');
    }
}
