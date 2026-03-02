<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SimbelmawaReport extends Model
{
    use HasFactory;

    protected $table = 'simbelmawa_reports';

    protected $fillable = [
        'tahun_ajaran',
        'id_ruang_kontrol',
        'jumlah_proposal_tervalidasi_pimpinan_pt',
        'jumlah_proposal_dapat_pendanaan',
        'total_dana_pendanaan',
        'jumlah_proposal_lolos_pimnas',
        'judul_proposal_lolos_pimnas',
        'jumlah_prestasi',
        'prestasi',
        'mongo_report_id',
        'created_by',
    ];

    protected $casts = [
        'judul_proposal_lolos_pimnas' => 'array',
        'prestasi' => 'array',
        'total_dana_pendanaan' => 'decimal:2',
    ];

    public function ruangKontrol()
    {
        return $this->belongsTo(RuangKontrol::class, 'id_ruang_kontrol', 'id_ruang_kontrol');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
