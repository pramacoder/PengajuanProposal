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
        'password', 'role', 'is_active', 'email_verified_at', 'id_dosen_pembimbing'
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    // Relasi One-to-Many dengan Proposal
    public function proposal()
    {
        return $this->hasOne(Proposal::class, 'id_mahasiswa');
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



