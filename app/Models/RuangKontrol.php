<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RuangKontrol extends Model
{
    use HasFactory;

    protected $table = 'ruang_kontrols';
    protected $primaryKey = 'id_ruang_kontrol';

    protected $fillable = [
        'status_perbaikan',
        'status_pendaftaran',
        'tanggal_pendaftaran_mulai',
        'tanggal_pendaftaran_selesai',
        'tanggal_perbaikan_mulai',
        'tanggal_perbaikan_selesai',
        'id_pt'
    ];

    protected $casts = [
        'tanggal_pendaftaran_mulai' => 'date',
        'tanggal_pendaftaran_selesai' => 'date',
        'tanggal_perbaikan_mulai' => 'date',
        'tanggal_perbaikan_selesai' => 'date',
    ];

    // Relasi ke PT
    public function pt()
    {
        return $this->belongsTo(PT::class, 'id_pt');
    }
}


