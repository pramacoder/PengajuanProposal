<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\RuangKontrol;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CheckActivePhaseTest extends TestCase
{
    use RefreshDatabase;

    // =========================================================
    // Mahasiswa — fase pendaftaran
    // =========================================================

    public function test_mahasiswa_blocked_when_no_ruang_kontrol_exists()
    {
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa', 'identifier' => '2500001001']);

        $response = $this->actingAs($mahasiswa)
            ->get('/mahasiswa/proposal/create');

        // Harus redirect (fase tidak bisa ditemukan)
        $response->assertStatus(302);
    }

    public function test_mahasiswa_blocked_when_pendaftaran_closed()
    {
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa', 'identifier' => '2500001002']);
        RuangKontrol::factory()->semuaTertutup()->create();

        $response = $this->actingAs($mahasiswa)
            ->get('/mahasiswa/proposal/create');

        $response->assertStatus(302);
        $response->assertRedirect(); // Redirect ke URL sebelumnya / login
    }

    public function test_mahasiswa_allowed_when_pendaftaran_open()
    {
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa', 'identifier' => '2500001003']);
        RuangKontrol::factory()->pendaftaranTerbuka()->create();

        $response = $this->actingAs($mahasiswa)
            ->get('/mahasiswa/proposal/create');

        $response->assertStatus(200);
    }

    // =========================================================
    // Reviewer — fase review
    // =========================================================

    public function test_reviewer_blocked_for_review_when_phase_closed()
    {
        $reviewer = User::factory()->create(['role' => 'reviewer', 'identifier' => 'RV_PHASE01']);
        RuangKontrol::factory()->semuaTertutup()->create();

        $response = $this->actingAs($reviewer)
            ->get('/reviewer/review-administratif');

        $response->assertStatus(302); // Redirect, bukan 200
    }

    public function test_reviewer_allowed_for_review_when_review_open()
    {
        $reviewer = User::factory()->create(['role' => 'reviewer', 'identifier' => 'RV_PHASE02']);
        RuangKontrol::factory()->reviewTerbuka()->create();

        $response = $this->actingAs($reviewer)
            ->get('/reviewer/review-administratif');

        // Bisa mengakses (200 atau redirect halaman tertentu tergantung data — bukan 302 ke middleware)
        $this->assertNotEquals(302, $response->getStatusCode());
    }

    // =========================================================
    // Mahasiswa — fase perbaikan (revisi)
    // =========================================================

    public function test_mahasiswa_blocked_revisi_when_perbaikan_closed()
    {
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa', 'identifier' => '2500001010']);
        RuangKontrol::factory()->semuaTertutup()->create();

        // POST revisi ke proposal dummy id=1 (tidak harus ada)
        $response = $this->actingAs($mahasiswa)
            ->get('/mahasiswa/revisi');

        $response->assertStatus(302);
    }

    public function test_mahasiswa_allowed_revisi_when_perbaikan_open()
    {
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa', 'identifier' => '2500001011']);
        RuangKontrol::factory()->perbaikanTerbuka()->create();

        $response = $this->actingAs($mahasiswa)
            ->get('/mahasiswa/revisi');

        // Bisa mengakses (200 atau halaman kosong — bukan redirect middleware)
        $this->assertNotEquals(302, $response->getStatusCode());
    }

    // =========================================================
    // JSON response saat fase tertutup (API request)
    // =========================================================

    public function test_json_request_returns_403_when_phase_closed()
    {
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa', 'identifier' => '2500001020']);
        RuangKontrol::factory()->semuaTertutup()->create();

        $response = $this->actingAs($mahasiswa)
            ->postJson('/mahasiswa/proposal/store', []);

        // JSON response harus 403 dengan error PHASE_NOT_ACTIVE
        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'error'   => 'PHASE_NOT_ACTIVE',
        ]);
    }

    // =========================================================
    // Operator tidak dibatasi fase (tidak ada check.phase di route operator)
    // =========================================================

    public function test_operator_can_access_ruang_kontrol_regardless_of_phase()
    {
        $operator = User::factory()->create(['role' => 'operator', 'identifier' => 'OP_PHASE01']);
        RuangKontrol::factory()->semuaTertutup()->create();

        $response = $this->actingAs($operator)
            ->get('/operator/ruang-kontrol');

        $response->assertStatus(200);
    }
}
