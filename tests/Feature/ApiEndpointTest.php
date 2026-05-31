<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Proposal;
use App\Models\RuangKontrol;
use App\Helpers\TahunAjaranHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_get_mahasiswa_by_nim_found()
    {
        $mhs = User::factory()->create([
            'role' => 'mahasiswa',
            'identifier' => '2500088001',
            'name' => 'Test Mahasiswa',
            'metadata' => ['prodi_name' => 'TI', 'fakultas_name' => 'FT'],
        ]);

        $response = $this->getJson('/api/mahasiswa/by-nim/2500088001');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => ['nama' => 'Test Mahasiswa', 'nim' => '2500088001'],
            ]);
    }

    public function test_api_get_mahasiswa_by_nim_not_found()
    {
        $response = $this->getJson('/api/mahasiswa/by-nim/9999999999');
        $response->assertStatus(404)->assertJson(['success' => false]);
    }

    public function test_api_check_proposal_not_enrolled()
    {
        User::factory()->create([
            'role' => 'mahasiswa',
            'identifier' => '2500088002',
        ]);

        $response = $this->getJson('/api/mahasiswa/check-proposal/2500088002');

        $response->assertStatus(200)->assertJson([
            'success' => true,
            'hasProposal' => false,
        ]);
    }

    public function test_api_check_proposal_already_enrolled()
    {
        // Create proper related users first to avoid FK constraint
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa', 'identifier' => '2500088003']);
        $dosen = User::factory()->create(['role' => 'dosen']);
        $reviewer = User::factory()->create(['role' => 'reviewer']);

        Proposal::factory()->create([
            'ketua_nim' => '2500088003',
            'id_mahasiswa' => $mahasiswa->id,
            'id_dosen' => $dosen->id,
            'id_reviewer_administratif' => $reviewer->id,
            'id_reviewer_substantif_1' => $reviewer->id,
            'id_reviewer_substantif_2' => $reviewer->id,
        ]);

        $response = $this->getJson('/api/mahasiswa/check-proposal/2500088003');

        $response->assertStatus(200)->assertJson([
            'success' => true,
            'hasProposal' => true,
        ]);
    }

    public function test_api_get_ruang_kontrol_status()
    {
        RuangKontrol::factory()->pendaftaranTerbuka()->create([
            'tahun_ajaran' => TahunAjaranHelper::getTahunAjaranTerbaru(),
        ]);

        $response = $this->getJson('/api/ruang-kontrol/status');

        $response->assertStatus(200)->assertJson([
            'success' => true,
            'data' => ['status_pendaftaran' => 'terbuka'],
        ]);
    }

    public function test_api_get_ruang_kontrol_status_no_data()
    {
        $response = $this->getJson('/api/ruang-kontrol/status');

        $response->assertStatus(200)->assertJson([
            'success' => true,
            'data' => ['status_pendaftaran' => 'tertutup'],
        ]);
    }

    public function test_api_proposal_revisi_returns_data()
    {
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);
        $dosen = User::factory()->create(['role' => 'dosen']);
        $reviewer = User::factory()->create(['role' => 'reviewer']);

        $proposal = Proposal::factory()->create([
            'id_mahasiswa' => $mahasiswa->id,
            'id_dosen' => $dosen->id,
            'id_reviewer_administratif' => $reviewer->id,
            'id_reviewer_substantif_1' => $reviewer->id,
            'id_reviewer_substantif_2' => $reviewer->id,
        ]);

        $response = $this->getJson("/api/proposal/{$proposal->id_proposal}/revisi");

        $response->assertStatus(200)->assertJson([
            'success' => true,
            'proposal' => ['id_proposal' => $proposal->id_proposal],
        ]);
    }

    public function test_api_proposal_revisi_not_found()
    {
        $response = $this->getJson('/api/proposal/99999/revisi');
        $response->assertStatus(500);
    }
}
