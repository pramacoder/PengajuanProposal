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
        'password', 'role', 'is_active', 'email_verified_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
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
}



