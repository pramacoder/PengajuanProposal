<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProposalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->role === 'mahasiswa';
    }

    protected function prepareForValidation()
    {
        // Parse the dana_diajukan to float before validation
        if ($this->has('dana_diajukan')) {
            $this->merge([
                'dana_diajukan' => \App\Helpers\ProposalHelper::parseAngka($this->dana_diajukan),
            ]);
        }
    }

    public function rules(): array
    {
        $skim = $this->input('skim', '');
        $insentifSkims = ['GFT', 'AI'];
        $isInsentif = in_array($skim, $insentifSkims);
        
        $danaRules = $isInsentif 
            ? 'required|numeric|min:0|max:0' 
            : 'required|numeric|min:1000000|max:15000000';

        return [
            // Informasi dasar proposal
            'judul' => 'required|string|min:10|max:200',
            'skim' => 'required|in:RE,RSH,KC,PM,PI,K,KI,VGK,AI,GFT',
            'dosen_pembimbing' => 'required|string',
            'dana_diajukan' => $danaRules,
            'tahun_ajaran' => 'required|string',
            
            // Data ketua tim (wajib)
            'ketua_nama' => 'required|string|max:255',
            'ketua_nim' => 'required|string|min:8|max:20',
            'ketua_prodi' => 'required|string|max:255',
            'ketua_fakultas' => 'required|string|max:255',
            'ketua_email' => 'required|email|max:255',
            'ketua_no_hp' => 'required|string|min:10|max:15',
            
            // Data anggota 1 (wajib)
            'anggota1_nama' => 'required|string|max:255',
            'anggota1_nim' => 'required|string|min:8|max:20',
            'anggota1_prodi' => 'required|string|max:255',
            'anggota1_fakultas' => 'required|string|max:255',
            'anggota1_email' => 'required|email|max:255',
            'anggota1_no_hp' => 'required|string|min:10|max:15',
            
            // Data anggota 2 (wajib)
            'anggota2_nama' => 'required|string|max:255',
            'anggota2_nim' => 'required|string|min:8|max:20',
            'anggota2_prodi' => 'required|string|max:255',
            'anggota2_fakultas' => 'required|string|max:255',
            'anggota2_email' => 'required|email|max:255',
            'anggota2_no_hp' => 'required|string|min:10|max:15',
            
            // Data anggota 3 (opsional)
            'anggota3_nama' => 'nullable|string|max:255',
            'anggota3_nim' => 'nullable|string|min:8|max:20',
            'anggota3_prodi' => 'nullable|string|max:255',
            'anggota3_fakultas' => 'nullable|string|max:255',
            'anggota3_email' => 'nullable|email|max:255',
            'anggota3_no_hp' => 'nullable|string|min:10|max:15',
            
            // Data anggota 4 (opsional)
            'anggota4_nama' => 'nullable|string|max:255',
            'anggota4_nim' => 'nullable|string|min:8|max:20',
            'anggota4_prodi' => 'nullable|string|max:255',
            'anggota4_fakultas' => 'nullable|string|max:255',
            'anggota4_email' => 'nullable|email|max:255',
            'anggota4_no_hp' => 'nullable|string|min:10|max:15',
            
            // Upload proposal
            'proposal_file' => 'nullable|file|mimes:pdf|max:5120',
        ];
    }

    public function messages(): array
    {
        $skim = $this->input('skim', '');
        $isInsentif = in_array($skim, ['GFT', 'AI']);

        return [
            'judul.required' => 'Judul proposal wajib diisi',
            'judul.min' => 'Judul proposal minimal 10 karakter',
            'judul.max' => 'Judul proposal maksimal 200 karakter',
            'skim.required' => 'Skim PKM wajib dipilih',
            'dosen_pembimbing.required' => 'Dosen pendamping wajib dipilih',
            'dana_diajukan.required' => 'Dana yang diajukan wajib diisi',
            'dana_diajukan.min' => $isInsentif ? 'PKM Insentif tidak memiliki pendanaan. Dana harus 0.' : 'Dana minimal Rp 1.000.000',
            'dana_diajukan.max' => $isInsentif ? 'PKM Insentif tidak memiliki pendanaan. Dana harus 0.' : 'Dana maksimal Rp 15.000.000',
            'ketua_nama.required' => 'Nama ketua tim wajib diisi',
            'ketua_nim.required' => 'NIM ketua tim wajib diisi',
            'ketua_nim.min' => 'NIM ketua tim minimal 8 digit',
            'ketua_prodi.required' => 'Program studi ketua tim wajib diisi',
            'ketua_fakultas.required' => 'Fakultas ketua tim wajib diisi',
            'ketua_email.required' => 'Email ketua tim wajib diisi',
            'ketua_email.email' => 'Format email ketua tim tidak valid',
            'ketua_no_hp.required' => 'No. HP ketua tim wajib diisi',
            'ketua_no_hp.min' => 'No. HP ketua tim minimal 10 digit',
            'anggota1_nama.required' => 'Nama anggota 1 wajib diisi',
            'anggota1_nim.required' => 'NIM anggota 1 wajib diisi',
            'anggota1_nim.min' => 'NIM anggota 1 minimal 8 digit',
            'anggota1_prodi.required' => 'Program studi anggota 1 wajib diisi',
            'anggota1_fakultas.required' => 'Fakultas anggota 1 wajib diisi',
            'anggota1_email.required' => 'Email anggota 1 wajib diisi',
            'anggota1_email.email' => 'Format email anggota 1 tidak valid',
            'anggota1_no_hp.required' => 'No. HP anggota 1 wajib diisi',
            'anggota1_no_hp.min' => 'No. HP anggota 1 minimal 10 digit',
            'anggota2_nama.required' => 'Nama anggota 2 wajib diisi',
            'anggota2_nim.required' => 'NIM anggota 2 wajib diisi',
            'anggota2_nim.min' => 'NIM anggota 2 minimal 8 digit',
            'anggota2_prodi.required' => 'Program studi anggota 2 wajib diisi',
            'anggota2_fakultas.required' => 'Fakultas anggota 2 wajib diisi',
            'anggota2_email.required' => 'Email anggota 2 wajib diisi',
            'anggota2_email.email' => 'Format email anggota 2 tidak valid',
            'anggota2_no_hp.required' => 'No. HP anggota 2 wajib diisi',
            'anggota2_no_hp.min' => 'No. HP anggota 2 minimal 10 digit',
            'proposal_file.mimes' => 'File harus berformat PDF',
            'proposal_file.max' => 'Ukuran file maksimal 5MB',
        ];
    }
}
