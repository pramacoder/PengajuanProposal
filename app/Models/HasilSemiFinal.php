<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilSemiFinal extends Model
{
    use HasFactory;

    protected $table = 'hasil_semi_finals';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'status_final',
        'catatan_final',
        'nilai',
        'skor_per_kriteria',
        'dana_yang_dapat_diberikan',
        'id_dosen_pendamping_universitas',
        'id_proposal',
        'id_pt',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
        'dana_yang_dapat_diberikan' => 'decimal:2',
        'skor_per_kriteria' => 'array',
    ];

    public function proposal()
    {
        return $this->belongsTo(Proposal::class, 'id_proposal', 'id_proposal');
    }

    public function pt()
    {
        return $this->belongsTo(User::class, 'id_pt');
    }

    public function dosenPendampingUniversitas()
    {
        return $this->belongsTo(User::class, 'id_dosen_pendamping_universitas');
    }
}
