<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $userType = $user->role;

        return view("{$userType}.profile", compact('user', 'userType'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $userType = $user->role;
        $rules = $this->getValidationRules($userType, $user->id);

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            $this->updateUserData($user, $request, $userType);
            return back()->with('success', 'Profile berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat memperbarui profile: ' . $e->getMessage());
        }
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak sesuai']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Password berhasil diperbarui!');
    }

    private function getValidationRules($userType, $userId)
    {
        $baseRules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $userId,
            'phone' => 'nullable|string|max:15',
        ];

        switch ($userType) {
            case 'mahasiswa':
                return array_merge($baseRules, [
                    'identifier' => 'required|string|max:20|unique:users,identifier,' . $userId,
                ]);

            case 'dosen':
                return array_merge($baseRules, [
                    'identifier' => 'required|string|max:20|unique:users,identifier,' . $userId,
                ]);

            case 'reviewer':
                return array_merge($baseRules, [
                    'identifier' => 'required|string|max:20|unique:users,identifier,' . $userId,
                ]);

            case 'operator':
            case 'pimpinan_pt':
                return $baseRules;

            default:
                return $baseRules;
        }
    }

    private function updateUserData($user, Request $request, $userType)
    {
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;

        if ($request->has('identifier')) {
            $user->identifier = $request->identifier;
        }

        $metadata = $user->metadata ?? [];

        switch ($userType) {
            case 'mahasiswa':
                if ($request->has('prodi')) $metadata['prodi_name'] = $request->prodi;
                if ($request->has('fakultas')) $metadata['fakultas_name'] = $request->fakultas;
                break;

            case 'dosen':
                if ($request->has('gelar_depan')) $metadata['gelar_depan'] = $request->gelar_depan;
                if ($request->has('gelar_belakang')) $metadata['gelar_belakang'] = $request->gelar_belakang;
                if ($request->has('nuptk')) $metadata['nuptk'] = $request->nuptk;
                break;
        }

        $user->metadata = $metadata;
        $user->save();
    }
}
