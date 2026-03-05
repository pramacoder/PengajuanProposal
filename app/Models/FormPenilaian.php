<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormPenilaian extends Model
{
    use HasFactory;

    protected $table = 'form_penilaian';

    protected $fillable = [
        'nama_form',
        'jenis_form',
        'skim',
        'config',
        'fields', // Added
        'is_active',
        'tahun_ajaran',
        'mongo_config_id',
        'created_by',
    ];

    protected $casts = [
        'config' => 'array',
        'fields' => 'array', // Added
        'is_active' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class , 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForSkim($query, string $skim)
    {
        return $query->where('skim', $skim)->orWhereNull('skim');
    }
}
