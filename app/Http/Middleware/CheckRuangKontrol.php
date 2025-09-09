<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\RuangKontrol;
use Illuminate\Support\Facades\Auth;

class CheckRuangKontrol
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Closure): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $type = 'pendaftaran')
    {
        // Hanya berlaku untuk mahasiswa
        if (!Auth::guard('mahasiswa')->check()) {
            return $next($request);
        }

        // Ambil status ruang kontrol
        $ruangKontrol = RuangKontrol::first();
        
        if (!$ruangKontrol) {
            // Jika tidak ada ruang kontrol, buat default tertutup
            $ruangKontrol = RuangKontrol::create([
                'status_pendaftaran' => 'tertutup',
                'status_perbaikan' => 'tertutup',
                'id_pt' => 1 // Default PT ID
            ]);
        }

        // Cek status berdasarkan type yang diminta
        $statusField = 'status_' . $type;
        $status = $ruangKontrol->$statusField ?? 'tertutup';

        if ($status === 'tertutup') {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sistem pendaftaran sedang ditutup. Silakan cek kembali nanti.',
                    'status' => 'tertutup'
                ], 403);
            }

            // Redirect ke halaman dengan pesan error
            return redirect()->route('mahasiswa.dashboard')
                ->with('error', 'Sistem pendaftaran sedang ditutup. Silakan cek kembali nanti.');
        }

        // Jika terbuka, lanjutkan request
        return $next($request);
    }
}
