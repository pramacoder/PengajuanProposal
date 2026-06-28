<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\RuangKontrol;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OperatorManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_operator_can_access_dashboard()
    {
        $operator = User::factory()->create(['role' => 'operator']);
        $this->actingAs($operator)->get('/operator/dashboard')->assertStatus(200);
    }

    public function test_operator_can_access_account_management()
    {
        $operator = User::factory()->create(['role' => 'operator']);
        $this->actingAs($operator)->get('/operator/akun')->assertStatus(200);
    }

    public function test_operator_can_access_ruang_kontrol()
    {
        $operator = User::factory()->create(['role' => 'operator']);
        $this->actingAs($operator)->get('/operator/ruang-kontrol')->assertStatus(200);
    }

    public function test_operator_can_create_jadwal()
    {
        $operator = User::factory()->create(['role' => 'operator']);
        $base = now();

        $response = $this->actingAs($operator)->postJson('/operator/create-jadwal', [
            'tahun_ajaran' => '2025/2026',
            'nama_history' => 'Jadwal Test',
            'tanggal_pendaftaran_mulai' => $base->copy()->format('Y-m-d'),
            'tanggal_pendaftaran_selesai' => $base->copy()->addMonth()->format('Y-m-d'),
            'tanggal_review_mulai' => $base->copy()->addMonths(2)->format('Y-m-d'),
            'tanggal_review_selesai' => $base->copy()->addMonths(3)->format('Y-m-d'),
            'tanggal_perbaikan_mulai' => $base->copy()->addMonths(4)->format('Y-m-d'),
            'tanggal_perbaikan_selesai' => $base->copy()->addMonths(5)->format('Y-m-d'),
            'tanggal_penilaian_akhir_mulai' => $base->copy()->addMonths(6)->format('Y-m-d'),
            'tanggal_penilaian_akhir_selesai' => $base->copy()->addMonths(7)->format('Y-m-d'),
        ]);

        $response->assertStatus(200);
    }

    public function test_pimpinan_pt_can_access_operator_routes()
    {
        $pimpinan = User::factory()->create(['role' => 'pimpinan_pt']);
        $this->actingAs($pimpinan)->get('/operator/dashboard')->assertStatus(200);
    }

    public function test_mahasiswa_cannot_access_operator_routes()
    {
        $mhs = User::factory()->create(['role' => 'mahasiswa']);
        $this->actingAs($mhs)->get('/operator/dashboard')->assertStatus(403);
    }

    public function test_dosen_cannot_access_operator_routes()
    {
        $dosen = User::factory()->create(['role' => 'dosen']);
        $this->actingAs($dosen)->get('/operator/dashboard')->assertStatus(403);
    }

    public function test_reviewer_cannot_access_operator_routes()
    {
        $reviewer = User::factory()->create(['role' => 'reviewer']);
        $this->actingAs($reviewer)->get('/operator/dashboard')->assertStatus(403);
    }

    public function test_operator_can_view_form_penilaian()
    {
        $operator = User::factory()->create(['role' => 'operator']);
        $this->actingAs($operator)->get('/operator/form-penilaian')->assertStatus(200);
    }

    public function test_guest_cannot_access_operator_routes()
    {
        $this->get('/operator/dashboard')->assertRedirect('/login');
    }
}
