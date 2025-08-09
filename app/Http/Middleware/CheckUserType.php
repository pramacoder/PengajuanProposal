<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserType
{
    public function handle(Request $request, Closure $next, ...$userTypes)
    {
        // Cek apakah user sudah login di salah satu guard
        $isLoggedIn = false;
        $currentUserType = null;

        if (Auth::guard('mahasiswa')->check()) {
            $isLoggedIn = true;
            $currentUserType = 'mahasiswa';
        } elseif (Auth::guard('dosen')->check()) {
            $isLoggedIn = true;
            $currentUserType = 'dosen';
        } elseif (Auth::guard('reviewer')->check()) {
            $isLoggedIn = true;
            $currentUserType = 'reviewer';
        } elseif (Auth::guard('operator')->check()) {
            $isLoggedIn = true;
            $currentUserType = 'operator';
        }

        if (!$isLoggedIn) {
            return redirect('/login');
        }

        // Cek apakah user type yang login diizinkan mengakses route ini
        if (!in_array($currentUserType, $userTypes)) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
} 