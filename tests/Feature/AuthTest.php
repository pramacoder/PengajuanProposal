<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    // =========================================================
    // Redirect & Guest
    // =========================================================

    public function test_root_redirects_to_login()
    {
        $this->get('/')->assertRedirect('/login');
    }

    public function test_guest_cannot_access_operator_dashboard()
    {
        $this->get('/operator/dashboard')->assertRedirect('/login');
    }

    public function test_guest_cannot_access_mahasiswa_dashboard()
    {
        $this->get('/mahasiswa/dashboard')->assertRedirect('/login');
    }

    // =========================================================
    // Login — form menggunakan field 'email'
    // =========================================================

    public function test_login_page_loads()
    {
        $this->get('/login')->assertStatus(200);
    }

    public function test_login_with_invalid_email_fails_validation()
    {
        $response = $this->post('/login', [
            'email'    => 'notexist@test.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors();
    }

    public function test_operator_can_login_and_redirected_to_dashboard()
    {
        User::factory()->create([
            'role'     => 'operator',
            'email'    => 'operator@test.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email'    => 'operator@test.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/operator/dashboard');
        $this->assertAuthenticated();
    }

    public function test_mahasiswa_can_login_and_redirected_to_dashboard()
    {
        User::factory()->create([
            'role'     => 'mahasiswa',
            'email'    => 'mahasiswa@test.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email'    => 'mahasiswa@test.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/mahasiswa/dashboard');
        $this->assertAuthenticated();
    }

    public function test_reviewer_can_login_and_redirected_to_dashboard()
    {
        User::factory()->create([
            'role'     => 'reviewer',
            'email'    => 'reviewer@test.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email'    => 'reviewer@test.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/reviewer/dashboard');
        $this->assertAuthenticated();
    }

    // =========================================================
    // Logout
    // =========================================================

    public function test_authenticated_user_can_logout()
    {
        $user = User::factory()->create(['role' => 'operator']);
        $this->actingAs($user)->post('/logout')->assertRedirect('/login');
        $this->assertGuest();
    }

    // =========================================================
    // Role Guard — cross-role access: middleware CheckUserType return 403
    // =========================================================

    public function test_mahasiswa_gets_403_on_operator_dashboard()
    {
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);
        $this->actingAs($mahasiswa)->get('/operator/dashboard')->assertStatus(403);
    }

    public function test_reviewer_gets_403_on_operator_routes()
    {
        $reviewer = User::factory()->create(['role' => 'reviewer']);
        $this->actingAs($reviewer)->get('/operator/dashboard')->assertStatus(403);
    }

    public function test_operator_gets_403_on_mahasiswa_routes()
    {
        $operator = User::factory()->create(['role' => 'operator']);
        $this->actingAs($operator)->get('/mahasiswa/dashboard')->assertStatus(403);
    }

    public function test_dosen_gets_403_on_operator_dashboard()
    {
        $dosen = User::factory()->create(['role' => 'dosen']);
        $this->actingAs($dosen)->get('/operator/dashboard')->assertStatus(403);
    }

    // =========================================================
    // Inactive user harus ditolak login
    // =========================================================

    public function test_inactive_user_cannot_login()
    {
        User::factory()->create([
            'role'      => 'mahasiswa',
            'email'     => 'inactive@test.com',
            'password'  => bcrypt('password'),
            'is_active' => false,
        ]);

        $response = $this->post('/login', [
            'email'    => 'inactive@test.com',
            'password' => 'password',
        ]);

        $response->assertStatus(302);
        $this->assertGuest();
    }
}
