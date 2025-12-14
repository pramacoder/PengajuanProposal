<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProposalRevisi extends Model
{
    use HasFactory;

    protected $table = 'proposal_revisi';
    protected $primaryKey = 'id_revisi';
    public $timestamps = true;

    protected $fillable = [
        'id_proposal',
        'nama_file',
        'path_file',
        'tanggal_submit',
        'jenis_revisi'
    ];

    protected $casts = [
        'tanggal_submit' => 'datetime',
    ];

    // Relationships
    public function proposal()
    {
        return $this->belongsTo(Proposal::class, 'id_proposal', 'id_proposal');
    }

    // Accessor untuk URL file
    public function getFileUrlAttribute()
    {
        return Storage::disk('public')->url($this->path_file);
    }

    // Accessor untuk ukuran file yang readable (menggunakan file system)
    public function getUkuranFileReadableAttribute()
    {
        if (!$this->fileExists()) {
            return 'File tidak ditemukan';
        }
        
        $bytes = Storage::disk('public')->size($this->path_file);
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    // Status revisi dikelola melalui relasi dengan tabel proposals dan hasil_finals

    // Method untuk cek apakah file masih ada
    public function fileExists()
    {
        return Storage::disk('public')->exists($this->path_file);
    }

    // Method untuk hapus file
    public function deleteFile()
    {
        if ($this->fileExists()) {
            Storage::disk('public')->delete($this->path_file);
        }
    }
}