<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class UserHelper
{
    /**
     * Mendeteksi guard yang sedang aktif
     */
    public static function getCurrentGuard()
    {
        if (Auth::guard('mahasiswa')->check()) {
            return 'mahasiswa';
        } elseif (Auth::guard('dosen')->check()) {
            return 'dosen';
        } elseif (Auth::guard('reviewer')->check()) {
            return 'reviewer';
        } elseif (Auth::guard('operator')->check()) {
            return 'operator';
        }
        
        return null;
    }

    /**
     * Mendapatkan user yang sedang login
     */
    public static function getCurrentUser()
    {
        $guard = self::getCurrentGuard();
        
        if ($guard) {
            return Auth::guard($guard)->user();
        }
        
        return Auth::user();
    }

    /**
     * Mendapatkan nama user yang sedang login
     */
    public static function getCurrentUserName()
    {
        $user = self::getCurrentUser();
        $guard = self::getCurrentGuard();
        
        if (!$user) {
            return 'Pengguna';
        }
        
        switch ($guard) {
            case 'mahasiswa':
                return $user->nama_mhs ?? 'Mahasiswa';
            case 'dosen':
                return $user->nama_dosen ?? 'Dosen';
            case 'reviewer':
                return $user->nama_reviewer ?? 'Reviewer';
            case 'operator':
                return $user->nama_pt ?? 'Operator';
            default:
                return $user->name ?? 'Pengguna';
        }
    }

    /**
     * Mendapatkan role user yang sedang login
     */
    public static function getCurrentUserRole()
    {
        $user = self::getCurrentUser();
        
        if (!$user) {
            return 'Pengguna';
        }
        
        return $user->role ?? 'Pengguna';
    }

    /**
     * Mendapatkan email user yang sedang login
     */
    public static function getCurrentUserEmail()
    {
        $user = self::getCurrentUser();
        $guard = self::getCurrentGuard();
        
        if (!$user) {
            return '';
        }
        
        switch ($guard) {
            case 'mahasiswa':
                return $user->email_mhs ?? '';
            case 'dosen':
                return $user->email_dosen ?? '';
            case 'reviewer':
                return $user->email_reviewer ?? '';
            case 'operator':
                return $user->email_pt ?? '';
            default:
                return $user->email ?? '';
        }
    }

    /**
     * Mendapatkan informasi lengkap user untuk ditampilkan di profile
     */
    public static function getUserProfileInfo()
    {
        $user = self::getCurrentUser();
        $guard = self::getCurrentGuard();
        
        if (!$user) {
            return [
                'name' => 'Pengguna',
                'role' => 'Pengguna Sistem',
                'email' => '',
                'phone' => '',
                'additional_info' => []
            ];
        }
        
        switch ($guard) {
            case 'mahasiswa':
                return [
                    'name' => $user->nama_mhs ?? 'Mahasiswa',
                    'role' => 'Mahasiswa',
                    'email' => $user->email_mhs ?? '',
                    'phone' => $user->no_hp_mhs ?? '',
                    'additional_info' => [
                        'NIM' => $user->nim ?? '',
                        'Program Studi' => $user->prodi_mhs ?? '',
                        'Fakultas' => $user->fakultas_mhs ?? ''
                    ]
                ];
                
            case 'dosen':
                $gelarDepan = $user->gelar_depan ? $user->gelar_depan . ' ' : '';
                $gelarBelakang = $user->gelar_belakang ? ', ' . $user->gelar_belakang : '';
                return [
                    'name' => $gelarDepan . ($user->nama_dosen ?? 'Dosen') . $gelarBelakang,
                    'role' => $user->role ?? 'Dosen',
                    'email' => $user->email_dosen ?? '',
                    'phone' => $user->no_hp_dosen ?? '',
                    'additional_info' => [
                        'NUPTK' => $user->nuptk ?? '',
                        'Role' => $user->role ?? ''
                    ]
                ];
                
            case 'reviewer':
                return [
                    'name' => $user->nama_reviewer ?? 'Reviewer',
                    'role' => $user->role ?? 'Reviewer',
                    'email' => $user->email_reviewer ?? '',
                    'phone' => $user->no_hp_reviewer ?? '',
                    'additional_info' => [
                        'Role' => $user->role ?? ''
                    ]
                ];
                
            case 'operator':
                return [
                    'name' => $user->nama_pt ?? 'Operator',
                    'role' => $user->role ?? 'Operator',
                    'email' => $user->email_pt ?? '',
                    'phone' => $user->no_hp_pt ?? '',
                    'additional_info' => [
                        'Role' => $user->role ?? ''
                    ]
                ];
                
            default:
                return [
                    'name' => $user->name ?? 'Pengguna',
                    'role' => 'Pengguna Sistem',
                    'email' => $user->email ?? '',
                    'phone' => '',
                    'additional_info' => []
                ];
        }
    }

    /**
     * Mendapatkan icon yang sesuai untuk user type
     */
    public static function getUserIcon()
    {
        $guard = self::getCurrentGuard();
        
        switch ($guard) {
            case 'mahasiswa':
                return 'fas fa-user-graduate';
            case 'dosen':
                return 'fas fa-chalkboard-teacher';
            case 'reviewer':
                return 'fas fa-search';
            case 'operator':
                return 'fas fa-cogs';
            default:
                return 'fas fa-user';
        }
    }

    /**
     * Mendapatkan warna avatar yang sesuai untuk user type
     */
    public static function getUserAvatarColor()
    {
        $guard = self::getCurrentGuard();
        
        switch ($guard) {
            case 'mahasiswa':
                return 'primary';
            case 'dosen':
                return 'success';
            case 'reviewer':
                return 'info';
            case 'operator':
                return 'warning';
            default:
                return 'secondary';
        }
    }
}

