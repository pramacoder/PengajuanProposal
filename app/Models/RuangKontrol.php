<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RuangKontrol extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';  
    public $incrementing = true;  
    protected $keyType = 'int';  

    protected $fillable = [
        'status_perbaikan', 'status_pendaftaran'
    ];

    // Relasi One-to-Many ke PT
    public function pts()
    {
        return $this->hasMany(PT::class, 'id_ruang_kontrol');
    }
}


