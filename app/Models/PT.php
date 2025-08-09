<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class PT extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'pts';
    protected $primaryKey = 'id_pt';  

    protected $fillable = [
        'nama_pt', 'no_hp_pt', 'email_pt',
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

    // Relasi Many-to-One ke RuangKontrol
    public function ruangKontrol()
    {
        return $this->belongsTo(RuangKontrol::class, 'id_ruang_kontrol');
    }
}


