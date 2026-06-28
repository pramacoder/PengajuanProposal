<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Proposal;
use App\Models\Fakultas;
use App\Models\Prodi;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendPasswordReset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'nim' => 'required|string',
            'gmail' => 'required|email',
        ]);

        $user = User::where('email', $request->email)
            ->where('identifier', $request->nim)
            ->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'Email atau NIM/NIDN/NIP tidak ditemukan dalam sistem.',
            ])->withInput();
        }

        $newPassword = strtoupper(substr(md5(uniqid()), 0, 8));
        $user->password = Hash::make($newPassword);
        $user->save();

        $request->session()->put('reset_data', [
            'user_type' => $user->role,
            'name' => $user->name,
            'email' => $request->gmail,
            'new_password' => $newPassword,
        ]);

        return redirect()->route('password.forgot')->with('success',
            'Password berhasil direset! Password baru telah dikirim ke ' . $request->gmail .
            '. Silakan cek email Anda dan gunakan password baru untuk login.');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)
            ->where('is_active', true)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'email' => 'Email atau password salah.',
            ])->withInput($request->only('email'));
        }

        Auth::login($user);
        $request->session()->regenerate();

        Log::info('Login successful', [
            'user_id' => $user->id,
            'role' => $user->role,
        ]);

        return redirect()->intended($this->getDashboardUrl($user->role));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function showDashboard()
    {
        $user = $this->getAuthenticatedUser();

        if (!$user) {
            return redirect('/login');
        }

        return match ($user->role) {
            'mahasiswa' => view('mahasiswa.dashboard', compact('user')),
            'dosen' => view('dosen.dashboard', compact('user')),
            'reviewer' => redirect()->route('reviewer.dashboard'),
            'operator' => view('operator.dashboard', compact('user')),
            'pimpinan_pt' => redirect()->route('pimpinan-pt.dashboard'),
            default => redirect('/login'),
        };
    }

    public function showProfile()
    {
        $user = $this->getAuthenticatedUser();

        if (!$user) {
            return redirect('/login');
        }

        return match ($user->role) {
            'mahasiswa' => view('mahasiswa.profile', compact('user')),
            'dosen' => view('dosen.profile', compact('user')),
            'reviewer' => view('reviewer.profile', compact('user')),
            'operator', 'pimpinan_pt' => view('operator.profile', compact('user')),
            default => redirect('/login'),
        };
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
        ]);

        $user = $this->getAuthenticatedUser();

        if (!$user) {
            return redirect('/login');
        }

        $user->name = $request->name;
        $user->phone = $request->phone;

        if ($request->filled('current_password') && $request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Password saat ini salah.']);
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function showRegisterForm(Request $request)
    {
        $userType = $request->get('user_type', 'mahasiswa');

        if ($userType === 'mahasiswa') {
            $fakultas = Fakultas::orderBy('nama_fakultas')->get();
            return view('auth.register', compact('userType', 'fakultas'));
        }

        return view('auth.register', compact('userType'));
    }

    public function register(Request $request)
    {
        $userType = $request->input('user_type', 'mahasiswa');

        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'identifier' => 'required|string|max:50|unique:users,identifier',
            'no_hp' => 'required|string|max:15',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $metadata = [];

        if ($userType === 'mahasiswa') {
            $request->validate([
                'prodi' => 'required|exists:prodis,id_prodi',
                'fakultas' => 'required|exists:fakultas,id_fakultas',
            ]);

            $prodi = Prodi::find($request->prodi);
            $fakultas = Fakultas::find($request->fakultas);

            $metadata = [
                'prodi_id' => $prodi->id_prodi,
                'prodi_name' => $prodi->nama_prodi,
                'fakultas_id' => $fakultas->id_fakultas,
                'fakultas_name' => $fakultas->nama_fakultas,
            ];
        } elseif ($userType === 'dosen') {
            $metadata = [
                'nuptk' => $request->input('nuptk'),
                'gelar_depan' => $request->input('gelar_depan'),
                'gelar_belakang' => $request->input('gelar_belakang'),
            ];
        }

        User::create([
            'identifier' => $request->identifier,
            'name' => $request->nama,
            'email' => $request->email,
            'phone' => $request->no_hp,
            'password' => $request->password,
            'role' => $userType,
            'is_active' => true,
            'metadata' => $metadata,
        ]);

        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login.');
    }

    // =========================================================================
    // Helper Methods
    // =========================================================================

    protected function getAuthenticatedUser(): ?User
    {
        return Auth::user();
    }

    protected function getDashboardUrl(string $role): string
    {
        return match ($role) {
            'mahasiswa' => '/mahasiswa/dashboard',
            'dosen' => '/dosen/dashboard',
            'reviewer' => '/reviewer/dashboard',
            'operator' => '/operator/dashboard',
            'pimpinan_pt' => '/pimpinan-pt/dashboard',
            default => '/login',
        };
    }
}
