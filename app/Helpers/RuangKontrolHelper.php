<?php

namespace App\Helpers;

use App\Models\RuangKontrol;

class RuangKontrolHelper
{
    /**
     * Ambil ruang kontrol aktif untuk tahun ajaran terbaru
     */
    private static function getActiveRuangKontrol()
    {
        $tahunAjaranTerbaru = TahunAjaranHelper::getTahunAjaranTerbaru();
        
        // Cari yang aktif untuk tahun ajaran terbaru
        $ruangKontrol = RuangKontrol::where('tahun_ajaran', $tahunAjaranTerbaru)
            ->where('is_active', true)
            ->first();
        
        // Fallback: jika tidak ada yang aktif, ambil yang pertama untuk tahun ajaran terbaru
        if (!$ruangKontrol) {
            $ruangKontrol = RuangKontrol::where('tahun_ajaran', $tahunAjaranTerbaru)
                ->orderBy('created_at', 'desc')
                ->first();
        }
        
        return $ruangKontrol;
    }
    
    /**
     * Cek status pendaftaran
     */
    public static function isPendaftaranOpen()
    {
        $ruangKontrol = self::getActiveRuangKontrol();
        return $ruangKontrol && $ruangKontrol->status_pendaftaran === 'terbuka';
    }

    /**
     * Cek status perbaikan
     */
    public static function isPerbaikanOpen()
    {
        $ruangKontrol = self::getActiveRuangKontrol();
        return $ruangKontrol && $ruangKontrol->status_perbaikan === 'terbuka';
    }

    /**
     * Dapatkan status lengkap ruang kontrol
     */
    public static function getRuangKontrolStatus()
    {
        $ruangKontrol = self::getActiveRuangKontrol();
        
        if (!$ruangKontrol) {
            return [
                'status_pendaftaran' => 'tertutup',
                'status_perbaikan' => 'tertutup',
                'tanggal_pendaftaran_mulai' => null,
                'tanggal_pendaftaran_selesai' => null,
                'tanggal_perbaikan_mulai' => null,
                'tanggal_perbaikan_selesai' => null,
            ];
        }

        return [
            'status_pendaftaran' => $ruangKontrol->status_pendaftaran,
            'status_perbaikan' => $ruangKontrol->status_perbaikan,
            'tanggal_pendaftaran_mulai' => $ruangKontrol->tanggal_pendaftaran_mulai,
            'tanggal_pendaftaran_selesai' => $ruangKontrol->tanggal_pendaftaran_selesai,
            'tanggal_perbaikan_mulai' => $ruangKontrol->tanggal_perbaikan_mulai,
            'tanggal_perbaikan_selesai' => $ruangKontrol->tanggal_perbaikan_selesai,
        ];
    }

    /**
     * Cek apakah tanggal saat ini berada dalam periode yang ditentukan
     */
    public static function isWithinPeriod($startDate, $endDate)
    {
        if (!$startDate || !$endDate) {
            return false;
        }

        $now = now();
        $start = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);

        return $now->between($start, $end);
    }

    /**
     * Cek apakah pendaftaran aktif berdasarkan tanggal dan status
     */
    public static function isPendaftaranActive()
    {
        $ruangKontrol = self::getActiveRuangKontrol();
        
        if (!$ruangKontrol || $ruangKontrol->status_pendaftaran !== 'terbuka') {
            return false;
        }

        return self::isWithinPeriod(
            $ruangKontrol->tanggal_pendaftaran_mulai,
            $ruangKontrol->tanggal_pendaftaran_selesai
        );
    }

    /**
     * Cek apakah perbaikan aktif berdasarkan tanggal dan status
     */
    public static function isPerbaikanActive()
    {
        $ruangKontrol = self::getActiveRuangKontrol();
        
        if (!$ruangKontrol || $ruangKontrol->status_perbaikan !== 'terbuka') {
            return false;
        }

        return self::isWithinPeriod(
            $ruangKontrol->tanggal_perbaikan_mulai,
            $ruangKontrol->tanggal_perbaikan_selesai
        );
    }
}
