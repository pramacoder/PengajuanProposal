<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proposal extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_proposal';

    protected $fillable = [
        'judul_proposal', 'judul', 'tanggal_pengajuan', 'skim', 'status_validasi',
        'status_validasi_2', 'status_final', 'status', 'catatan',
        'id_mahasiswa', 'id_dosen', 'id_dosen_pendamping_universitas', 'team_id',
        'dosen_pembimbing', 'dana_diajukan', 'dana_diajukan_operator', 'dana_diajukan_belmawa',
        'tahun_ajaran', 'tanggal_validasi',
        'id_reviewer_administratif', 'id_reviewer_substantif_1', 'id_reviewer_substantif_2',
        'id_reviewer_substantif_seleksi_1', 'id_reviewer_substantif_seleksi_2',
        'path_review_dosen', 'nama_file_review_dosen', 'tanggal_review_dosen',
        'ketua_nama', 'ketua_nim', 'ketua_prodi', 'ketua_fakultas', 'ketua_email', 'ketua_no_hp',
        'anggota1_nama', 'anggota1_nim', 'anggota1_prodi', 'anggota1_fakultas', 'anggota1_email', 'anggota1_no_hp',
        'anggota2_nama', 'anggota2_nim', 'anggota2_prodi', 'anggota2_fakultas', 'anggota2_email', 'anggota2_no_hp',
        'anggota3_nama', 'anggota3_nim', 'anggota3_prodi', 'anggota3_fakultas', 'anggota3_email', 'anggota3_no_hp',
        'anggota4_nama', 'anggota4_nim', 'anggota4_prodi', 'anggota4_fakultas', 'anggota4_email', 'anggota4_no_hp',
    ];

    // =========================================================================
    // Relationships - All reference unified users table
    // =========================================================================

    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'id_mahasiswa');
    }

    public function dosen()
    {
        return $this->belongsTo(User::class, 'id_dosen');
    }

    public function dosenPendampingUniversitas()
    {
        return $this->belongsTo(User::class, 'id_dosen_pendamping_universitas');
    }

    public function reviewerAdministratif()
    {
        return $this->belongsTo(User::class, 'id_reviewer_administratif');
    }

    public function reviewerSubstantif1()
    {
        return $this->belongsTo(User::class, 'id_reviewer_substantif_1');
    }

    public function reviewerSubstantif2()
    {
        return $this->belongsTo(User::class, 'id_reviewer_substantif_2');
    }

    public function reviewerSubstantifSeleksi1()
    {
        return $this->belongsTo(User::class, 'id_reviewer_substantif_seleksi_1');
    }

    public function reviewerSubstantifSeleksi2()
    {
        return $this->belongsTo(User::class, 'id_reviewer_substantif_seleksi_2');
    }

    public function dokumen()
    {
        return $this->hasOne(Dokumen::class, 'id_proposal', 'id_proposal');
    }

    public function nilaiAdministratif()
    {
        return $this->hasMany(NilaiAdministratif::class, 'id_proposal', 'id_proposal');
    }

    public function nilaiSubstantif()
    {
        return $this->hasMany(NilaiSubstantif::class, 'id_proposal', 'id_proposal');
    }

    public function hasilSemiFinal()
    {
        return $this->hasOne(HasilSemiFinal::class, 'id_proposal', 'id_proposal');
    }

    public function hasilFinal()
    {
        return $this->hasOne(HasilFinal::class, 'id_proposal', 'id_proposal');
    }

    public function proposalRevisi()
    {
        return $this->hasMany(ProposalRevisi::class, 'id_proposal', 'id_proposal');
    }

    // Team accessors via JSONB metadata (not eager-loadable Eloquent relationships)
    public function getAnggotaTimAttribute()
    {
        if (!$this->team_id) return collect();
        return User::where('role', 'mahasiswa')
            ->whereRaw("metadata->>'team_id' = ?", [(string) $this->team_id])
            ->whereRaw("metadata->>'is_ketua' != 'true'")
            ->get();
    }

    public function getKetuaTimAttribute()
    {
        if (!$this->team_id) return null;
        return User::where('role', 'mahasiswa')
            ->whereRaw("metadata->>'team_id' = ?", [(string) $this->team_id])
            ->whereRaw("metadata->>'is_ketua' = 'true'")
            ->first();
    }

    public function getSemuaAnggotaTimAttribute()
    {
        if (!$this->team_id) return collect();
        return User::where('role', 'mahasiswa')
            ->whereRaw("metadata->>'team_id' = ?", [(string) $this->team_id])
            ->get();
    }

    // =========================================================================
    // Accessors
    // =========================================================================

    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Menunggu',
            'valid' => 'Valid',
            'tidak_valid' => 'Tidak Valid',
            'submitted' => 'Telah Diajukan',
            'review_administratif' => 'Review Administratif',
            'review_substantif' => 'Review Substantif',
            'revisi' => 'Revisi',
            'review_substantif_seleksi' => 'Review Substantif Seleksi',
            'hasil_semi_final' => 'Hasil Semi Final',
            'revisi_akhir' => 'Revisi Akhir',
            'validasi_akhir_dosen_univ' => 'Validasi Akhir Dosen Universitas',
            'pimpinan_pt' => 'Penilaian Pimpinan PT',
            'lolos_tingkat_universitas' => 'Lolos Tingkat Universitas',
            'tidak_lolos_tingkat_universitas' => 'Tidak Lolos Tingkat Universitas',
            'lolos_pimnas_pendanaan' => 'Lolos PIMNAS + Pendanaan',
            'lolos_pimnas_tidak_pendanaan' => 'Lolos PIMNAS, Tidak Lolos Pendanaan',
            'tidak_lolos_pimnas_lolos_pendanaan' => 'Tidak Lolos PIMNAS, Lolos Pendanaan',
            'lolos' => 'Lolos',
            'tidak_lolos' => 'Tidak Lolos',
        ];

        return $labels[$this->status] ?? ucfirst(str_replace('_', ' ', $this->status));
    }

    public function getSkimLabelAttribute()
    {
        $labels = [
            'RE' => 'PKM-RE (Riset Eksak)',
            'RSH' => 'PKM-RSH (Riset Sosial Humaniora)',
            'KC' => 'PKM-KC (Karsa Cipta)',
            'PM' => 'PKM-PM (Pengabdian Masyarakat)',
            'PI' => 'PKM-PI (Penerapan Iptek)',
            'K' => 'PKM-K (Kewirausahaan)',
            'KI' => 'PKM-KI (Karya Inovatif)',
            'VGK' => 'PKM-VGK (Video Gagasan Konstruktif)',
            'AI' => 'PKM-AI (Artikel Ilmiah)',
            'GFT' => 'PKM-GFT (Gagasan Futuristik Tertulis)',
        ];

        return $labels[$this->skim] ?? $this->skim;
    }

    public function getDanaFormattedAttribute()
    {
        return 'Rp ' . number_format($this->dana_diajukan ?? 0, 0, ',', '.');
    }

    public function getTeamSizeAttribute()
    {
        $count = 1; // ketua
        for ($i = 1; $i <= 4; $i++) {
            if (!empty($this->{"anggota{$i}_nim"})) $count++;
        }
        return $count;
    }

    public function canBeEdited(): bool
    {
        return in_array($this->status, ['draft', 'pending']);
    }

    public function canBeDeleted(): bool
    {
        return $this->status === 'draft';
    }

    public function isTeamComplete(): bool
    {
        return $this->team_size >= 3;
    }

    public function isTeamFull(): bool
    {
        return $this->team_size >= 5;
    }
}
