<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\RuangKontrol;

class CheckActivePhase
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $requiredPhase
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $requiredPhase)
    {
        $ruangKontrol = RuangKontrol::first();
        
        if (!$ruangKontrol) {
            // If no ruang kontrol exists, deny access
            return $this->denyAccess($request, 'Sistem belum dikonfigurasi. Silakan hubungi operator.');
        }
        
        $isPhaseActive = false;
        $activePhase = '';
        
        switch ($requiredPhase) {
            case 'pendaftaran':
                $isPhaseActive = $ruangKontrol->status_pendaftaran === 'terbuka';
                $activePhase = 'Pengajuan Proposal';
                break;
            case 'perbaikan':
                $isPhaseActive = $ruangKontrol->status_perbaikan === 'terbuka';
                $activePhase = 'Perbaikan Proposal';
                break;
            default:
                return $this->denyAccess($request, 'Fase yang diminta tidak valid.');
        }
        
        if (!$isPhaseActive) {
            return $this->denyAccess($request, "Fase {$activePhase} sedang tidak aktif. Silakan tunggu hingga fase ini dibuka oleh operator.");
        }
        
        return $next($request);
    }
    
    /**
     * Deny access and return appropriate response
     */
    private function denyAccess(Request $request, string $message)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $message,
                'error' => 'PHASE_NOT_ACTIVE'
            ], 403);
        }
        
        return redirect()->back()->with('error', $message);
    }
}
