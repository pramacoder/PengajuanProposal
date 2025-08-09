<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Reviewer extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'reviewers';
    protected $primaryKey = 'id_reviewer';

    protected $fillable = [
        'nama_reviewer', 'no_hp_reviewer', 'email_reviewer',
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

    // Relasi One-to-Many ke NilaiAdministratif
    public function nilaiAdministratifs()
    {
        return $this->hasMany(NilaiAdministratif::class, 'id_reviewer');
    }

    // Relasi One-to-Many ke NilaiSubstantif
    public function nilaiSubstantifs()
    {
        return $this->hasMany(NilaiSubstantif::class, 'id_reviewer');
    }
}