<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Fakultas;
use App\Models\Prodi;
use Illuminate\Support\Facades\Hash;

class AccountManagementController extends Controller
{
    public function manageAccounts(Request $request)
    {
        $fakultas = Fakultas::orderBy('nama_fakultas')->get();
        $prodis = Prodi::orderBy('nama_prodi')->get();
        
        $hasFilter = $request->filled('filter_fakultas') || 
                     $request->filled('filter_prodi') || 
                     $request->filled('filter_nim') || 
                     $request->filled('filter_nama') ||
                     $request->filled('filter_nama_dosen') ||
                     $request->filled('filter_nama_reviewer') ||
                     $request->filled('filter_nama_operator');
        
        $mahasiswaQuery = User::mahasiswa();
        
        if ($hasFilter) {
            if ($request->filled('filter_fakultas')) {
                $fakultasModel = Fakultas::find($request->filter_fakultas);
                if ($fakultasModel) {
                    $mahasiswaQuery->whereRaw("metadata->>'fakultas_name' = ?", [$fakultasModel->nama_fakultas]);
                }
            }
            
            if ($request->filled('filter_prodi')) {
                $prodiModel = Prodi::find($request->filter_prodi);
                if ($prodiModel) {
                    $mahasiswaQuery->whereRaw("metadata->>'prodi_name' = ?", [$prodiModel->nama_prodi]);
                }
            }
            
            if ($request->filled('filter_nim')) {
                $mahasiswaQuery->where('identifier', 'like', '%' . $request->filter_nim . '%');
            }
            
            if ($request->filled('filter_nama')) {
                $mahasiswaQuery->where('name', 'like', '%' . $request->filter_nama . '%');
            }
        }
        
        $mahasiswas = $hasFilter ? $mahasiswaQuery->orderBy('created_at', 'desc')->get() : collect();
        
        $dosenQuery = User::dosen();
        if ($hasFilter && $request->filled('filter_nama_dosen')) {
            $dosenQuery->where('name', 'like', '%' . $request->filter_nama_dosen . '%');
        }
        $dosens = $hasFilter ? $dosenQuery->orderBy('created_at', 'desc')->get() : collect();
        
        $reviewerQuery = User::reviewer();
        if ($hasFilter && $request->filled('filter_nama_reviewer')) {
            $reviewerQuery->where('name', 'like', '%' . $request->filter_nama_reviewer . '%');
        }
        $reviewers = $hasFilter ? $reviewerQuery->orderBy('created_at', 'desc')->get() : collect();
        
        $operatorQuery = User::whereIn('role', ['operator', 'pimpinan_pt']);
        if ($hasFilter && $request->filled('filter_nama_operator')) {
            $operatorQuery->where('name', 'like', '%' . $request->filter_nama_operator . '%');
        }
        $operators = $hasFilter ? $operatorQuery->orderBy('created_at', 'desc')->get() : collect();
        
        return view('operator.manajemen_akun', compact('mahasiswas', 'dosens', 'reviewers', 'operators', 'fakultas', 'prodis', 'hasFilter'));
    }
    
