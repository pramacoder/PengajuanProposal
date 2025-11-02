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
        'status_final', 'status', 'catatan', 'id_mahasiswa', 'id_dosen', 'team_id',
        'dosen_pembimbing', 'dana_diajukan', 'tahun_ajaran', 'tanggal_validasi',
        'id_reviewer_administratif', 'id_reviewer_substantif_1', 'id_reviewer_substantif_2',
        'path_review_dosen', 'nama_file_review_dosen', 'tanggal_review_dosen',
        
        // Data ketua tim (untuk kompatibilitas dengan sistem lama)
        'ketua_nama', 'ketua_nim', 'ketua_prodi', 'ketua_fakultas', 'ketua_email', 'ketua_no_hp',
        
        // Data anggota 1
        'anggota1_nama', 'anggota1_nim', 'anggota1_prodi', 'anggota1_fakultas', 'anggota1_email', 'anggota1_no_hp',
        
        // Data anggota 2
        'anggota2_nama', 'anggota2_nim', 'anggota2_prodi', 'anggota2_fakultas', 'anggota2_email', 'anggota2_no_hp',
        
        // Data anggota 3
        'anggota3_nama', 'anggota3_nim', 'anggota3_prodi', 'anggota3_fakultas', 'anggota3_email', 'anggota3_no_hp',
        
        // Data anggota 4
        'anggota4_nama', 'anggota4_nim', 'anggota4_prodi', 'anggota4_fakultas', 'anggota4_email', 'anggota4_no_hp'
    ];

    // Relasi One-to-One ke Mahasiswa (pengaju proposal)
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'id_mahasiswa', 'id_mahasiswa');
    }

    // Relasi ke anggota tim (mahasiswa dengan team_id yang sama)
    public function anggotaTim()
    {
        return $this->hasMany(Mahasiswa::class, 'team_id', 'team_id')
                   ->where('is_ketua', false);
    }

    // Relasi ke ketua tim (mahasiswa dengan team_id yang sama dan is_ketua = true)
    public function ketuaTim()
    {
        return $this->hasOne(Mahasiswa::class, 'team_id', 'team_id')
                   ->where('is_ketua', true);
    }

    // Relasi ke semua anggota tim (termasuk ketua)
    public function semuaAnggotaTim()
    {
        return $this->hasMany(Mahasiswa::class, 'team_id', 'team_id');
    }

    // Relasi One-to-Many ke Dosen (Relasi Dosen Pendamping)
    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'id_dosen', 'id_dosen');
    }

    // Relasi ke Reviewer Administratif
    public function reviewerAdministratif()
    {
        return $this->belongsTo(Reviewer::class, 'id_reviewer_administratif', 'id_reviewer');
    }

    // Relasi ke Reviewer Substantif 1
    public function reviewerSubstantif1()
    {
        return $this->belongsTo(Reviewer::class, 'id_reviewer_substantif_1', 'id_reviewer');
    }

    // Relasi ke Reviewer Substantif 2
    public function reviewerSubstantif2()
    {
        return $this->belongsTo(Reviewer::class, 'id_reviewer_substantif_2', 'id_reviewer');
    }

    // Relasi One-to-One ke Dokumen
    public function dokumen()
    {
        return $this->hasOne(Dokumen::class, 'id_proposal', 'id_proposal');
    }

    // Relasi One-to-Many ke NilaiAdministratif
    public function nilaiAdministratif()
    {
        return $this->hasMany(NilaiAdministratif::class, 'id_proposal', 'id_proposal');
    }

    // Relasi One-to-Many ke NilaiSubstantif
    public function nilaiSubstantif()
    {
        return $this->hasMany(NilaiSubstantif::class, 'id_proposal', 'id_proposal');
    }

    // Relasi One-to-One ke HasilFinal
    public function hasilFinal()
    {
        return $this->hasOne(HasilFinal::class, 'id_proposal', 'id_proposal');
    }

    // Relasi One-to-Many ke ProposalRevisi
    public function proposalRevisi()
    {
        return $this->hasMany(ProposalRevisi::class, 'id_proposal', 'id_proposal');
    }

    /**
     * Relasi ke ketua tim (1 proposal memiliki 1 ketua) - DEPRECATED, gunakan ketuaTim()
     */
    public function ketua()
    {
        return $this->ketuaTim();
    }

    /**
     * Relasi ke anggota tim (1 proposal memiliki banyak anggota non-ketua) - DEPRECATED, gunakan anggotaTim()
     */
    public function anggota()
    {
        return $this->anggotaTim();
    }

    /**
     * Relasi ke semua anggota tim termasuk ketua - DEPRECATED, gunakan semuaAnggotaTim()
     */
    public function allMembers()
    {
        return $this->semuaAnggotaTim();
    }

    /**
     * Accessor untuk mendapatkan jumlah anggota tim
     */
    public function getTeamSizeAttribute()
    {
        return $this->semuaAnggotaTim()->count();
    }

    /**
     * Cek apakah tim sudah lengkap (minimal 3 anggota)
     */
    public function isTeamComplete()
    {
        return $this->team_size >= 3;
    }

    /**
     * Cek apakah tim sudah penuh (maksimal 5 anggota)
     */
    public function isTeamFull()
    {
        return $this->team_size >= 5;
    }

    // Method untuk mendapatkan status label
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
            'lolos' => 'Lolos',
            'tidak_lolos' => 'Tidak Lolos'
        ];

        return $labels[$this->status] ?? $this->status;
    }

    // Method untuk mendapatkan skim label
    public function getSkimLabelAttribute()
    {
        $labels = [
            'RE' => 'PKM-RE (Riset Eksak)',
            'RSH' => 'PKM-RSH (Riset Sosial Humaniora)',
            'KC' => 'PKM-KC (Karsa Cipta)',
            'PM' => 'PKM-PM (Pengabdian Masyarakat)',
            'PI' => 'PKM-PI (Penerapan Iptek)',
            'K' => 'PKM-K (Kewirausahaan)',
            'KI' => 'PKM-KI (Karsa Cipta)',
            'VGK' => 'PKM-VGK (Video Gagasan Konstruktif)',
            'AI' => 'PKM-AI (Artikel Ilmiah)',
            'GFT' => 'PKM-GFT (Gagasan Futuristik Tertulis)'
        ];

        return $labels[$this->skim] ?? $this->skim;
    }

    // Method untuk format dana
    public function getDanaFormattedAttribute()
    {
        return 'Rp ' . number_format($this->dana_diajukan, 0, ',', '.');
    }

    // Method untuk cek apakah proposal bisa diedit
    public function canBeEdited()
    {
        return in_array($this->status, ['draft', 'pending']);
    }

    // Method untuk cek apakah proposal bisa dihapus
    public function canBeDeleted()
    {
        return $this->status === 'draft';
    }

    /**
     * Dapatkan ketua tim
     */
    public function getKetua()
    {
        return $this->ketuaTim;
    }

    /**
     * Dapatkan anggota tim (non-ketua)
     */
    public function getAnggota()
    {
        return $this->anggotaTim;
    }

    /**
     * Dapatkan semua anggota tim
     */
    public function getAllAnggota()
    {
        return $this->semuaAnggotaTim;
    }

    /**
     * Cek apakah NIM adalah anggota tim ini
     */
    public function isMember($nim)
    {
        return $this->semuaAnggotaTim()->where('nim', $nim)->exists();
    }

    /**
     * Cek apakah NIM adalah ketua tim ini
     */
    public function isKetua($nim)
    {
        return $this->ketuaTim()->where('nim', $nim)->exists();
    }
}

