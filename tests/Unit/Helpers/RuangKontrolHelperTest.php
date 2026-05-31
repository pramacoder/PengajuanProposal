<?php

namespace Tests\Unit\Helpers;

use Tests\TestCase;
use App\Helpers\RuangKontrolHelper;
use App\Models\RuangKontrol;
use App\Helpers\TahunAjaranHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RuangKontrolHelperTest extends TestCase
{
    use RefreshDatabase;

    public function test_is_pendaftaran_open_returns_true_when_open()
    {
        RuangKontrol::factory()->pendaftaranTerbuka()->create([
            'tahun_ajaran' => TahunAjaranHelper::getTahunAjaranTerbaru(),
        ]);
        $this->assertTrue(RuangKontrolHelper::isPendaftaranOpen());
    }

    public function test_is_pendaftaran_open_returns_false_when_closed()
    {
        RuangKontrol::factory()->semuaTertutup()->create([
            'tahun_ajaran' => TahunAjaranHelper::getTahunAjaranTerbaru(),
        ]);
        $this->assertFalse(RuangKontrolHelper::isPendaftaranOpen());
    }

    public function test_get_ruang_kontrol_status_when_no_data()
    {
        $status = RuangKontrolHelper::getRuangKontrolStatus();
        $this->assertEquals('tertutup', $status['status_pendaftaran']);
        $this->assertEquals('tertutup', $status['status_review']);
        $this->assertNull($status['tanggal_pendaftaran_mulai']);
    }

    public function test_is_within_period()
    {
        $this->assertTrue(RuangKontrolHelper::isWithinPeriod(now()->subDay(), now()->addDay()));
        $this->assertFalse(RuangKontrolHelper::isWithinPeriod(now()->subMonth(), now()->subDay()));
        $this->assertFalse(RuangKontrolHelper::isWithinPeriod(null, null));
    }
}
