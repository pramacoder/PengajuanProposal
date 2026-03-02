<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
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

    public function showResetForm()
    {
        return view('auth.reset_password');
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'role' => 'required|in:mahasiswa,dosen',
            'identifier' => 'required|string|max:20',
            'email_personal' => 'required|email|max:255'
        ]);

        try {
            $user = User::where('role', $request->role)
                ->where('identifier', $request->identifier)
                ->first();

            if (!$user) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', ucfirst($request->role) . ' dengan ' . $this->getIdentifierLabel($request->role) . ' tersebut tidak ditemukan dalam database.');
            }

            $newPassword = Str::random(8);
            $user->update(['password' => Hash::make($newPassword)]);

            $this->emailService->sendPasswordResetEmail(
                $request->email_personal,
                $user->name,
                $request->identifier,
                $newPassword,
                $request->role,
                $user->email
            );

            return redirect()->route('login')
                ->with('success', 'Password berhasil direset! Silakan cek Gmail Anda untuk mendapatkan password baru.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    private function getIdentifierLabel($role)
    {
        switch ($role) {
            case 'mahasiswa': return 'NIM';
            case 'dosen': return 'NUPTK';
            default: return 'Identifier';
        }
    }
}
