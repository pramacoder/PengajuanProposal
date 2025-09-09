<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiSubstantif extends Model
{
    use HasFactory;

    protected $table = 'nilai_substantifs';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'note_substantif',
        'id_proposal',
        'id_reviewer'
    ];

    // Relationships
    public function proposal()
    {
        return $this->belongsTo(Proposal::class, 'id_proposal', 'id_proposal');
    }

    public function reviewer()
    {
        return $this->belongsTo(Reviewer::class, 'id_reviewer', 'id_reviewer');
    }
}

