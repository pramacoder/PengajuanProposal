<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiAdministratif extends Model
{
    use HasFactory;

    protected $table = 'nilai_administratifs';
    protected $primaryKey = 'id';
    public $timestamps = true;

    protected $fillable = [
        'note_administratif',
        'checklist',
        'id_proposal',
        'id_reviewer'
    ];

    protected $casts = [
        'checklist' => 'array'
    ];

    // Relationships
    public function proposal()
    {
        return $this->belongsTo(Proposal::class, 'id_proposal', 'id_proposal');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'id_reviewer');
    }
}

