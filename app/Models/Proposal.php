<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proposal extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_proposal';

    protected $fillable = [
        'judul_proposal', 'tanggal_pengajuan', 'skim', 'status_validasi',
        'status_final', 'catatan', 'id_mahasiswa', 'id_dosen'
    ];

    // Relasi One-to-One ke Mahasiswa
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'id_mahasiswa');
    }

    // Relasi One-to-Many ke Dosen (Relasi Dosen Pembimbing)
    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'id_dosen');
    }

    // Relasi One-to-One ke Dokumen
    public function dokumen()
    {
        return $this->hasOne(Dokumen::class, 'id_proposal');
    }

    // Relasi One-to-One ke NilaiAdministratif
    public function nilaiAdministratif()
    {
        return $this->hasOne(NilaiAdministratif::class, 'id_proposal');
    }

    // Relasi One-to-Many ke NilaiSubstantif
    public function nilaiSubstantif()
    {
        return $this->hasMany(NilaiSubstantif::class, 'id_proposal');
    }

    // Relasi One-to-One ke HasilFinal
    public function hasilFinal()
    {
        return $this->hasOne(HasilFinal::class, 'id_proposal');
    }
}

