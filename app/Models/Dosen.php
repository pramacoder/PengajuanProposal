<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Dosen extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'id_dosen';
    
    protected $table = 'dosens';

    protected $fillable = [
        'nuptk', 'nama_dosen', 'gelar_depan', 'gelar_belakang', 'email_dosen', 'no_hp_dosen',
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

    // Relasi One-to-Many ke Proposal (sebagai Dosen Pendamping)
    public function proposals()
    {
        return $this->hasMany(Proposal::class, 'id_dosen');
    }

    // Relasi One-to-Many ke Mahasiswa (sebagai Dosen Pembimbing)
    public function mahasiswaBimbingan()
    {
        return $this->hasMany(Mahasiswa::class, 'id_dosen_pembimbing', 'id_dosen');
    }
}

