<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiAdministratif extends Model
{
    use HasFactory;

    protected $fillable = [
        'note_administratif', 'checklist', 'id_proposal', 'id_reviewer'
    ];
    protected $casts = [
        'checklist' => 'array', 
    ];
    // Relasi One-to-One ke Proposal
    public function proposal()
    {
        return $this->belongsTo(Proposal::class, 'id_proposal');
    }

    // Relasi One-to-One ke Reviewer
    public function reviewer()
    {
        return $this->belongsTo(Reviewer::class, 'id_reviewer');
    }
}

