<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\RuangKontrol;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RuangKontrolModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_phases_constant_has_four_phases()
    {
        $this->assertCount(4, RuangKontrol::PHASES);
        $this->assertContains('pendaftaran', RuangKontrol::PHASES);
        $this->assertContains('review', RuangKontrol::PHASES);
        $this->assertContains('perbaikan', RuangKontrol::PHASES);
        $this->assertContains('penilaian_akhir', RuangKontrol::PHASES);
    }

    public function test_phase_labels_defined_for_all_phases()
    {
        foreach (RuangKontrol::PHASES as $phase) {
            $this->assertArrayHasKey($phase, RuangKontrol::PHASE_LABELS);
            $this->assertNotEmpty(RuangKontrol::PHASE_LABELS[$phase]);
        }
    }

    public function test_get_status_for_phase()
    {
        $rk = RuangKontrol::factory()->create([
            'status_pendaftaran' => 'terbuka',
            'status_review' => 'tertutup',
            'status_perbaikan' => 'terbuka',
            'status_penilaian_akhir' => 'tertutup',
        ]);

        $this->assertEquals('terbuka', $rk->getStatusForPhase('pendaftaran'));
        $this->assertEquals('tertutup', $rk->getStatusForPhase('review'));
        $this->assertEquals('terbuka', $rk->getStatusForPhase('perbaikan'));
        $this->assertEquals('tertutup', $rk->getStatusForPhase('penilaian_akhir'));
        $this->assertEquals('tertutup', $rk->getStatusForPhase('unknown_phase'));
    }

    public function test_get_active_phase_returns_first_open()
    {
        $rk = RuangKontrol::factory()->create([
            'status_pendaftaran' => 'tertutup',
            'status_review' => 'terbuka',
            'status_perbaikan' => 'tertutup',
            'status_penilaian_akhir' => 'tertutup',
        ]);

        $this->assertEquals('review', $rk->getActivePhase());
    }

    public function test_no_active_phase_when_all_closed()
    {
        $rk = RuangKontrol::factory()->semuaTertutup()->create();

        $this->assertNull($rk->getActivePhase());
    }

    public function test_get_date_range()
    {
        $rk = RuangKontrol::factory()->create();

        [$start, $end] = $rk->getDateRange('pendaftaran');
        $this->assertNotNull($start);
        $this->assertNotNull($end);

        [$unknownStart, $unknownEnd] = $rk->getDateRange('nonexistent');
        $this->assertNull($unknownStart);
        $this->assertNull($unknownEnd);
    }
}
