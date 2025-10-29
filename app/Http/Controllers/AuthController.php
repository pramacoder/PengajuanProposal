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

    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendPasswordReset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'nim' => 'required|string',
            'gmail' => 'required|email'
        ]);

        // Cari user berdasarkan email dan NIM
        $user = null;
        $userType = null;

        // Cek di tabel mahasiswa
        $mahasiswa = Mahasiswa::where('email_mhs', $request->email)
                             ->where('nim', $request->nim)
                             ->first();
        if ($mahasiswa) {
            $user = $mahasiswa;
            $userType = 'mahasiswa';
        }

        // Cek di tabel dosen
        if (!$user) {
            $dosen = Dosen::where('email_dosen', $request->email)
                         ->where('nuptk', $request->nim)
                         ->first();
            if ($dosen) {
                $user = $dosen;
                $userType = 'dosen';
            }
        }

        // Cek di tabel reviewer
        if (!$user) {
            $reviewer = Reviewer::where('email_reviewer', $request->email)
                               ->where('nip_reviewer', $request->nim)
                               ->first();
            if ($reviewer) {
                $user = $reviewer;
                $userType = 'reviewer';
            }
        }

        // Cek di tabel operator
        if (!$user) {
            $operator = PT::where('email_pt', $request->email)
                         ->first();
            if ($operator) {
                $user = $operator;
                $userType = 'operator';
            }
        }

        if (!$user) {
            return back()->withErrors([
                'email' => 'Email atau NIM tidak ditemukan dalam sistem.',
            ])->withInput();
        }

        // Generate password baru
        $newPassword = strtoupper(substr(md5(uniqid()), 0, 8));
        
        // Update password
        $user->password = Hash::make($newPassword);
        $user->save();

        // Kirim email (implementasi sederhana - bisa dikembangkan dengan Mail class)
        // Untuk sementara, kita simpan data di session untuk ditampilkan
        $request->session()->put('reset_data', [
            'user_type' => $userType,
            'name' => $user->nama_mhs ?? $user->nama_dosen ?? $user->nama_reviewer ?? $user->nama_pt,
            'email' => $request->gmail,
            'new_password' => $newPassword
        ]);

        return redirect()->route('password.forgot')->with('success', 
            'Password berhasil direset! Password baru telah dikirim ke ' . $request->gmail . 
            '. Silakan cek email Anda dan gunakan password baru untuk login.');
    }


    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $email = $request->email;
        $password = $request->password;

        // Coba login ke semua guard secara berurutan
        $guards = [
            'mahasiswa' => ['email_mhs', 'mahasiswa'],
            'dosen' => ['email_dosen', 'dosen'],
            'reviewer' => ['email_reviewer', 'reviewer'],
            'operator' => ['email_pt', 'operator']
        ];

        foreach ($guards as $guardName => $config) {
            $emailField = $config[0];
            $guard = $config[1];
            
            $credentials = [
                $emailField => $email,
                'password' => $password,
                'is_active' => true
            ];

            // Debug: cek credentials
            \Log::info('Login attempt', [
                'guard' => $guard,
                'email' => $email,
                'email_field' => $emailField,
                'credentials' => $credentials
            ]);

            if (Auth::guard($guard)->attempt($credentials)) {
                $request->session()->regenerate();
                
                // Debug: cek user yang berhasil login
                $user = Auth::guard($guard)->user();
                \Log::info('Login successful', [
                    'user_id' => $user->id ?? 'unknown',
                    'guard' => $guard,
                    'email' => $email
                ]);
                
                // Redirect berdasarkan guard yang berhasil
                switch ($guard) {
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
        }

        // Debug: cek jika login gagal
        \Log::warning('Login failed', [
            'email' => $email,
            'message' => 'No matching user found in any guard'
        ]);

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->withInput($request->only('email'));
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
            // Redirect ke ReviewerController dashboard
            return redirect()->route('reviewer.dashboard');
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
            $user = Auth::guard('mahasiswa')->user()->load(['prodi.fakultas', 'proposals']);
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

    public function showDosenRegistration()
    {
        return view('auth.register_dosen');
    }

    public function showReviewerRegistration()
    {
        return view('auth.register_reviewer');
    }

    public function showOperatorRegistration()
    {
        return view('auth.register_operator');
    }

    public function registerDosen(Request $request)
    {
        $request->validate([
            'nama_dosen' => 'required|string|max:255',
            'nuptk' => 'required|string|max:20|unique:dosens,nuptk',
            'gelar_depan' => 'nullable|string|max:50',
            'gelar_belakang' => 'nullable|string|max:50',
            'email_dosen' => 'required|email|max:255|unique:dosens,email_dosen',
            'no_hp_dosen' => 'required|string|max:15',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Cek apakah email sudah terdaftar
        $existingDosen = Dosen::where('email_dosen', $request->email_dosen)->first();
        if ($existingDosen) {
            return back()->withErrors(['email_dosen' => 'Email sudah terdaftar.'])->withInput();
        }

        // Cek apakah NUPTK sudah terdaftar
        $existingNUPTK = Dosen::where('nuptk', $request->nuptk)->first();
        if ($existingNUPTK) {
            return back()->withErrors(['nuptk' => 'NUPTK sudah terdaftar.'])->withInput();
        }
        
        // Buat dosen baru
        $dosen = new Dosen();
        $dosen->nama_dosen = $request->nama_dosen;
        $dosen->nuptk = $request->nuptk;
        $dosen->gelar_depan = $request->gelar_depan;
        $dosen->gelar_belakang = $request->gelar_belakang;
        $dosen->email_dosen = $request->email_dosen;
        $dosen->no_hp_dosen = $request->no_hp_dosen;
        $dosen->password = Hash::make($request->password);
        $dosen->is_active = true;
        $dosen->save();

        return redirect()->route('login.dosen')->with('success', 'Registrasi berhasil! Silakan login sebagai Dosen.');
    }

    public function registerReviewer(Request $request)
    {
        $request->validate([
            'nama_reviewer' => 'required|string|max:255',
            'email_reviewer' => 'required|email|max:255|unique:reviewers,email_reviewer',
            'no_hp_reviewer' => 'required|string|max:15',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Cek apakah email sudah terdaftar
        $existingReviewer = Reviewer::where('email_reviewer', $request->email_reviewer)->first();
        if ($existingReviewer) {
            return back()->withErrors(['email_reviewer' => 'Email sudah terdaftar.'])->withInput();
        }
        
        // Buat reviewer baru
        $reviewer = new Reviewer();
        $reviewer->nama_reviewer = $request->nama_reviewer;
        $reviewer->email_reviewer = $request->email_reviewer;
        $reviewer->no_hp_reviewer = $request->no_hp_reviewer;
        $reviewer->password = Hash::make($request->password);
        $reviewer->role = 'reviewer';
        $reviewer->is_active = true;
        $reviewer->save();

        return redirect()->route('login.reviewer')->with('success', 'Registrasi berhasil! Silakan login sebagai Reviewer.');
    }

    public function registerOperator(Request $request)
    {
        $request->validate([
            'nama_pt' => 'required|string|max:255',
            'email_pt' => 'required|email|max:255|unique:pts,email_pt',
            'no_hp_pt' => 'required|string|max:15',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Cek apakah email sudah terdaftar
        $existingPT = PT::where('email_pt', $request->email_pt)->first();
        if ($existingPT) {
            return back()->withErrors(['email_pt' => 'Email sudah terdaftar.'])->withInput();
        }
        
        // Buat operator baru
        $pt = new PT();
        $pt->nama_pt = $request->nama_pt;
        $pt->email_pt = $request->email_pt;
        $pt->no_hp_pt = $request->no_hp_pt;
        $pt->password = Hash::make($request->password);
        $pt->role = 'operator';
        $pt->is_active = true;
        $pt->save();

        return redirect()->route('login.operator')->with('success', 'Registrasi berhasil! Silakan login sebagai Operator.');
    }

    public function register(Request $request)
    {
        $userType = $request->input('user_type', 'mahasiswa');
        
        // Validasi berdasarkan user type
        switch ($userType) {
            case 'mahasiswa':
                $request->validate([
                    'nama' => 'required|string|max:255',
                    'nim' => 'required|string|max:20|unique:mahasiswas,nim',
                    'email' => 'required|email|max:255|unique:mahasiswas,email_mhs',
                    'no_hp' => 'required|string|max:15',
                    'prodi' => 'required|exists:prodis,id_prodi',
                    'fakultas' => 'required|exists:fakultas,id_fakultas',
                    'password' => 'required|string|min:8|confirmed',
                ]);

                // Cek apakah email sudah terdaftar
                $existingMahasiswa = Mahasiswa::where('email_mhs', $request->email)->first();
                if ($existingMahasiswa) {
                    return back()->withErrors(['email' => 'Email sudah terdaftar.'])->withInput();
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
                $mahasiswa->nama_mhs = $request->nama;
                $mahasiswa->nim = $request->nim;
                $mahasiswa->email_mhs = $request->email;
                $mahasiswa->no_hp_mhs = $request->no_hp;
                $mahasiswa->prodi_mhs = $prodi->nama_prodi;
                $mahasiswa->fakultas_mhs = $fakultas->nama_fakultas;
                $mahasiswa->password = Hash::make($request->password);
                $mahasiswa->is_active = true;
                $mahasiswa->save();

                return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login sebagai Mahasiswa.');
                break;

            case 'dosen':
                $request->validate([
                    'nama' => 'required|string|max:255',
                    'nuptk' => 'required|string|max:20|unique:dosens,nuptk',
                    'email' => 'required|email|max:255|unique:dosens,email_dosen',
                    'no_hp' => 'required|string|max:15',
                    'password' => 'required|string|min:8|confirmed',
                ]);

                // Cek apakah email sudah terdaftar
                $existingDosen = Dosen::where('email_dosen', $request->email)->first();
                if ($existingDosen) {
                    return back()->withErrors(['email' => 'Email sudah terdaftar.'])->withInput();
                }

                // Cek apakah NUPTK sudah terdaftar
                $existingNUPTK = Dosen::where('nuptk', $request->nuptk)->first();
                if ($existingNUPTK) {
                    return back()->withErrors(['nuptk' => 'NUPTK sudah terdaftar.'])->withInput();
                }
                
                // Buat dosen baru
                $dosen = new Dosen();
                $dosen->nama_dosen = $request->nama;
                $dosen->nuptk = $request->nuptk;
                $dosen->email_dosen = $request->email;
                $dosen->no_hp_dosen = $request->no_hp;
                $dosen->password = Hash::make($request->password);
                $dosen->is_active = true;
                $dosen->save();

                return redirect()->route('login.dosen')->with('success', 'Registrasi berhasil! Silakan login sebagai Dosen.');
                break;

            case 'reviewer':
                $request->validate([
                    'nama' => 'required|string|max:255',
                    'email' => 'required|email|max:255|unique:reviewers,email_reviewer',
                    'no_hp' => 'required|string|max:15',
                    'password' => 'required|string|min:8|confirmed',
                ]);

                // Cek apakah email sudah terdaftar
                $existingReviewer = Reviewer::where('email_reviewer', $request->email)->first();
                if ($existingReviewer) {
                    return back()->withErrors(['email' => 'Email sudah terdaftar.'])->withInput();
                }
                
                // Buat reviewer baru
                $reviewer = new Reviewer();
                $reviewer->nama_reviewer = $request->nama;
                $reviewer->email_reviewer = $request->email;
                $reviewer->no_hp_reviewer = $request->no_hp;
                $reviewer->password = Hash::make($request->password);
                $reviewer->role = 'reviewer';
                $reviewer->is_active = true;
                $reviewer->save();

                return redirect()->route('login.reviewer')->with('success', 'Registrasi berhasil! Silakan login sebagai Reviewer.');
                break;

            case 'operator':
                $request->validate([
                    'nama' => 'required|string|max:255',
                    'email' => 'required|email|max:255|unique:pts,email_pt',
                    'no_hp' => 'required|string|max:15',
                    'password' => 'required|string|min:8|confirmed',
                ]);

                // Cek apakah email sudah terdaftar
                $existingPT = PT::where('email_pt', $request->email)->first();
                if ($existingPT) {
                    return back()->withErrors(['email' => 'Email sudah terdaftar.'])->withInput();
                }
                
                // Buat operator baru
                $pt = new PT();
                $pt->nama_pt = $request->nama;
                $pt->email_pt = $request->email;
                $pt->no_hp_pt = $request->no_hp;
                $pt->password = Hash::make($request->password);
                $pt->role = 'operator';
                $pt->is_active = true;
                $pt->save();

                return redirect()->route('login.operator')->with('success', 'Registrasi berhasil! Silakan login sebagai Operator.');
                break;

            default:
                return back()->withErrors(['user_type' => 'Role tidak valid.'])->withInput();
        }
    }
} 