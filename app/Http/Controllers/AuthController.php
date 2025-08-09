<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\Reviewer;
use App\Models\PT;
use App\Models\Proposal;
use App\Models\Fakultas;
use App\Models\Prodi;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'user_type' => 'required|in:mahasiswa,dosen,reviewer,operator'
        ]);

        $credentials = [
            'password' => $request->password,
            'is_active' => true
        ];

        // Set email field berdasarkan user type
        switch ($request->user_type) {
            case 'mahasiswa':
                $credentials['email_mhs'] = $request->email;
                $guard = 'mahasiswa';
                break;
            case 'dosen':
                $credentials['email_dosen'] = $request->email;
                $guard = 'dosen';
                break;
            case 'reviewer':
                $credentials['email_reviewer'] = $request->email;
                $guard = 'reviewer';
                break;
            case 'operator':
                $credentials['email_pt'] = $request->email;
                $guard = 'operator';
                break;
        }

        if (Auth::guard($guard)->attempt($credentials)) {
            $request->session()->regenerate();
            
            // Redirect berdasarkan user type
            switch ($request->user_type) {
                case 'mahasiswa':
                    return redirect()->intended('/mahasiswa/dashboard');
                case 'dosen':
                    return redirect()->intended('/dosen/dashboard');
                case 'reviewer':
                    return redirect()->intended('/reviewer/dashboard');
                case 'operator':
                    return redirect()->intended('/operator/dashboard');
            }
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->withInput($request->only('email', 'user_type'));
    }

    public function logout(Request $request)
    {
        // Logout dari semua guard
        Auth::guard('mahasiswa')->logout();
        Auth::guard('dosen')->logout();
        Auth::guard('reviewer')->logout();
        Auth::guard('operator')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function showDashboard()
    {
        // Cek user yang sedang login
        if (Auth::guard('mahasiswa')->check()) {
            $user = Auth::guard('mahasiswa')->user();
            return view('mahasiswa.dashboard', compact('user'));
        } elseif (Auth::guard('dosen')->check()) {
            $user = Auth::guard('dosen')->user();
            return view('dosen.dashboard', compact('user'));
        } elseif (Auth::guard('reviewer')->check()) {
            $user = Auth::guard('reviewer')->user();
            return view('reviewer.dashboard', compact('user'));
        } elseif (Auth::guard('operator')->check()) {
            $user = Auth::guard('operator')->user();
            return view('operator.dashboard', compact('user'));
        }

        return redirect('/login');
    }

    public function showProfile()
    {
        // Cek user yang sedang login dan tampilkan profil
        if (Auth::guard('mahasiswa')->check()) {
            $user = Auth::guard('mahasiswa')->user();
            return view('mahasiswa.profile', compact('user'));
        } elseif (Auth::guard('dosen')->check()) {
            $user = Auth::guard('dosen')->user();
            return view('dosen.profile', compact('user'));
        } elseif (Auth::guard('reviewer')->check()) {
            $user = Auth::guard('reviewer')->user();
            return view('reviewer.profile', compact('user'));
        } elseif (Auth::guard('operator')->check()) {
            $user = Auth::guard('operator')->user();
            return view('operator.profile', compact('user'));
        }

        return redirect('/login');
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',
        ]);

        // Cek user yang sedang login
        if (Auth::guard('mahasiswa')->check()) {
            $user = Auth::guard('mahasiswa')->user();
            $user->nama_mhs = $request->name;
            $user->no_hp_mhs = $request->phone;
        } elseif (Auth::guard('dosen')->check()) {
            $user = Auth::guard('dosen')->user();
            $user->nama_dosen = $request->name;
            $user->no_hp_dosen = $request->phone;
        } elseif (Auth::guard('reviewer')->check()) {
            $user = Auth::guard('reviewer')->user();
            $user->nama_reviewer = $request->name;
            $user->no_hp_reviewer = $request->phone;
        } elseif (Auth::guard('operator')->check()) {
            $user = Auth::guard('operator')->user();
            $user->nama_pt = $request->name;
            $user->no_hp_pt = $request->phone;
        } else {
            return redirect('/login');
        }

        // Update password jika ada
        if ($request->filled('current_password') && $request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Password saat ini salah.']);
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function showProposalForm()
    {
        if (!Auth::guard('mahasiswa')->check()) {
            return redirect('/login');
        }
        
        $user = Auth::guard('mahasiswa')->user();
        return view('mahasiswa.ajukanproposal', compact('user'));
    }

    public function storeProposal(Request $request)
    {
        if (!Auth::guard('mahasiswa')->check()) {
            return redirect('/login');
        }

        $request->validate([
            'judul' => 'required|string|max:255',
            'skim' => 'required|string',
            'tahun_ajaran' => 'required|string',
            'tanggal_pengajuan' => 'required|date',
            'ketua_nama' => 'required|string|max:255',
            'ketua_nim' => 'required|string|max:20',
            'ketua_prodi' => 'required|string|max:255',
            'ketua_fakultas' => 'required|string|max:255',
            'ketua_email' => 'required|email',
            'ketua_no_hp' => 'required|string|max:15',
        ]);

        $mahasiswa = Auth::guard('mahasiswa')->user();

        $proposal = new Proposal();
        $proposal->judul_proposal = $request->judul;
        $proposal->skim = $request->skim;
        $proposal->tanggal_pengajuan = $request->tanggal_pengajuan;
        $proposal->status_validasi = 'pending';
        $proposal->status_final = 'submitted';
        $proposal->id_mahasiswa = $mahasiswa->id_mahasiswa;
        
        // Simpan data tambahan dalam catatan (bisa diubah nanti dengan menambah kolom)
        $additionalData = [
            'tahun_ajaran' => $request->tahun_ajaran,
            'ketua_nama' => $request->ketua_nama,
            'ketua_nim' => $request->ketua_nim,
            'ketua_prodi' => $request->ketua_prodi,
            'ketua_fakultas' => $request->ketua_fakultas,
            'ketua_email' => $request->ketua_email,
            'ketua_no_hp' => $request->ketua_no_hp,
            'anggota1_nama' => $request->anggota1_nama ?? null,
            'anggota1_nim' => $request->anggota1_nim ?? null,
            'anggota1_prodi' => $request->anggota1_prodi ?? null,
            'anggota1_fakultas' => $request->anggota1_fakultas ?? null,
            'anggota1_email' => $request->anggota1_email ?? null,
            'anggota1_no_hp' => $request->anggota1_no_hp ?? null,
            'anggota2_nama' => $request->anggota2_nama ?? null,
            'anggota2_nim' => $request->anggota2_nim ?? null,
            'anggota2_prodi' => $request->anggota2_prodi ?? null,
            'anggota2_fakultas' => $request->anggota2_fakultas ?? null,
            'anggota2_email' => $request->anggota2_email ?? null,
            'anggota2_no_hp' => $request->anggota2_no_hp ?? null,
            'anggota3_nama' => $request->anggota3_nama ?? null,
            'anggota3_nim' => $request->anggota3_nim ?? null,
            'anggota3_prodi' => $request->anggota3_prodi ?? null,
            'anggota3_fakultas' => $request->anggota3_fakultas ?? null,
            'anggota3_email' => $request->anggota3_email ?? null,
            'anggota3_no_hp' => $request->anggota3_no_hp ?? null,
        ];
        
        $proposal->catatan = json_encode($additionalData);
        
        $proposal->save();

        return redirect()->route('mahasiswa.proposal.index')->with('success', 'Proposal berhasil diajukan!');
    }

    public function showProposalList()
    {
        if (!Auth::guard('mahasiswa')->check()) {
            return redirect('/login');
        }
        
        $user = Auth::guard('mahasiswa')->user();
        $proposals = Proposal::where('id_mahasiswa', $user->id_mahasiswa)->orderBy('created_at', 'desc')->get();
        
        return view('mahasiswa.proposal_list', compact('user', 'proposals'));
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
        
        // Validasi berdasarkan user type
        switch ($userType) {
            case 'mahasiswa':
                $request->validate([
                    'nama_mhs' => 'required|string|max:255',
                    'nim' => 'required|string|max:20|unique:mahasiswas,nim',
                    'email_mhs' => 'required|email|max:255|unique:mahasiswas,email_mhs',
                    'no_hp_mhs' => 'required|string|max:15',
                    'prodi' => 'required|exists:prodis,id_prodi',
                    'fakultas' => 'required|exists:fakultas,id_fakultas',
                    'password' => 'required|string|min:8|confirmed',
                ]);

                // Cek apakah email sudah terdaftar
                $existingMahasiswa = Mahasiswa::where('email_mhs', $request->email_mhs)->first();
                if ($existingMahasiswa) {
                    return back()->withErrors(['email_mhs' => 'Email sudah terdaftar.'])->withInput();
                }

                // Cek apakah NIM sudah terdaftar
                $existingNIM = Mahasiswa::where('nim', $request->nim)->first();
                if ($existingNIM) {
                    return back()->withErrors(['nim' => 'NIM sudah terdaftar.'])->withInput();
                }

                // Ambil nama prodi dan fakultas berdasarkan ID
                $prodi = Prodi::find($request->prodi);
                $fakultas = Fakultas::find($request->fakultas);
                
                // Buat mahasiswa baru
                $mahasiswa = new Mahasiswa();
                $mahasiswa->nama_mhs = $request->nama_mhs;
                $mahasiswa->nim = $request->nim;
                $mahasiswa->email_mhs = $request->email_mhs;
                $mahasiswa->no_hp_mhs = $request->no_hp_mhs;
                $mahasiswa->prodi_mhs = $prodi->nama_prodi;
                $mahasiswa->fakultas_mhs = $fakultas->nama_fakultas;
                $mahasiswa->password = Hash::make($request->password);
                $mahasiswa->is_active = true;
                $mahasiswa->save();

                return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login.');
                break;

            default:
                return back()->withErrors(['user_type' => 'Role tidak valid.'])->withInput();
        }
    }
} 