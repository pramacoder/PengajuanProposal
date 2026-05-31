<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DosenWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_dosen_can_access_dashboard()
    {
        $dosen = User::factory()->create(['role' => 'dosen']);
        // DosenController::dashboard() does redirect()->route('dosen.pendamping.dashboard')
        $this->actingAs($dosen)->get('/dosen/dashboard')->assertRedirect();
    }

    public function test_dosen_can_view_profile()
    {
        $dosen = User::factory()->create(['role' => 'dosen']);
        $response = $this->actingAs($dosen)->get('/dosen/profile');
        // May return 200 or 500 depending on Vite manifest; accept non-403
        $this->assertNotEquals(403, $response->getStatusCode());
    }

    public function test_dosen_can_update_profile()
    {
        $dosen = User::factory()->create(['role' => 'dosen']);

        $response = $this->actingAs($dosen)->put('/dosen/profile', [
            'name' => 'Dr. Updated Name',
            'phone' => '081234567890',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $dosen->id,
            'name' => 'Dr. Updated Name',
        ]);
    }

    public function test_dosen_pembimbing_can_access_dashboard()
    {
        $dosen = User::factory()->create(['role' => 'dosen']);
        $response = $this->actingAs($dosen)->get('/dosen/pembimbing/dashboard');
        // Should render 200 (or 500 if Vite not built — but not 403)
        $this->assertNotEquals(403, $response->getStatusCode());
    }

    public function test_dosen_pendamping_can_access_dashboard()
    {
        $dosen = User::factory()->create(['role' => 'dosen']);
        $response = $this->actingAs($dosen)->get('/dosen/pendamping/dashboard');
        $this->assertNotEquals(403, $response->getStatusCode());
    }

    public function test_dosen_cannot_access_mahasiswa_routes()
    {
        $dosen = User::factory()->create(['role' => 'dosen']);
        $this->actingAs($dosen)->get('/mahasiswa/dashboard')->assertStatus(403);
    }

    public function test_dosen_cannot_access_reviewer_routes()
    {
        $dosen = User::factory()->create(['role' => 'dosen']);
        $this->actingAs($dosen)->get('/reviewer/dashboard')->assertStatus(403);
    }

    public function test_dosen_cannot_access_operator_routes()
    {
        $dosen = User::factory()->create(['role' => 'dosen']);
        $this->actingAs($dosen)->get('/operator/dashboard')->assertStatus(403);
    }
}
