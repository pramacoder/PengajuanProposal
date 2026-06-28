<?php

namespace Tests\Unit\Helpers;

use Tests\TestCase;
use App\Helpers\TahunAjaranHelper;
use Carbon\Carbon;

class TahunAjaranHelperTest extends TestCase
{
    public function test_get_tahun_ajaran_terbaru_returns_correct_format()
    {
        $result = TahunAjaranHelper::getTahunAjaranTerbaru();

        // Must match YYYY/YYYY format
        $this->assertMatchesRegularExpression('/^\d{4}\/\d{4}$/', $result);

        // Second year should be first year + 1
        [$first, $second] = explode('/', $result);
        $this->assertEquals((int) $first + 1, (int) $second);
    }

    public function test_get_tahun_ajaran_by_year()
    {
        $this->assertEquals('2025/2026', TahunAjaranHelper::getTahunAjaranByYear(2025));
        $this->assertEquals('2024/2025', TahunAjaranHelper::getTahunAjaranByYear(2024));
        $this->assertEquals('2020/2021', TahunAjaranHelper::getTahunAjaranByYear(2020));
    }

    public function test_is_tahun_ajaran_terbaru()
    {
        $current = TahunAjaranHelper::getTahunAjaranTerbaru();
        $this->assertTrue(TahunAjaranHelper::isTahunAjaranTerbaru($current));
        $this->assertFalse(TahunAjaranHelper::isTahunAjaranTerbaru('2019/2020'));
    }

    public function test_get_list_tahun_ajaran()
    {
        $list = TahunAjaranHelper::getListTahunAjaran(2023, 2025);

        $this->assertIsArray($list);
        $this->assertNotEmpty($list);

        // Should be reversed (newest first)
        $this->assertStringContainsString('2025', $list[0]);
        $this->assertStringContainsString('2023', end($list));
    }

    public function test_get_tahun_ajaran_by_date()
    {
        // August 2025 → 2025/2026 (Jul-Dec = current/next)
        $this->assertEquals('2025/2026', TahunAjaranHelper::getTahunAjaranByDate('2025-08-15'));

        // March 2026 → 2025/2026 (Jan-Jun = prev/current)
        $this->assertEquals('2025/2026', TahunAjaranHelper::getTahunAjaranByDate('2026-03-15'));

        // Carbon instance
        $this->assertEquals('2025/2026', TahunAjaranHelper::getTahunAjaranByDate(Carbon::create(2025, 9, 1)));
    }
}
