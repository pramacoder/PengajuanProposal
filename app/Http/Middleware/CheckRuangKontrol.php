<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\RuangKontrol;
use App\Helpers\TahunAjaranHelper;

class CheckRuangKontrol
{
    public function handle(Request $request, Closure $next, $type = 'pendaftaran')
    {
        $user = auth()->user();

        if (!$user || !$user->isMahasiswa()) {
            return $next($request);
        }

        $tahunAjaranTerbaru = TahunAjaranHelper::getTahunAjaranTerbaru();
        $ruangKontrol = RuangKontrol::where('tahun_ajaran', $tahunAjaranTerbaru)
            ->where('is_active', true)
            ->first();

        if (!$ruangKontrol) {
            $ruangKontrol = RuangKontrol::where('tahun_ajaran', $tahunAjaranTerbaru)
                ->orderBy('created_at', 'desc')
                ->first();
        }

        if (!$ruangKontrol) {
            $ruangKontrol = RuangKontrol::create([
                'status_pendaftaran' => 'tertutup',
                'status_perbaikan' => 'tertutup',
                'tahun_ajaran' => $tahunAjaranTerbaru,
                'nama_history' => 'Jadwal ' . $tahunAjaranTerbaru,
                'is_active' => true,
                'id_pt' => $user->id,
            ]);
        }

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

            return redirect()->route('mahasiswa.dashboard')
                ->with('error', 'Sistem pendaftaran sedang ditutup. Silakan cek kembali nanti.');
        }

        return $next($request);
    }
}
