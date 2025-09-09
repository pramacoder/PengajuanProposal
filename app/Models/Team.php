<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Team - Menyimpan anggota tim untuk setiap proposal
 * 
 * Konsep:
 * - 1 Proposal = 1 Team
 * - 1 Team = 3-5 anggota (minimal 3, maksimal 5)
 * - Setiap record = 1 anggota tim
 * - id_proposal mengelompokkan anggota menjadi 1 tim
 */
class Team extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_team';
    
    protected $fillable = [
        'id_proposal', 'id_mahasiswa', 'nama', 'nim', 'prodi', 'fakultas', 'email', 'no_hp', 'role', 'status'
    ];

    protected $casts = [
        'status' => 'string',
        'role' => 'string'
    ];

    /**
     * Relasi ke Proposal (tim ini adalah bagian dari proposal mana)
     */
    public function proposal()
    {
        return $this->belongsTo(Proposal::class, 'id_proposal', 'id_proposal');
    }

    /**
     * Relasi ke Mahasiswa (jika anggota tim adalah mahasiswa terdaftar)
     */
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'id_mahasiswa', 'id_mahasiswa');
    }

    // Scope untuk query yang sering digunakan
    public function scopeKetua($query)
    {
        return $query->where('role', 'ketua');
    }

    public function scopeAnggota($query)
    {
        return $query->where('role', '!=', 'ketua');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Helper methods untuk mendapatkan anggota tim
    public static function getKetua($proposalId)
    {
        return self::where('id_proposal', $proposalId)
                   ->where('role', 'ketua')
                   ->first();
    }

    public static function getAnggota($proposalId)
    {
        return self::where('id_proposal', $proposalId)
                   ->where('role', '!=', 'ketua')
                   ->orderBy('role')
                   ->get();
    }

    public static function getAllMembers($proposalId)
    {
        return self::where('id_proposal', $proposalId)
                   ->orderByRaw("CASE WHEN role = 'ketua' THEN 1 ELSE 2 END")
                   ->orderBy('role')
                   ->get();
    }

    public static function getTeamSize($proposalId)
    {
        return self::where('id_proposal', $proposalId)
                   ->where('status', 'active')
                   ->count();
    }

    /**
     * Cek apakah NIM sudah terdaftar di proposal lain
     */
    public static function isNimInOtherProposal($nim, $excludeProposalId = null)
    {
        $query = self::where('nim', $nim);
        
        if ($excludeProposalId) {
            $query->where('id_proposal', '!=', $excludeProposalId);
        }
        
        return $query->exists();
    }

    /**
     * Dapatkan role yang tersedia
     */
    public static function getValidRoles()
    {
        return ['ketua', 'anggota1', 'anggota2', 'anggota3', 'anggota4'];
    }

    /**
     * Dapatkan role berikutnya yang tersedia untuk proposal
     */
    public static function getNextAvailableRole($proposalId)
    {
        $existingRoles = self::where('id_proposal', $proposalId)
                             ->pluck('role')
                             ->toArray();
        
        $validRoles = self::getValidRoles();
        
        foreach ($validRoles as $role) {
            if (!in_array($role, $existingRoles)) {
                return $role;
            }
        }
        
        return null; // Tim sudah penuh
    }

    /**
     * Cek apakah tim sudah lengkap (minimal 3 anggota)
     */
    public static function isTeamComplete($proposalId)
    {
        return self::getTeamSize($proposalId) >= 3;
    }

    /**
     * Cek apakah tim sudah penuh (maksimal 5 anggota)
     */
    public static function isTeamFull($proposalId)
    {
        return self::getTeamSize($proposalId) >= 5;
    }

    /**
     * Dapatkan anggota berdasarkan role
     */
    public static function getMemberByRole($proposalId, $role)
    {
        return self::where('id_proposal', $proposalId)
                   ->where('role', $role)
                   ->first();
    }

    // Accessors untuk label yang lebih user-friendly
    public function getRoleLabelAttribute()
    {
        $labels = [
            'ketua' => 'Ketua Tim',
            'anggota1' => 'Anggota 1',
            'anggota2' => 'Anggota 2',
            'anggota3' => 'Anggota 3',
            'anggota4' => 'Anggota 4'
        ];
        
        return $labels[$this->role] ?? $this->role;
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'active' => 'Aktif',
            'inactive' => 'Tidak Aktif'
        ];
        
        return $labels[$this->status] ?? $this->status;
    }
}
