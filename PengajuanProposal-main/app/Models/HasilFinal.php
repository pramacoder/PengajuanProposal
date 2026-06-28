<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilFinal extends Model
{
    use HasFactory;

    protected $table = 'hasil_finals';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'status_pimnas', // Untuk Pimpinan PT
        'status_pendanaan', // Untuk Pimpinan PT
        'dana_yang_didapatkan', // Untuk Pimpinan PT
        'status_final', // Untuk Operator (lolos, tidak_lolos)
        'dana_yang_dapat_diberikan', // Untuk Operator
        'catatan_final',
        'nilai',
        'skor_per_kriteria',
        'id_proposal',
        'id_pimpinan_pt', // Untuk Pimpinan PT
        'id_pt' // Untuk Operator
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
        'dana_yang_didapatkan' => 'decimal:2',
        'dana_yang_dapat_diberikan' => 'decimal:2',
        'skor_per_kriteria' => 'array'
    ];

    // Relationships
    public function proposal()
    {
        return $this->belongsTo(Proposal::class, 'id_proposal', 'id_proposal');
    }

    public function pimpinanPt()
    {
        return $this->belongsTo(User::class, 'id_pimpinan_pt');
    }

    public function pt()
    {
        return $this->belongsTo(User::class, 'id_pt');
    }
}

