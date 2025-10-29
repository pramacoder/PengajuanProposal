<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Mahasiswa extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'id_mahasiswa';  

    protected $fillable = [
        'nim', 'nama_mhs', 'prodi_mhs', 'fakultas_mhs', 'no_hp_mhs', 'email_mhs',
        'password', 'role', 'is_active', 'email_verified_at', 'id_dosen_pembimbing',
        'team_id', 'is_ketua'
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'is_ketua' => 'boolean',
    ];

    // Relasi One-to-Many dengan Proposal
    public function proposal()
    {
        return $this->hasOne(Proposal::class, 'id_mahasiswa');
    }

    // Relasi untuk proposal tahun terbaru
    public function proposalTahunTerbaru()
    {
        return $this->hasOne(Proposal::class, 'id_mahasiswa')->where('tahun_ajaran', '2024/2025');
    }

    // Relasi untuk semua proposal
    public function proposals()
    {
        return $this->hasMany(Proposal::class, 'id_mahasiswa');
    }

    // Relasi ke anggota tim (self-reference berdasarkan team_id)
    public function anggotaTim()
    {
        return $this->hasMany(Mahasiswa::class, 'team_id', 'team_id')
                   ->where('id_mahasiswa', '!=', $this->id_mahasiswa);
    }

    // Relasi ke ketua tim (self-reference berdasarkan team_id)
    public function ketuaTim()
    {
        return $this->hasOne(Mahasiswa::class, 'team_id', 'team_id')
                   ->where('is_ketua', true);
    }

    // Relasi ke proposal sebagai ketua tim
    public function proposalAsKetua()
    {
        return $this->hasOne(Proposal::class, 'team_id', 'team_id');
    }

    // Relasi ke proposal sebagai anggota tim
    public function proposalsAsMember()
    {
        return $this->hasMany(Proposal::class, 'team_id', 'team_id');
    }

    // Relasi ke Prodi berdasarkan nama_prodi
    public function prodi()
    {
        return $this->belongsTo(Prodi::class, 'prodi_mhs', 'nama_prodi');
    }

    // Relasi ke Fakultas berdasarkan nama_fakultas
    public function fakultas()
    {
        return $this->belongsTo(Fakultas::class, 'fakultas_mhs', 'nama_fakultas');
    }

    // Relasi One-to-One dengan Dosen (Dosen Pembimbing)
    public function dosenPembimbing()
    {
        return $this->belongsTo(Dosen::class, 'id_dosen_pembimbing', 'id_dosen');
    }

    // Accessor untuk nama_mahasiswa (kompatibilitas dengan view)
    public function getNamaMahasiswaAttribute()
    {
        return $this->nama_mhs;
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'id_mahasiswa';
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getAttribute($this->getAuthIdentifierName());
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }


}



