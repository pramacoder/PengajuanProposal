<?php

namespace App\Helpers;

class TahunAjaranHelper
{
    /**
     * Get tahun ajaran terbaru berdasarkan tahun saat ini
     */
    public static function getTahunAjaranTerbaru()
    {
        $currentYear = date('Y');
        $currentMonth = date('n');
        
        // Jika bulan Januari-Juni, tahun ajaran adalah tahun sebelumnya/tahun sekarang
        // Jika bulan Juli-Desember, tahun ajaran adalah tahun sekarang/tahun berikutnya
        if ($currentMonth >= 7) {
            return $currentYear . '/' . ($currentYear + 1);
        } else {
            return ($currentYear - 1) . '/' . $currentYear;
        }
    }
    
    /**
     * Get tahun ajaran berdasarkan tahun tertentu
     */
    public static function getTahunAjaranByYear($year)
    {
        return $year . '/' . ($year + 1);
    }
    
    /**
     * Check apakah tahun ajaran adalah tahun terbaru
     */
    public static function isTahunAjaranTerbaru($tahunAjaran)
    {
        return $tahunAjaran === self::getTahunAjaranTerbaru();
    }
    
    /**
     * Get list tahun ajaran yang tersedia
     */
    public static function getListTahunAjaran($startYear = 2020, $endYear = null)
    {
        if ($endYear === null) {
            $endYear = date('Y') + 1;
        }
        
        $list = [];
        for ($year = $startYear; $year <= $endYear; $year++) {
            $list[] = $year . '/' . ($year + 1);
        }
        
        return array_reverse($list); // Urutkan dari terbaru ke terlama
    }
}