    public function bulkDeleteMahasiswa(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id',
        ]);
        
        try {
            $count = User::where('role', 'mahasiswa')->whereIn('id', $request->ids)->delete();
            return redirect()->route('operator.manage.accounts')->with('success', "Berhasil menghapus {$count} akun mahasiswa.");
        } catch (\Exception $e) {
            return redirect()->route('operator.manage.accounts')->with('error', 'Gagal menghapus akun mahasiswa: ' . $e->getMessage());
        }
    }

    public function storeAccount(Request $request, $type)
    {
        switch ($type) {
            case 'mahasiswa':
                $request->validate([
                    'nama' => 'required|string|max:255',
                    'nim' => 'required|string|max:20|unique:users,identifier',
                    'email' => 'required|email|max:255|unique:users,email',
                    'no_hp' => 'required|string|max:15',
                    'prodi' => 'required|exists:prodis,id_prodi',
                    'fakultas' => 'required|exists:fakultas,id_fakultas',
                    'password' => 'required|string|min:8|confirmed',
                ]);
                $prodi = Prodi::find($request->prodi);
                $fakultas = Fakultas::find($request->fakultas);
                User::create([
                    'name' => $request->nama,
                    'identifier' => $request->nim,
                    'email' => $request->email,
                    'phone' => $request->no_hp,
                    'password' => Hash::make($request->password),
                    'role' => 'mahasiswa',
                    'is_active' => true,
                    'metadata' => [
                        'prodi_name' => $prodi->nama_prodi,
                        'fakultas_name' => $fakultas->nama_fakultas,
                        'prodi_id' => $prodi->id_prodi,
                        'fakultas_id' => $fakultas->id_fakultas,
                    ],
                ]);
                break;
            case 'dosen':
                $request->validate([
                    'nama' => 'required|string|max:255',
                    'nuptk' => 'required|string|max:20',
                    'email' => 'required|email|max:255|unique:users,email',
                    'no_hp' => 'required|string|max:15',
                    'password' => 'required|string|min:8|confirmed',
                ]);
                User::create([
                    'name' => $request->nama,
                    'identifier' => $request->nuptk,
                    'email' => $request->email,
                    'phone' => $request->no_hp,
                    'password' => Hash::make($request->password),
                    'role' => 'dosen',
                    'is_active' => true,
                    'metadata' => ['nuptk' => $request->nuptk],
                ]);
                break;
            case 'reviewer':
                $request->validate([
                    'nama' => 'required|string|max:255',
                    'email' => 'required|email|max:255|unique:users,email',
                    'no_hp' => 'required|string|max:15',
                    'password' => 'required|string|min:8|confirmed',
                ]);
                User::create([
                    'name' => $request->nama,
                    'email' => $request->email,
                    'phone' => $request->no_hp,
                    'password' => Hash::make($request->password),
                    'role' => 'reviewer',
                    'is_active' => true,
                ]);
                break;
            case 'operator':
                $request->validate([
                    'nama' => 'required|string|max:255',
                    'email' => 'required|email|max:255|unique:users,email',
                    'no_hp' => 'required|string|max:15',
                    'password' => 'required|string|min:8|confirmed',
                ]);
                User::create([
                    'name' => $request->nama,
                    'email' => $request->email,
                    'phone' => $request->no_hp,
                    'password' => Hash::make($request->password),
                    'role' => 'operator',
                    'is_active' => true,
                ]);
                break;
            default:
                return back()->with('error', 'Tipe akun tidak dikenal.');
        }
        return redirect()->route('operator.manage.accounts')->with('success', 'Akun berhasil dibuat.');
    }

    public function updateAccount(Request $request, $type, $id)
    {
        switch ($type) {
            case 'mahasiswa':
                $mahasiswa = User::where('role', 'mahasiswa')->findOrFail($id);
                $request->validate([
                    'nama' => 'required|string|max:255',
                    'nim' => 'required|string|max:20|unique:users,identifier,' . $mahasiswa->id . ',id',
                    'email' => 'required|email|max:255|unique:users,email,' . $mahasiswa->id . ',id',
                    'no_hp' => 'required|string|max:15',
                    'prodi' => 'nullable|exists:prodis,id_prodi',
                    'fakultas' => 'nullable|exists:fakultas,id_fakultas',
                    'password' => 'nullable|string|min:8|confirmed',
                    'is_active' => 'nullable|boolean',
                ]);
                $mahasiswa->name = $request->nama;
                $mahasiswa->identifier = $request->nim;
                $mahasiswa->email = $request->email;
                $mahasiswa->phone = $request->no_hp;
                if ($request->filled('prodi')) {
                    $prodi = Prodi::find($request->prodi);
                    $mahasiswa->setMetadataValue('prodi_name', $prodi->nama_prodi);
                    $mahasiswa->setMetadataValue('prodi_id', $prodi->id_prodi);
                }
                if ($request->filled('fakultas')) {
                    $fakultas = Fakultas::find($request->fakultas);
                    $mahasiswa->setMetadataValue('fakultas_name', $fakultas->nama_fakultas);
                    $mahasiswa->setMetadataValue('fakultas_id', $fakultas->id_fakultas);
                }
                if ($request->filled('password')) { $mahasiswa->password = Hash::make($request->password); }
                if ($request->has('is_active')) { $mahasiswa->is_active = (bool)$request->is_active; }
                $mahasiswa->save();
                break;
            case 'dosen':
                $dosen = User::where('role', 'dosen')->findOrFail($id);
                $request->validate([
                    'nama' => 'required|string|max:255',
                    'nuptk' => 'required|string|max:20',
                    'email' => 'required|email|max:255|unique:users,email,' . $dosen->id . ',id',
                    'no_hp' => 'required|string|max:15',
                    'password' => 'nullable|string|min:8|confirmed',
                    'is_active' => 'nullable|boolean',
                ]);
                $dosen->name = $request->nama;
                $dosen->identifier = $request->nuptk;
                $dosen->email = $request->email;
                $dosen->phone = $request->no_hp;
                $dosen->setMetadataValue('nuptk', $request->nuptk);
                if ($request->filled('password')) { $dosen->password = Hash::make($request->password); }
                if ($request->has('is_active')) { $dosen->is_active = (bool)$request->is_active; }
                $dosen->save();
                break;
            case 'reviewer':
                $reviewer = User::where('role', 'reviewer')->findOrFail($id);
                $request->validate([
                    'nama' => 'required|string|max:255',
                    'email' => 'required|email|max:255|unique:users,email,' . $reviewer->id . ',id',
                    'no_hp' => 'required|string|max:15',
                    'password' => 'nullable|string|min:8|confirmed',
                    'is_active' => 'nullable|boolean',
                ]);
                $reviewer->name = $request->nama;
                $reviewer->email = $request->email;
                $reviewer->phone = $request->no_hp;
                if ($request->filled('password')) { $reviewer->password = Hash::make($request->password); }
                if ($request->has('is_active')) { $reviewer->is_active = (bool)$request->is_active; }
                $reviewer->save();
                break;
            case 'operator':
                $operator = User::whereIn('role', ['operator', 'pimpinan_pt'])->findOrFail($id);
                $request->validate([
                    'nama' => 'required|string|max:255',
                    'email' => 'required|email|max:255|unique:users,email,' . $operator->id . ',id',
                    'no_hp' => 'required|string|max:15',
                    'password' => 'nullable|string|min:8|confirmed',
                    'is_active' => 'nullable|boolean',
                ]);
                $operator->name = $request->nama;
                $operator->email = $request->email;
                $operator->phone = $request->no_hp;
                if ($request->filled('password')) { $operator->password = Hash::make($request->password); }
                if ($request->has('is_active')) { $operator->is_active = (bool)$request->is_active; }
                $operator->save();
                break;
            default:
                return back()->with('error', 'Tipe akun tidak dikenal.');
        }
        return redirect()->route('operator.manage.accounts')->with('success', 'Akun berhasil diperbarui.');
    }

    public function deleteAccount($type, $id)
    {
        switch ($type) {
            case 'mahasiswa':
                User::where('role', 'mahasiswa')->where('id', $id)->delete();
                break;
            case 'dosen':
                User::where('role', 'dosen')->where('id', $id)->delete();
                break;
            case 'reviewer':
                User::where('role', 'reviewer')->where('id', $id)->delete();
                break;
            case 'operator':
                User::whereIn('role', ['operator', 'pimpinan_pt'])->where('id', $id)->delete();
                break;
            default:
                return back()->with('error', 'Tipe akun tidak dikenal.');
        }
        return redirect()->route('operator.manage.accounts')->with('success', 'Akun berhasil dihapus.');
    }
}
