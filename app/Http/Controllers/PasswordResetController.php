<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\PT;
use App\Models\Reviewer;
use App\Services\EmailService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Tampilkan form reset password
     */
    public function showResetForm()
    {
        return view('auth.reset_password');
    }

    /**
     * Proses request reset password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'role' => 'required|in:mahasiswa,dosen',
            'identifier' => 'required|string|max:20',
            'email_personal' => 'required|email|max:255'
        ]);

        try {
            $user = null;
            $role = $request->role;
            $identifier = $request->identifier;

            // Cari user berdasarkan role dan identifier
            switch ($role) {
                case 'mahasiswa':
                    $user = Mahasiswa::where('nim', $identifier)->first();
                    break;
                case 'dosen':
                    $user = Dosen::where('nuptk', $identifier)->first();
                    break;
            }

            if (!$user) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', ucfirst($role) . ' dengan ' . $this->getIdentifierLabel($role) . ' tersebut tidak ditemukan dalam database.');
            }

            // Generate password baru
            $newPassword = Str::random(8);
            $hashedPassword = Hash::make($newPassword);

            // Update password
            $user->update(['password' => $hashedPassword]);

            // Kirim email reset password
            $this->emailService->sendPasswordResetEmail(
                $request->email_personal, // Email personal yang diinput
                $this->getNama($user, $role), // Nama dari database
                $identifier,
                $newPassword,
                $role,
                $this->getEmailLogin($user, $role)
            );

            return redirect()->route('login')
                ->with('success', 'Password berhasil direset! Silakan cek Gmail Anda untuk mendapatkan password baru.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Get identifier label berdasarkan role
     */
    private function getIdentifierLabel($role)
    {
        switch ($role) {
            case 'mahasiswa': return 'NIM';
            case 'dosen': return 'NUPTK';
            default: return 'Identifier';
        }
    }

    /**
     * Get nama dari database
     */
    private function getNama($user, $role)
    {
        switch ($role) {
            case 'mahasiswa':
                return $user->nama_mhs;
            case 'dosen':
                return $user->nama_dosen;
            default:
                return '';
        }
    }

    /**
     * Get email login dari database
     */
    private function getEmailLogin($user, $role)
    {
        switch ($role) {
            case 'mahasiswa':
                return $user->email_mhs;
            case 'dosen':
                return $user->email_dosen;
            default:
                return '';
        }
    }
}
