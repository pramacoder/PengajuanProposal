<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Services\EmailService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MahasiswaRegistrationController extends Controller
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function showRegistrationForm()
    {
        return view('auth.register_mahasiswa');
    }

    public function register(Request $request)
    {
        $roleType = $request->input('role_type', 'mahasiswa');

        if ($roleType === 'mahasiswa') {
            $request->validate([
                'nama_mahasiswa' => 'required|string|max:255',
                'nim' => 'required|string|max:20',
                'email_mahasiswa' => 'required|email|max:255'
            ]);

            try {
                $mahasiswa = User::where('role', 'mahasiswa')
                    ->where('identifier', $request->nim)
                    ->first();

                if (!$mahasiswa) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'NIM tidak ditemukan dalam database. Silakan hubungi administrator.');
                }

                $defaultPassword = Str::random(12);
                $mahasiswa->update(['password' => Hash::make($defaultPassword)]);

                $this->emailService->sendRegistrationEmail(
                    $request->email_mahasiswa,
                    $request->nama_mahasiswa,
                    $mahasiswa->identifier,
                    $defaultPassword,
                    'mahasiswa',
                    $mahasiswa->email
                );

                return redirect()->route('login')
                    ->with('success', 'Kredensial login berhasil dikirim! Silakan cek Gmail Anda untuk mendapatkan kredensial login.');

            } catch (\Exception $e) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
            }

        } elseif ($roleType === 'dosen') {
            $request->validate([
                'nama_dosen' => 'required|string|max:255',
                'nuptk_dosen' => 'required|string|max:20',
                'email_dosen' => 'required|email|max:255'
            ]);

            try {
                $dosen = User::where('role', 'dosen')
                    ->where('identifier', $request->nuptk_dosen)
                    ->first();

                if (!$dosen) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'NUPTK/NIDN dosen tidak ditemukan dalam database. Silakan hubungi administrator.');
                }

                $defaultPassword = Str::random(12);
                $dosen->update(['password' => Hash::make($defaultPassword)]);

                $this->emailService->sendRegistrationEmail(
                    $request->email_dosen,
                    $request->nama_dosen,
                    $dosen->identifier,
                    $defaultPassword,
                    'dosen',
                    $dosen->email
                );

                return redirect()->route('login')
                    ->with('success', 'Kredensial login berhasil dikirim! Silakan cek Gmail Anda untuk mendapatkan kredensial login.');

            } catch (\Exception $e) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('error', 'Invalid role type.');
    }
}
