<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\Mahasiswa;
use App\Models\Dosen;
use App\Models\Reviewer;
use App\Models\PT;

class ProfileController extends Controller
{
    /**
     * Show profile edit form for current authenticated user
     */
    public function edit()
    {
        $user = null;
        $userType = null;

        // Check which guard is authenticated and get user data
        if (Auth::guard('mahasiswa')->check()) {
            $user = Auth::guard('mahasiswa')->user();
            $userType = 'mahasiswa';
        } elseif (Auth::guard('dosen')->check()) {
            $user = Auth::guard('dosen')->user();
            $userType = 'dosen';
        } elseif (Auth::guard('reviewer')->check()) {
            $user = Auth::guard('reviewer')->user();
            $userType = 'reviewer';
        } elseif (Auth::guard('operator')->check()) {
            $user = Auth::guard('operator')->user();
            $userType = 'operator';
        } else {
            return redirect()->route('login');
        }

        return view("{$userType}.profile", compact('user', 'userType'));
    }

    /**
     * Update profile information
     */
    public function update(Request $request)
    {
        $user = null;
        $userType = null;

        // Determine which guard is authenticated
        if (Auth::guard('mahasiswa')->check()) {
            $user = Auth::guard('mahasiswa')->user();
            $userType = 'mahasiswa';
        } elseif (Auth::guard('dosen')->check()) {
            $user = Auth::guard('dosen')->user();
            $userType = 'dosen';
        } elseif (Auth::guard('reviewer')->check()) {
            $user = Auth::guard('reviewer')->user();
            $userType = 'reviewer';
        } elseif (Auth::guard('operator')->check()) {
            $user = Auth::guard('operator')->user();
            $userType = 'operator';
        } else {
            return redirect()->route('login');
        }

        // Validation rules based on user type
        $rules = $this->getValidationRules($userType);
        
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Update user data based on type
            $this->updateUserData($user, $request, $userType);

            return back()->with('success', 'Profile berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat memperbarui profile: ' . $e->getMessage());
        }
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = null;
        $userType = null;

        // Determine which guard is authenticated
        if (Auth::guard('mahasiswa')->check()) {
            $user = Auth::guard('mahasiswa')->user();
            $userType = 'mahasiswa';
        } elseif (Auth::guard('dosen')->check()) {
            $user = Auth::guard('dosen')->user();
            $userType = 'dosen';
        } elseif (Auth::guard('reviewer')->check()) {
            $user = Auth::guard('reviewer')->user();
            $userType = 'reviewer';
        } elseif (Auth::guard('operator')->check()) {
            $user = Auth::guard('operator')->user();
            $userType = 'operator';
        } else {
            return redirect()->route('login');
        }

        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai']);
        }

        // Update password
        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password berhasil diperbarui!');
    }

    /**
     * Get validation rules based on user type
     */
    private function getValidationRules($userType)
    {
        $baseRules = [
            'email' => 'required|email|unique:' . $this->getTableName($userType) . ',email,' . Auth::guard($userType)->id() . ',' . $this->getPrimaryKey($userType),
        ];

        switch ($userType) {
            case 'mahasiswa':
                return array_merge($baseRules, [
                    'nama_mhs' => 'required|string|max:255',
                    'nim' => 'required|string|max:20|unique:mahasiswas,nim,' . Auth::guard('mahasiswa')->id() . ',id_mahasiswa',
                    'alamat_mhs' => 'nullable|string|max:500',
                    'no_telp_mhs' => 'nullable|string|max:15',
                ]);
            
            case 'dosen':
                return array_merge($baseRules, [
                    'nama_dosen' => 'required|string|max:255',
                    'nidn' => 'required|string|max:20|unique:dosens,nidn,' . Auth::guard('dosen')->id() . ',id_dosen',
                    'alamat_dosen' => 'nullable|string|max:500',
                    'no_telp_dosen' => 'nullable|string|max:15',
                ]);
            
            case 'reviewer':
                return array_merge($baseRules, [
                    'nama_reviewer' => 'required|string|max:255',
                    'nidn' => 'required|string|max:20|unique:reviewers,nidn,' . Auth::guard('reviewer')->id() . ',id_reviewer',
                    'alamat_reviewer' => 'nullable|string|max:500',
                    'no_telp_reviewer' => 'nullable|string|max:15',
                ]);
            
            case 'operator':
                return array_merge($baseRules, [
                    'nama_pt' => 'required|string|max:255',
                    'alamat_pt' => 'nullable|string|max:500',
                    'no_telp_pt' => 'nullable|string|max:15',
                ]);
            
            default:
                return $baseRules;
        }
    }

    /**
     * Get table name based on user type
     */
    private function getTableName($userType)
    {
        $tables = [
            'mahasiswa' => 'mahasiswas',
            'dosen' => 'dosens',
            'reviewer' => 'reviewers',
            'operator' => 'pts',
        ];

        return $tables[$userType] ?? 'users';
    }

    /**
     * Get primary key based on user type
     */
    private function getPrimaryKey($userType)
    {
        $keys = [
            'mahasiswa' => 'id_mahasiswa',
            'dosen' => 'id_dosen',
            'reviewer' => 'id_reviewer',
            'operator' => 'id_pt',
        ];

        return $keys[$userType] ?? 'id';
    }

    /**
     * Update user data based on type
     */
    private function updateUserData($user, Request $request, $userType)
    {
        switch ($userType) {
            case 'mahasiswa':
                $user->nama_mhs = $request->nama_mhs;
                $user->nim = $request->nim;
                $user->email_mhs = $request->email;
                $user->alamat_mhs = $request->alamat_mhs;
                $user->no_telp_mhs = $request->no_telp_mhs;
                break;
            
            case 'dosen':
                $user->nama_dosen = $request->nama_dosen;
                $user->nidn = $request->nidn;
                $user->email_dosen = $request->email;
                $user->alamat_dosen = $request->alamat_dosen;
                $user->no_telp_dosen = $request->no_telp_dosen;
                break;
            
            case 'reviewer':
                $user->nama_reviewer = $request->nama_reviewer;
                $user->nidn = $request->nidn;
                $user->email_reviewer = $request->email;
                $user->alamat_reviewer = $request->alamat_reviewer;
                $user->no_telp_reviewer = $request->no_telp_reviewer;
                break;
            
            case 'operator':
                $user->nama_pt = $request->nama_pt;
                $user->email_pt = $request->email;
                $user->alamat_pt = $request->alamat_pt;
                $user->no_telp_pt = $request->no_telp_pt;
                break;
        }

        $user->save();
    }
}
