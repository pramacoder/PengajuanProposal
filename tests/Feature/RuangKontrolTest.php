<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\RuangKontrol;
use App\Models\Mahasiswa;
use App\Models\PT;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RuangKontrolTest extends TestCase
{
    use RefreshDatabase;

    public function test_operator_can_access_ruang_kontrol()
    {
        $operator = PT::factory()->create();
        
        $response = $this->actingAs($operator, 'operator')
            ->get('/operator/ruang-kontrol');
        
        $response->assertStatus(200);
        $response->assertViewIs('operator.ruang_kontrol');
    }

    public function test_operator_can_update_ruang_kontrol_status()
    {
        $operator = PT::factory()->create();
        $ruangKontrol = RuangKontrol::factory()->create([
            'status_pendaftaran' => 'tertutup',
            'status_perbaikan' => 'tertutup'
        ]);

        $response = $this->actingAs($operator, 'operator')
            ->postJson('/operator/update-ruang-kontrol', [
                'status_pendaftaran' => 'terbuka',
                'status_perbaikan' => 'tertutup',
                'tanggal_pendaftaran_mulai' => '2025-01-01',
                'tanggal_pendaftaran_selesai' => '2025-12-31',
                'tanggal_perbaikan_mulai' => '2025-01-01',
                'tanggal_perbaikan_selesai' => '2025-12-31'
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('ruang_kontrols', [
            'status_pendaftaran' => 'terbuka',
            'status_perbaikan' => 'tertutup'
        ]);
    }

    public function test_mahasiswa_cannot_access_proposal_create_when_pendaftaran_closed()
    {
        $mahasiswa = Mahasiswa::factory()->create();
        RuangKontrol::factory()->create([
            'status_pendaftaran' => 'tertutup'
        ]);

        $response = $this->actingAs($mahasiswa, 'mahasiswa')
            ->get('/mahasiswa/proposal/create');

        $response->assertStatus(302);
        $response->assertRedirect('/mahasiswa/dashboard');
    }

    public function test_mahasiswa_can_access_proposal_create_when_pendaftaran_open()
    {
        $mahasiswa = Mahasiswa::factory()->create();
        RuangKontrol::factory()->create([
            'status_pendaftaran' => 'terbuka',
            'tanggal_pendaftaran_mulai' => '2025-01-01',
            'tanggal_pendaftaran_selesai' => '2025-12-31'
        ]);

        $response = $this->actingAs($mahasiswa, 'mahasiswa')
            ->get('/mahasiswa/proposal/create');

        $response->assertStatus(200);
        $response->assertViewIs('mahasiswa.ajukanproposal');
    }

    public function test_api_returns_ruang_kontrol_status()
    {
        RuangKontrol::factory()->create([
            'status_pendaftaran' => 'terbuka',
            'status_perbaikan' => 'tertutup'
        ]);

        $response = $this->getJson('/api/ruang-kontrol/status');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'status_pendaftaran' => 'terbuka',
                'status_perbaikan' => 'tertutup'
            ]
        ]);
    }
}
