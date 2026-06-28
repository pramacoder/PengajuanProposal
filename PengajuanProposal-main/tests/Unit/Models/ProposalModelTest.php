<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Proposal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProposalModelTest extends TestCase
{
    use RefreshDatabase;

    // =========================================================
    // Primary Key
    // =========================================================

    public function test_proposal_has_correct_primary_key()
    {
        $proposal = new Proposal();
        $this->assertEquals('id_proposal', $proposal->getKeyName());
    }

    // =========================================================
    // Accessors
    // =========================================================

    public function test_status_label_accessor_returns_known_labels()
    {
        $proposal = new Proposal(['status' => 'pending']);
        $this->assertEquals('Menunggu', $proposal->status_label);

        $proposal->status = 'valid';
        $this->assertEquals('Valid', $proposal->status_label);

        $proposal->status = 'lolos_tingkat_universitas';
        $this->assertEquals('Lolos Tingkat Universitas', $proposal->status_label);
    }

    public function test_status_label_accessor_handles_unknown_status()
    {
        $proposal = new Proposal(['status' => 'custom_status']);
        $this->assertEquals('Custom status', $proposal->status_label);
    }

    public function test_skim_label_accessor()
    {
        $proposal = new Proposal(['skim' => 'RE']);
        $this->assertEquals('PKM-RE (Riset Eksak)', $proposal->skim_label);

        $proposal->skim = 'PM';
        $this->assertEquals('PKM-PM (Pengabdian Masyarakat)', $proposal->skim_label);

        $proposal->skim = 'UNKNOWN';
        $this->assertEquals('UNKNOWN', $proposal->skim_label);
    }

    public function test_dana_formatted_accessor()
    {
        $proposal = new Proposal(['dana_diajukan' => 15000000]);
        $this->assertEquals('Rp 15.000.000', $proposal->dana_formatted);
    }

    public function test_dana_formatted_when_null()
    {
        $proposal = new Proposal(['dana_diajukan' => null]);
        $this->assertEquals('Rp 0', $proposal->dana_formatted);
    }

    // =========================================================
    // Team Size
    // =========================================================

    public function test_team_size_accessor_counts_correctly()
    {
        // Ketua only
        $proposal = new Proposal([
            'ketua_nim' => '123',
        ]);
        $this->assertEquals(1, $proposal->team_size);

        // Ketua + 2 anggota
        $proposal = new Proposal([
            'ketua_nim' => '123',
            'anggota1_nim' => '456',
            'anggota2_nim' => '789',
        ]);
        $this->assertEquals(3, $proposal->team_size);
    }

    public function test_is_team_complete()
    {
        $proposal = new Proposal([
            'ketua_nim' => '1',
            'anggota1_nim' => '2',
            'anggota2_nim' => '3',
        ]);
        $this->assertTrue($proposal->isTeamComplete()); // 3 >= 3

        $proposal2 = new Proposal([
            'ketua_nim' => '1',
            'anggota1_nim' => '2',
        ]);
        $this->assertFalse($proposal2->isTeamComplete()); // 2 < 3
    }

    public function test_is_team_full()
    {
        $proposal = new Proposal([
            'ketua_nim' => '1',
            'anggota1_nim' => '2',
            'anggota2_nim' => '3',
            'anggota3_nim' => '4',
            'anggota4_nim' => '5',
        ]);
        $this->assertTrue($proposal->isTeamFull()); // 5 >= 5
    }

    // =========================================================
    // Business Logic
    // =========================================================

    public function test_can_be_edited_logic()
    {
        $proposal = new Proposal(['status' => 'draft']);
        $this->assertTrue($proposal->canBeEdited());

        $proposal->status = 'pending';
        $this->assertTrue($proposal->canBeEdited());

        $proposal->status = 'submitted';
        $this->assertFalse($proposal->canBeEdited());

        $proposal->status = 'lolos';
        $this->assertFalse($proposal->canBeEdited());
    }

    public function test_can_be_deleted_logic()
    {
        $proposal = new Proposal(['status' => 'draft']);
        $this->assertTrue($proposal->canBeDeleted());

        $proposal->status = 'pending';
        $this->assertFalse($proposal->canBeDeleted());

        $proposal->status = 'submitted';
        $this->assertFalse($proposal->canBeDeleted());
    }

    // =========================================================
    // Relationships (definition check — no DB needed)
    // =========================================================

    public function test_relationships_are_defined()
    {
        $proposal = new Proposal();

        // Check that relationship methods exist and return correct types
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $proposal->mahasiswa());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $proposal->dosen());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $proposal->dosenPendampingUniversitas());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $proposal->reviewerAdministratif());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasOne::class, $proposal->dokumen());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $proposal->nilaiAdministratif());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $proposal->nilaiSubstantif());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasOne::class, $proposal->hasilSemiFinal());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasOne::class, $proposal->hasilFinal());
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class, $proposal->proposalRevisi());
    }
}
