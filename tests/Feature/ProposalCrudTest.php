<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Proposal;
use App\Models\RuangKontrol;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProposalCrudTest extends TestCase
{
    use RefreshDatabase;

    private function createMahasiswaWithProposal(string $status = 'pending'): array
    {
        $mahasiswa = User::factory()->create([
            'role' => 'mahasiswa',
            'identifier' => '2500001099',
        ]);
        $dosen = User::factory()->create(['role' => 'dosen']);
        $reviewer = User::factory()->create(['role' => 'reviewer']);

        RuangKontrol::factory()->pendaftaranTerbuka()->create();

        $proposal = Proposal::factory()->create([
            'id_mahasiswa' => $mahasiswa->id,
            'id_dosen' => $dosen->id,
            'id_reviewer_administratif' => $reviewer->id,
            'id_reviewer_substantif_1' => $reviewer->id,
            'id_reviewer_substantif_2' => $reviewer->id,
            'status' => $status,
            'ketua_nim' => $mahasiswa->identifier,
        ]);

        return [$mahasiswa, $proposal];
    }

    // =========================================================
    // Dashboard
    // =========================================================

    public function test_mahasiswa_can_view_dashboard()
    {
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);
        $response = $this->actingAs($mahasiswa)->get('/mahasiswa/dashboard');
        $response->assertStatus(200);
    }

    // =========================================================
    // Proposal List
    // =========================================================

    public function test_mahasiswa_can_view_proposal_list()
    {
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);
        $response = $this->actingAs($mahasiswa)->get('/mahasiswa/proposal');
        $response->assertStatus(200);
    }

    // =========================================================
    // Create Proposal
    // =========================================================

    public function test_mahasiswa_can_view_create_form_when_pendaftaran_open()
    {
        $mahasiswa = User::factory()->create([
            'role' => 'mahasiswa',
            'identifier' => '2500002001',
        ]);
        RuangKontrol::factory()->pendaftaranTerbuka()->create();

        $response = $this->actingAs($mahasiswa)->get('/mahasiswa/proposal/create');
        $response->assertStatus(200);
    }

    public function test_mahasiswa_cannot_create_when_pendaftaran_closed()
    {
        $mahasiswa = User::factory()->create([
            'role' => 'mahasiswa',
            'identifier' => '2500002002',
        ]);
        RuangKontrol::factory()->semuaTertutup()->create();

        $response = $this->actingAs($mahasiswa)->get('/mahasiswa/proposal/create');
        $response->assertStatus(302);
    }

    // =========================================================
    // View Detail
    // =========================================================

    public function test_mahasiswa_can_view_own_proposal_detail()
    {
        [$mahasiswa, $proposal] = $this->createMahasiswaWithProposal();

        $response = $this->actingAs($mahasiswa)
            ->get("/mahasiswa/proposal/{$proposal->id_proposal}");
        $response->assertStatus(200);
    }

    // =========================================================
    // Business Rules
    // =========================================================

    public function test_proposal_can_be_edited_checks()
    {
        $proposal = new Proposal(['status' => 'draft']);
        $this->assertTrue($proposal->canBeEdited());

        $proposal->status = 'lolos';
        $this->assertFalse($proposal->canBeEdited());
    }

    public function test_proposal_can_be_deleted_checks()
    {
        $proposal = new Proposal(['status' => 'draft']);
        $this->assertTrue($proposal->canBeDeleted());

        $proposal->status = 'pending';
        $this->assertFalse($proposal->canBeDeleted());
    }

    // =========================================================
    // Role Guards
    // =========================================================

    public function test_dosen_cannot_access_mahasiswa_proposal_routes()
    {
        $dosen = User::factory()->create(['role' => 'dosen']);
        $this->actingAs($dosen)->get('/mahasiswa/proposal')->assertStatus(403);
    }

    public function test_reviewer_cannot_access_mahasiswa_proposal_routes()
    {
        $reviewer = User::factory()->create(['role' => 'reviewer']);
        $this->actingAs($reviewer)->get('/mahasiswa/proposal')->assertStatus(403);
    }

    public function test_guest_cannot_access_proposal_routes()
    {
        $this->get('/mahasiswa/proposal')->assertRedirect('/login');
    }
}
