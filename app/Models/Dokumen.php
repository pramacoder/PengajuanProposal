<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dokumen extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_dokumen';
    protected $fillable = [
        'path_file', 'tgl_upload', 'id_proposal', 'skim'
    ];

    // Relasi One-to-One ke Proposal
    public function proposal()
    {
        return $this->belongsTo(Proposal::class, 'id_proposal');
    }
}
