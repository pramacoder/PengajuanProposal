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
        'password', 'role', 'is_active'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    // Relasi One-to-Many ke RuangKontrol
    public function ruangKontrol()
    {
        return $this->hasMany(RuangKontrol::class, 'id_pt');
    }
}


