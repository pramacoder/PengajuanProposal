<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class UserHelper
{
    public static function getCurrentGuard()
    {
        $user = Auth::user();
        return $user ? $user->role : null;
    }

    public static function getCurrentUser()
    {
        return Auth::user();
    }

    public static function getCurrentUserName()
    {
        $user = Auth::user();
        return $user ? $user->name : 'Pengguna';
    }

    public static function getCurrentUserRole()
    {
        $user = Auth::user();
        return $user ? ($user->role ?? 'Pengguna') : 'Pengguna';
    }

    public static function getCurrentUserEmail()
    {
        $user = Auth::user();
        return $user ? $user->email : '';
    }

    public static function getUserProfileInfo()
    {
        $user = Auth::user();

        if (!$user) {
            return [
                'name' => 'Pengguna',
                'role' => 'Pengguna Sistem',
                'email' => '',
                'phone' => '',
                'additional_info' => []
            ];
        }

        $base = [
            'name' => $user->name,
            'role' => ucfirst(str_replace('_', ' ', $user->role)),
            'email' => $user->email,
            'phone' => $user->phone ?? '',
            'additional_info' => []
        ];

        switch ($user->role) {
            case 'mahasiswa':
                $base['additional_info'] = [
                    'NIM' => $user->identifier ?? '',
                    'Program Studi' => $user->getProdiName() ?? '',
                    'Fakultas' => $user->getFakultasName() ?? '',
                ];
                break;

            case 'dosen':
                $base['name'] = $user->getFullNameWithTitle();
                $base['additional_info'] = [
                    'NUPTK' => $user->getNuptk() ?? '',
                ];
                break;

            case 'reviewer':
                $base['additional_info'] = [
                    'ID' => $user->identifier ?? '',
                ];
                break;

            case 'operator':
            case 'pimpinan_pt':
                $base['additional_info'] = [
                    'Role' => ucfirst(str_replace('_', ' ', $user->role)),
                ];
                break;
        }

        return $base;
    }

    public static function getUserIcon()
    {
        $user = Auth::user();
        $role = $user ? $user->role : null;

        $icons = [
            'mahasiswa' => 'fas fa-user-graduate',
            'dosen' => 'fas fa-chalkboard-teacher',
            'reviewer' => 'fas fa-search',
            'operator' => 'fas fa-cogs',
            'pimpinan_pt' => 'fas fa-building',
        ];

        return $icons[$role] ?? 'fas fa-user';
    }

    public static function getUserAvatarColor()
    {
        $user = Auth::user();
        $role = $user ? $user->role : null;

        $colors = [
            'mahasiswa' => 'primary',
            'dosen' => 'success',
            'reviewer' => 'info',
            'operator' => 'warning',
            'pimpinan_pt' => 'danger',
        ];

        return $colors[$role] ?? 'secondary';
    }
}
