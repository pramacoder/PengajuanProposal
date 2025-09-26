<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mahasiswa;
use App\Models\Dosen;
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

    /**
     * Tampilkan form registrasi mahasiswa
     */
    public function showRegistrationForm()
    {
        return view('auth.register_mahasiswa');
    }

    /**
     * Proses request kredensial login
     */
    public function register(Request $request)
    {
        $request->validate([
            'nama_mahasiswa' => 'required|string|max:255',
            'nim' => 'required|string|max:20',
            'email_mahasiswa' => 'required|email|max:255',
            'nama_dosen' => 'required|string|max:255',
            'nuptk_dosen' => 'required|string|max:20',
            'email_dosen' => 'required|email|max:255'
        ]);

        try {
            // Cari data mahasiswa berdasarkan NIM
            $mahasiswa = Mahasiswa::where('nim', $request->nim)->first();
            
            if (!$mahasiswa) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'NIM tidak ditemukan dalam database. Silakan hubungi administrator.');
            }

            // Cari data dosen berdasarkan NUPTK
            $dosen = Dosen::where('nuptk', $request->nuptk_dosen)->first();
            
            if (!$dosen) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'NUPTK dosen tidak ditemukan dalam database. Silakan hubungi administrator.');
            }

            // Update relasi dosen pembimbing
            $mahasiswa->update([
                'id_dosen_pembimbing' => $dosen->id_dosen
            ]);

            // Generate password baru untuk mahasiswa dan dosen
            $passwordMahasiswa = Str::random(8);
            $passwordDosen = Str::random(8);

            // Update password
            $mahasiswa->update(['password' => Hash::make($passwordMahasiswa)]);
            $dosen->update(['password' => Hash::make($passwordDosen)]);

            // Kirim email kredensial ke mahasiswa (ke Gmail yang diinput)
            $this->emailService->sendRegistrationEmail(
                $request->email_mahasiswa, // Gmail yang diinput mahasiswa
                $request->nama_mahasiswa, // Nama yang diinput mahasiswa
                $mahasiswa->nim,
                $passwordMahasiswa,
                'mahasiswa',
                $mahasiswa->email_mhs // Email login yang sebenarnya
            );

            // Kirim email kredensial ke dosen (ke Gmail yang diinput)
            $this->emailService->sendRegistrationEmail(
                $request->email_dosen, // Gmail yang diinput dosen
                $request->nama_dosen, // Nama yang diinput dosen
                $dosen->nuptk,
                $passwordDosen,
                'dosen',
                $dosen->email_dosen // Email login yang sebenarnya
            );

            return redirect()->route('login')
                ->with('success', 'Kredensial login berhasil dikirim! Silakan cek Gmail mahasiswa dan dosen pembimbing untuk mendapatkan kredensial login.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
