<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiSubstantif extends Model
{
    use HasFactory;

    protected $fillable = [
        'hasil_substantif', 'note_substantif', 'id_proposal', 'id_reviewer'
    ];

    // Relasi One-to-Many ke Proposal
    public function proposal()
    {
        return $this->belongsTo(Proposal::class, 'id_proposal');
    }

    // Relasi One-to-Many ke Reviewer
    public function reviewer()
    {
        return $this->belongsTo(Reviewer::class, 'id_reviewer');
    }
}

