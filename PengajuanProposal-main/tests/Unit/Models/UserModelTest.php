<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    // =========================================================
    // Fillable & Casts
    // =========================================================

    public function test_user_has_correct_fillable_fields()
    {
        $user = new User();
        $expected = [
            'identifier', 'name', 'email', 'password',
            'role', 'phone', 'is_active', 'metadata', 'email_verified_at',
        ];

        $this->assertEquals($expected, $user->getFillable());
    }

    public function test_password_is_hidden()
    {
        $user = User::factory()->create(['password' => 'secret123']);
        $array = $user->toArray();

        $this->assertArrayNotHasKey('password', $array);
        $this->assertArrayNotHasKey('remember_token', $array);
    }

    // =========================================================
    // Role Check Methods
    // =========================================================

    public function test_role_check_methods_return_correct_boolean()
    {
        $mahasiswa = User::factory()->create(['role' => 'mahasiswa']);
        $this->assertTrue($mahasiswa->isMahasiswa());
        $this->assertFalse($mahasiswa->isDosen());
        $this->assertFalse($mahasiswa->isReviewer());
        $this->assertFalse($mahasiswa->isOperator());
        $this->assertFalse($mahasiswa->isPimpinanPT());

        $dosen = User::factory()->create(['role' => 'dosen']);
        $this->assertTrue($dosen->isDosen());
        $this->assertFalse($dosen->isMahasiswa());

        $reviewer = User::factory()->create(['role' => 'reviewer']);
        $this->assertTrue($reviewer->isReviewer());

        $operator = User::factory()->create(['role' => 'operator']);
        $this->assertTrue($operator->isOperator());
        $this->assertTrue($operator->isOperatorOrPimpinan());

        $pimpinan = User::factory()->create(['role' => 'pimpinan_pt']);
        $this->assertTrue($pimpinan->isPimpinanPT());
        $this->assertTrue($pimpinan->isOperatorOrPimpinan());
    }

    // =========================================================
    // Metadata Accessors
    // =========================================================

    public function test_metadata_accessor_and_mutator()
    {
        $user = User::factory()->create([
            'role' => 'mahasiswa',
            'metadata' => ['prodi_id' => 1, 'fakultas_id' => 2],
        ]);

        $this->assertEquals(1, $user->getMetadataValue('prodi_id'));
        $this->assertEquals(2, $user->getMetadataValue('fakultas_id'));
        $this->assertNull($user->getMetadataValue('nonexistent'));
        $this->assertEquals('default', $user->getMetadataValue('nonexistent', 'default'));

        $user->setMetadataValue('new_key', 'new_value');
        $this->assertEquals('new_value', $user->getMetadataValue('new_key'));
    }

    public function test_mahasiswa_specific_accessors()
    {
        $user = User::factory()->create([
            'role' => 'mahasiswa',
            'identifier' => '2500001001',
            'metadata' => [
                'prodi_id' => 1,
                'prodi_name' => 'Teknik Informatika',
                'fakultas_id' => 1,
                'fakultas_name' => 'Fakultas Teknik',
                'team_id' => 'TEAM001',
                'is_ketua' => true,
            ],
        ]);

        $this->assertEquals('2500001001', $user->getNim());
        $this->assertEquals(1, $user->getProdiId());
        $this->assertEquals(1, $user->getFakultasId());
        $this->assertEquals('Teknik Informatika', $user->getProdiName());
        $this->assertEquals('Fakultas Teknik', $user->getFakultasName());
        $this->assertEquals('TEAM001', $user->getTeamId());
        $this->assertTrue($user->isKetuaTim());
    }

    public function test_dosen_specific_accessors()
    {
        $user = User::factory()->create([
            'role' => 'dosen',
            'identifier' => 'NIDN001',
            'name' => 'Budi Santoso',
            'metadata' => [
                'nuptk' => '123456',
                'gelar_depan' => 'Dr.',
                'gelar_belakang' => 'M.Kom.',
            ],
        ]);

        $this->assertEquals('NIDN001', $user->getNidn());
        $this->assertEquals('123456', $user->getNuptk());
        $this->assertEquals('Dr.', $user->getGelarDepan());
        $this->assertEquals('M.Kom.', $user->getGelarBelakang());
        $this->assertEquals('Dr. Budi Santoso, M.Kom.', $user->getFullNameWithTitle());
    }

    public function test_dosen_full_name_without_titles()
    {
        $user = User::factory()->create([
            'role' => 'dosen',
            'name' => 'Budi Santoso',
            'metadata' => [],
        ]);

        $this->assertEquals('Budi Santoso', $user->getFullNameWithTitle());
    }

    // =========================================================
    // Backward Compatibility Attributes
    // =========================================================

    public function test_backward_compatibility_attributes()
    {
        $user = User::factory()->create([
            'role' => 'mahasiswa',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'identifier' => '2500001002',
            'phone' => '08123456789',
        ]);

        $this->assertEquals('Test User', $user->nama_mhs);
        $this->assertEquals('2500001002', $user->nim);
        $this->assertEquals('test@example.com', $user->email_mhs);
        $this->assertEquals('08123456789', $user->no_hp_mhs);
        $this->assertEquals($user->id, $user->id_mahasiswa);
    }

    // =========================================================
    // Scopes
    // =========================================================

    public function test_scopes_filter_correctly()
    {
        User::factory()->create(['role' => 'mahasiswa', 'is_active' => true]);
        User::factory()->create(['role' => 'mahasiswa', 'is_active' => false]);
        User::factory()->create(['role' => 'dosen', 'is_active' => true]);
        User::factory()->create(['role' => 'reviewer', 'is_active' => true]);

        $this->assertEquals(2, User::mahasiswa()->count());
        $this->assertEquals(1, User::dosen()->count());
        $this->assertEquals(1, User::reviewer()->count());
        $this->assertEquals(3, User::active()->count());
        $this->assertEquals(2, User::role('mahasiswa')->count());
    }

    // =========================================================
    // Static Finders
    // =========================================================

    public function test_static_finder_methods()
    {
        $user = User::factory()->create([
            'role' => 'mahasiswa',
            'identifier' => '2500099001',
        ]);

        $found = User::findByIdentifier('2500099001');
        $this->assertNotNull($found);
        $this->assertEquals($user->id, $found->id);

        $foundMhs = User::findMahasiswaByNim('2500099001');
        $this->assertNotNull($foundMhs);
        $this->assertEquals($user->id, $foundMhs->id);

        $notFound = User::findMahasiswaByNim('9999999999');
        $this->assertNull($notFound);
    }

    // =========================================================
    // Reviewer/Operator accessor
    // =========================================================

    public function test_reviewer_nip_accessor()
    {
        $reviewer = User::factory()->create([
            'role' => 'reviewer',
            'identifier' => 'RV001',
        ]);

        $this->assertEquals('RV001', $reviewer->getNip());
        $this->assertNull($reviewer->getNim()); // Not mahasiswa
    }

    public function test_mahasiswa_nim_returns_null_for_non_mahasiswa()
    {
        $dosen = User::factory()->create(['role' => 'dosen']);
        $this->assertNull($dosen->getNim());
    }
}
