<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\RuangKontrol;
use App\Helpers\TahunAjaranHelper;

class CheckActivePhase
{
    public function handle(Request $request, Closure $next, string $requiredPhase)
    {
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
            return $this->denyAccess($request, 'Sistem belum dikonfigurasi. Silakan hubungi operator.');
        }

        $phaseLabels = RuangKontrol::PHASE_LABELS;

        if (!in_array($requiredPhase, RuangKontrol::PHASES)) {
            return $this->denyAccess($request, 'Fase yang diminta tidak valid.');
        }

        $status = $ruangKontrol->getStatusForPhase($requiredPhase);
        $label = $phaseLabels[$requiredPhase] ?? $requiredPhase;

        if ($status !== 'terbuka') {
            return $this->denyAccess($request, "{$label} sedang tidak aktif. Silakan tunggu hingga fase ini dibuka oleh operator.");
        }

        return $next($request);
    }

    private function denyAccess(Request $request, string $message)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'error' => 'PHASE_NOT_ACTIVE'
            ], 403);
        }

        $fallback = url()->previous() !== url()->current()
            ? url()->previous()
            : route('login');

        return redirect($fallback)->with('error', $message);
    }
}
