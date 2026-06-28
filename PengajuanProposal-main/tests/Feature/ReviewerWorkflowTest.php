<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\RuangKontrol;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReviewerWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_reviewer_can_access_dashboard()
    {
        $reviewer = User::factory()->create(['role' => 'reviewer']);
        $this->actingAs($reviewer)->get('/reviewer/dashboard')->assertStatus(200);
    }

    public function test_reviewer_can_access_review_administratif_when_open()
    {
        $reviewer = User::factory()->create(['role' => 'reviewer']);
        RuangKontrol::factory()->reviewTerbuka()->create();

        $response = $this->actingAs($reviewer)->get('/reviewer/review-administratif');
        $this->assertNotEquals(302, $response->getStatusCode());
    }

    public function test_reviewer_blocked_from_review_when_closed()
    {
        $reviewer = User::factory()->create(['role' => 'reviewer']);
        RuangKontrol::factory()->semuaTertutup()->create();

        $this->actingAs($reviewer)->get('/reviewer/review-administratif')->assertStatus(302);
    }

    public function test_reviewer_can_access_substantif_when_open()
    {
        $reviewer = User::factory()->create(['role' => 'reviewer']);
        RuangKontrol::factory()->reviewTerbuka()->create();

        $response = $this->actingAs($reviewer)->get('/reviewer/review-substantif');
        $this->assertNotEquals(302, $response->getStatusCode());
    }

    public function test_reviewer_cannot_access_operator_routes()
    {
        $reviewer = User::factory()->create(['role' => 'reviewer']);
        $this->actingAs($reviewer)->get('/operator/dashboard')->assertStatus(403);
    }

    public function test_reviewer_cannot_access_mahasiswa_routes()
    {
        $reviewer = User::factory()->create(['role' => 'reviewer']);
        $this->actingAs($reviewer)->get('/mahasiswa/dashboard')->assertStatus(403);
    }

    public function test_reviewer_can_access_profile()
    {
        $reviewer = User::factory()->create(['role' => 'reviewer']);
        $this->actingAs($reviewer)->get('/reviewer/profile')->assertStatus(200);
    }

    public function test_reviewer_can_update_profile()
    {
        $reviewer = User::factory()->create(['role' => 'reviewer']);

        $response = $this->actingAs($reviewer)->put('/reviewer/profile', [
            'name' => 'Updated Name',
            'phone' => '081234567890',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'id' => $reviewer->id,
            'name' => 'Updated Name',
        ]);
    }
}
