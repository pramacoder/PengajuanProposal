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
        'skor_per_kriteria',
        'total_nilai',
        'nilai_akhir',
        'id_proposal',
        'id_reviewer'
    ];

    protected $casts = [
        'total_nilai' => 'decimal:2',
        'nilai_akhir' => 'decimal:2'
    ];
    
    /**
     * Set attribute untuk skor_per_kriteria
     * Memastikan data di-encode sebagai JSON dengan benar
     */
    public function setSkorPerKriteriaAttribute($value)
    {
        if (is_array($value) && !empty($value)) {
            // Pastikan array di-encode sebagai JSON
            // Gunakan JSON_NUMERIC_CHECK untuk memastikan angka tetap sebagai angka
            $this->attributes['skor_per_kriteria'] = json_encode($value, JSON_NUMERIC_CHECK);
        } elseif ($value === null || $value === '') {
            $this->attributes['skor_per_kriteria'] = null;
        } else {
            $this->attributes['skor_per_kriteria'] = $value;
        }
    }
    
    /**
     * Get attribute untuk skor_per_kriteria
     * Memastikan data di-decode dengan benar
     */
    public function getSkorPerKriteriaAttribute($value)
    {
        if (is_null($value) || $value === '') {
            return [];
        }
        
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                // Pastikan hasil adalah array dengan key numerik
                if (is_array($decoded)) {
                    return $decoded;
                }
            }
            return [];
        }
        
        return is_array($value) ? $value : [];
    }

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

