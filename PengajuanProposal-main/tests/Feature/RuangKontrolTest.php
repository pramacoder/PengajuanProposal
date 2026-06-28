<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\RuangKontrol;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RuangKontrolTest extends TestCase
{
    use RefreshDatabase;

    public function test_operator_can_access_ruang_kontrol()
    {
        $operator = User::factory()->create(['role' => 'operator', 'identifier' => 'OP001']);

        $response = $this->actingAs($operator)
            ->get('/operator/ruang-kontrol');

        $response->assertStatus(200);
        $response->assertViewIs('operator.ruang_kontrol');
    }

    public function test_operator_can_update_ruang_kontrol_status()
    {
        $operator = User::factory()->create(['role' => 'operator', 'identifier' => 'OP002']);
        // Buat RuangKontrol dengan id_pt milik operator ini
        $ruangKontrol = RuangKontrol::factory()->create([
            'status_pendaftaran' => 'tertutup',
            'status_perbaikan'   => 'tertutup',
            'id_pt'              => $operator->id,
        ]);

        // Gunakan tanggal dinamis (future) agar lolos validasi "masa lalu"
        $base = now();
        $response = $this->actingAs($operator)
            ->postJson('/operator/update-ruang-kontrol', [
                'id_ruang_kontrol'                => $ruangKontrol->id_ruang_kontrol,
                'status_pendaftaran'              => 'terbuka',
                'status_perbaikan'                => 'tertutup',
                'tanggal_pendaftaran_mulai'       => $base->copy()->format('Y-m-d'),
                'tanggal_pendaftaran_selesai'     => $base->copy()->addMonths(1)->format('Y-m-d'),
                'tanggal_review_mulai'            => $base->copy()->addMonths(2)->format('Y-m-d'),
                'tanggal_review_selesai'          => $base->copy()->addMonths(3)->format('Y-m-d'),
                'tanggal_perbaikan_mulai'         => $base->copy()->addMonths(4)->format('Y-m-d'),
                'tanggal_perbaikan_selesai'       => $base->copy()->addMonths(5)->format('Y-m-d'),
                'tanggal_penilaian_akhir_mulai'   => $base->copy()->addMonths(6)->format('Y-m-d'),
                'tanggal_penilaian_akhir_selesai' => $base->copy()->addMonths(7)->format('Y-m-d'),
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('ruang_kontrols', [
            'id_ruang_kontrol'   => $ruangKontrol->id_ruang_kontrol,
            'status_pendaftaran' => 'terbuka',
            'status_perbaikan'   => 'tertutup',
        ]);
    }

    public function test_mahasiswa_cannot_access_proposal_create_when_pendaftaran_closed()
    {
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa', 'identifier' => '2501234567']);
        RuangKontrol::factory()->create([
            'status_pendaftaran' => 'tertutup'
        ]);

        $response = $this->actingAs($mahasiswa)
            ->get('/mahasiswa/proposal/create');

        $response->assertStatus(302);
        // CheckActivePhase middleware meredirect ke URL sebelumnya (fallback ke /)
        $response->assertRedirect();
    }

    public function test_mahasiswa_can_access_proposal_create_when_pendaftaran_open()
    {
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa', 'identifier' => '2501234568']);
        RuangKontrol::factory()->create([
            'status_pendaftaran'          => 'terbuka',
            'tanggal_pendaftaran_mulai'   => now()->subDays(1),
            'tanggal_pendaftaran_selesai' => now()->addYear(),
        ]);

        $response = $this->actingAs($mahasiswa)
            ->get('/mahasiswa/proposal/create');

        $response->assertStatus(200);
        $response->assertViewIs('mahasiswa.ajukanproposal');
    }

    public function test_api_returns_ruang_kontrol_status()
    {
        RuangKontrol::factory()->create([
            'status_pendaftaran' => 'terbuka',
            'status_perbaikan'   => 'tertutup'
        ]);

        $response = $this->getJson('/api/ruang-kontrol/status');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data'    => [
                'status_pendaftaran' => 'terbuka',
                'status_perbaikan'   => 'tertutup'
            ]
        ]);
    }
}
