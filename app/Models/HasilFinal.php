<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilFinal extends Model
{
    use HasFactory;

    protected $fillable = [
        'status_final', 'catatan_final', 'id_proposal', 'id_pt'
    ];

    // Relasi Many-to-One ke PT (setiap HasilFinal terkait dengan satu PT)
    public function pt()
    {
        return $this->belongsTo(PT::class, 'id_pt');
    }

    // Relasi One-to-One ke Proposal
    public function proposal()
    {
        return $this->belongsTo(Proposal::class, 'id_proposal');
    }
}

