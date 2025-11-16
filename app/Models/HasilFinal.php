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
        'status_final',
        'catatan_final',
        'nilai',
        'skor_per_kriteria',
        'dana_yang_dapat_diberikan',
        'id_proposal',
        'id_pt'
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
        'dana_yang_dapat_diberikan' => 'decimal:2',
        'skor_per_kriteria' => 'array'
    ];

    // Relationships
    public function proposal()
    {
        return $this->belongsTo(Proposal::class, 'id_proposal', 'id_proposal');
    }

    public function pt()
    {
        return $this->belongsTo(PT::class, 'id_pt', 'id_pt');
    }
}

