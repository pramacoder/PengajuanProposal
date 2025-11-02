<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\RuangKontrol;
use App\Helpers\TahunAjaranHelper;
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

        // Ambil ruang kontrol aktif untuk tahun akademik terbaru
        $tahunAjaranTerbaru = TahunAjaranHelper::getTahunAjaranTerbaru();
        $ruangKontrol = RuangKontrol::where('tahun_ajaran', $tahunAjaranTerbaru)
            ->where('is_active', true)
            ->first();
        
        // Fallback: jika tidak ada yang aktif, ambil yang pertama untuk tahun ajaran terbaru
        if (!$ruangKontrol) {
            $ruangKontrol = RuangKontrol::where('tahun_ajaran', $tahunAjaranTerbaru)
                ->orderBy('created_at', 'desc')
                ->first();
        }
        
        if (!$ruangKontrol) {
            // Jika tidak ada ruang kontrol, buat default tertutup untuk tahun ajaran terbaru
            $ruangKontrol = RuangKontrol::create([
                'status_pendaftaran' => 'tertutup',
                'status_perbaikan' => 'tertutup',
                'tahun_ajaran' => $tahunAjaranTerbaru,
                'nama_history' => 'Jadwal ' . $tahunAjaranTerbaru,
                'is_active' => true,
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
