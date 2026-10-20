<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'identifier',
        'name',
        'email',
        'password',
        'role',
        'phone',
        'is_active',
        'metadata',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_active' => 'boolean',
            'metadata' => 'array',
            'email_verified_at' => 'datetime',
        ];
    }

    // =========================================================================
    // Role Check Methods
    // =========================================================================

    public function isMahasiswa(): bool
    {
        return $this->role === 'mahasiswa';
    }

    public function isDosen(): bool
    {
        return $this->role === 'dosen';
    }

    public function isReviewer(): bool
    {
        return $this->role === 'reviewer';
    }

    public function isOperator(): bool
    {
        return $this->role === 'operator';
    }

    public function isPimpinanPT(): bool
    {
        return $this->role === 'pimpinan_pt';
    }

    public function isOperatorOrPimpinan(): bool
    {
        return in_array($this->role, ['operator', 'pimpinan_pt']);
    }

    // =========================================================================
    // Metadata Accessors (role-specific)
    // =========================================================================

    public function getMetadataValue(string $key, $default = null)
    {
        return data_get($this->metadata, $key, $default);
    }

    public function setMetadataValue(string $key, $value): self
    {
        $metadata = $this->metadata ?? [];
        data_set($metadata, $key, $value);
        $this->metadata = $metadata;
        return $this;
    }

    // -- Mahasiswa accessors --
    public function getNim(): ?string
    {
        return $this->isMahasiswa() ? $this->identifier : null;
    }

    public function getProdiId(): ?int
    {
        return $this->getMetadataValue('prodi_id');
    }

    public function getFakultasId(): ?int
    {
        return $this->getMetadataValue('fakultas_id');
    }

    public function getProdiName(): ?string
    {
        return $this->getMetadataValue('prodi_name');
    }

    public function getFakultasName(): ?string
    {
        return $this->getMetadataValue('fakultas_name');
    }

    public function getTeamId(): ?string
    {
        return $this->getMetadataValue('team_id');
    }

    public function isKetuaTim(): bool
    {
        return (bool) $this->getMetadataValue('is_ketua', false);
    }

    public function getDosenPembimbingId(): ?int
    {
        return $this->getMetadataValue('id_dosen_pembimbing');
    }

    // -- Dosen accessors --
    public function getNidn(): ?string
    {
        return $this->isDosen() ? $this->identifier : null;
    }

    public function getNuptk(): ?string
    {
        return $this->getMetadataValue('nuptk');
    }

    public function getGelarDepan(): ?string
    {
        return $this->getMetadataValue('gelar_depan');
    }

    public function getGelarBelakang(): ?string
    {
        return $this->getMetadataValue('gelar_belakang');
    }

    public function getFullNameWithTitle(): string
    {
        $depan = $this->getGelarDepan();
        $belakang = $this->getGelarBelakang();
        $name = $this->name;

        if ($depan) $name = $depan . ' ' . $name;
        if ($belakang) $name = $name . ', ' . $belakang;

        return $name;
    }

    // -- Reviewer/Operator accessors --
    public function getNip(): ?string
    {
        return in_array($this->role, ['reviewer', 'operator', 'pimpinan_pt'])
            ? $this->identifier
            : null;
    }

    // =========================================================================
    // Backward Compatibility Accessors
    // =========================================================================

    public function getNamaMhsAttribute(): string { return $this->name; }
    public function getNamaDosenAttribute(): string { return $this->name; }
    public function getNamaReviewerAttribute(): string { return $this->name; }
    public function getNamaPtAttribute(): string { return $this->name; }

    public function getNimAttribute(): ?string { return $this->isMahasiswa() ? $this->identifier : null; }
    public function getNuptkAttribute(): ?string { return $this->getMetadataValue('nuptk') ?? $this->identifier; }
    public function getNipAttribute(): ?string { return $this->getNip(); }

    public function getEmailMhsAttribute(): string { return $this->email; }
    public function getEmailDosenAttribute(): string { return $this->email; }
    public function getEmailReviewerAttribute(): string { return $this->email; }
    public function getEmailPtAttribute(): string { return $this->email; }

    public function getNoHpMhsAttribute(): ?string { return $this->phone; }
    public function getNoTelpMhsAttribute(): ?string { return $this->phone; }
    public function getAlamatMhsAttribute(): ?string { return $this->getMetadataValue('alamat') ?? $this->getMetadataValue('alamat_mhs') ?? 'Kampus Bukit Jimbaran, Badung, Bali'; }
    public function getAlamatAttribute(): ?string { return $this->alamat_mhs; }
    public function getNoHpDosenAttribute(): ?string { return $this->phone; }
    public function getNoHpReviewerAttribute(): ?string { return $this->phone; }
    public function getNoHpPtAttribute(): ?string { return $this->phone; }

    public function getProdiMhsAttribute(): ?string { return $this->getProdiName(); }
    public function getFakultasMhsAttribute(): ?string { return $this->getFakultasName(); }

    public function getTahunMasukAttribute(): ?string
    {
        if ($val = $this->getMetadataValue('tahun_masuk')) {
            return (string) $val;
        }
        if ($this->identifier && strlen($this->identifier) >= 2 && is_numeric(substr($this->identifier, 0, 2))) {
            return '20' . substr($this->identifier, 0, 2);
        }
        return '2020';
    }

    public function getSemesterAttribute(): ?string
    {
        if ($val = $this->getMetadataValue('semester')) {
            return (string) $val;
        }
        $tahunMasuk = (int) $this->tahun_masuk;
        if ($tahunMasuk > 2000) {
            $currentYear = (int) date('Y');
            $diff = max(1, ($currentYear - $tahunMasuk) * 2);
            return 'Semester ' . min(8, $diff);
        }
        return 'Semester 8';
    }

    public function getIpkAttribute(): ?string
    {
        if ($val = $this->getMetadataValue('ipk')) {
            return number_format((float) $val, 2, '.', '');
        }
        return '3.82';
    }

    public function getProdiAttribute()
    {
        if ($prodiId = $this->getProdiId()) {
            $prodi = Prodi::with('fakultas')->find($prodiId);
            if ($prodi) return $prodi;
        }

        if ($prodiName = $this->getProdiName()) {
            $prodi = Prodi::with('fakultas')->where('nama_prodi', $prodiName)->first();
            if ($prodi) return $prodi;
        }

        $fakultasName = $this->getFakultasName() ?? 'Fakultas Teknik';
        $prodiName = $this->getProdiName() ?? 'Teknologi Informasi';

        $fakultasObj = new \stdClass();
        $fakultasObj->nama_fakultas = $fakultasName;

        $prodiObj = new \stdClass();
        $prodiObj->nama_prodi = $prodiName;
        $prodiObj->fakultas = $fakultasObj;

        return $prodiObj;
    }

    public function getIdMahasiswaAttribute(): int { return $this->id; }
    public function getIdDosenAttribute(): int { return $this->id; }
    public function getIdReviewerAttribute(): int { return $this->id; }
    public function getIdPtAttribute(): int { return $this->id; }

    public function getNidnAttribute(): ?string { return $this->isDosen() ? $this->identifier : ($this->getMetadataValue('nidn') ?? $this->identifier); }
    public function getGelarDepanAttribute(): ?string { return $this->getMetadataValue('gelar_depan'); }
    public function getGelarBelakangAttribute(): ?string { return $this->getMetadataValue('gelar_belakang'); }
    public function getBidangKeahlianAttribute(): ?string { return $this->getMetadataValue('bidang_keahlian'); }

    // =========================================================================
    // Relationships (role-specific)
    // =========================================================================

    // -- Mahasiswa relationships --
    public function proposal()
    {
        return $this->hasOne(Proposal::class, 'id_mahasiswa');
    }

    public function proposals()
    {
        return $this->hasMany(Proposal::class, 'id_mahasiswa');
    }

    public function dosenPembimbing()
    {
        return $this->belongsTo(User::class, 'id_dosen_pembimbing_meta');
    }

    // -- Dosen relationships --
    public function proposalsDosen()
    {
        return $this->hasMany(Proposal::class, 'id_dosen');
    }

    public function proposalsUniversitas()
    {
        return $this->hasMany(Proposal::class, 'id_dosen_pendamping_universitas');
    }

    // -- Reviewer relationships --
    public function nilaiAdministratifs()
    {
        return $this->hasMany(NilaiAdministratif::class, 'id_reviewer');
    }

    public function nilaiSubstantifs()
    {
        return $this->hasMany(NilaiSubstantif::class, 'id_reviewer');
    }

    // -- Operator relationships --
    public function ruangKontrol()
    {
        return $this->hasMany(RuangKontrol::class, 'id_pt');
    }

    // =========================================================================
    // Scopes
    // =========================================================================

    public function scopeRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeMahasiswa($query)
    {
        return $query->where('role', 'mahasiswa');
    }

    public function scopeDosen($query)
    {
        return $query->where('role', 'dosen');
    }

    public function scopeReviewer($query)
    {
        return $query->where('role', 'reviewer');
    }

    public function scopeOperator($query)
    {
        return $query->where('role', 'operator');
    }

    public function scopePimpinanPT($query)
    {
        return $query->where('role', 'pimpinan_pt');
    }

    // =========================================================================
    // Static Finders
    // =========================================================================

    public static function findByIdentifier(string $identifier): ?self
    {
        return static::where('identifier', $identifier)->first();
    }

    public static function findMahasiswaByNim(string $nim): ?self
    {
        return static::where('identifier', $nim)->where('role', 'mahasiswa')->first();
    }

    public static function findDosenByNuptk(string $nuptk): ?self
    {
        return static::where('role', 'dosen')
            ->where(function ($q) use ($nuptk) {
                $q->where('identifier', $nuptk)
                  ->orWhereJsonContains('metadata->nuptk', $nuptk);
            })->first();
    }
}
