<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RuangKontrol extends Model
{
    use HasFactory;

    protected $table = 'ruang_kontrols';
    protected $primaryKey = 'id_ruang_kontrol';

    const PHASES = ['pendaftaran', 'review', 'perbaikan', 'penilaian_akhir'];

    const PHASE_LABELS = [
        'pendaftaran' => 'Fase 1 — Pengajuan Proposal',
        'review' => 'Fase 2 — Review',
        'perbaikan' => 'Fase 3 — Revisi & Seleksi',
        'penilaian_akhir' => 'Fase 4 — Penilaian Akhir',
    ];

    const PHASE_ICONS = [
        'pendaftaran' => 'fa-file-upload',
        'review' => 'fa-search',
        'perbaikan' => 'fa-edit',
        'penilaian_akhir' => 'fa-trophy',
    ];

    const PHASE_DESCRIPTIONS = [
        'pendaftaran' => 'Mahasiswa submit proposal, dosen pendamping validasi',
        'review' => 'Operator assign reviewer, reviewer review administratif & substantif',
        'perbaikan' => 'Mahasiswa revisi, reviewer seleksi, hasil semi final, validasi dosen universitas',
        'penilaian_akhir' => 'Revisi akhir, penilaian Pimpinan PT, pengumuman hasil final',
    ];

    protected $fillable = [
        'status_pendaftaran',
        'status_review',
        'status_perbaikan',
        'status_penilaian_akhir',
        'tanggal_pendaftaran_mulai',
        'tanggal_pendaftaran_selesai',
        'tanggal_review_mulai',
        'tanggal_review_selesai',
        'tanggal_perbaikan_mulai',
        'tanggal_perbaikan_selesai',
        'tanggal_penilaian_akhir_mulai',
        'tanggal_penilaian_akhir_selesai',
        'tanggal_review_pertama_mulai',
        'tanggal_review_pertama_selesai',
        'tahun_ajaran',
        'nama_history',
        'is_active',
        'dana_min_operator',
        'dana_max_operator',
        'dana_min_belmawa',
        'dana_max_belmawa',
        'id_pt',
    ];

    protected $casts = [
        'tanggal_pendaftaran_mulai' => 'date',
        'tanggal_pendaftaran_selesai' => 'date',
        'tanggal_review_mulai' => 'date',
        'tanggal_review_selesai' => 'date',
        'tanggal_perbaikan_mulai' => 'date',
        'tanggal_perbaikan_selesai' => 'date',
        'tanggal_penilaian_akhir_mulai' => 'date',
        'tanggal_penilaian_akhir_selesai' => 'date',
        'tanggal_review_pertama_mulai' => 'date',
        'tanggal_review_pertama_selesai' => 'date',
        'dana_min_operator' => 'decimal:2',
        'dana_max_operator' => 'decimal:2',
        'dana_min_belmawa' => 'decimal:2',
        'dana_max_belmawa' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function pt()
    {
        return $this->belongsTo(User::class, 'id_pt');
    }

    public function getStatusForPhase(string $phase): string
    {
        return match ($phase) {
            'pendaftaran' => $this->status_pendaftaran,
            'review' => $this->status_review,
            'perbaikan' => $this->status_perbaikan,
            'penilaian_akhir' => $this->status_penilaian_akhir,
            default => 'tertutup',
        };
    }

    public function getActivePhase(): ?string
    {
        foreach (self::PHASES as $phase) {
            if ($this->getStatusForPhase($phase) === 'terbuka') {
                return $phase;
            }
        }
        return null;
    }    public function getDateRange(string $phase): array
    {
        return match ($phase) {
            'pendaftaran' => [$this->tanggal_pendaftaran_mulai, $this->tanggal_pendaftaran_selesai],
            'review' => [$this->tanggal_review_mulai, $this->tanggal_review_selesai],
            'perbaikan' => [$this->tanggal_perbaikan_mulai, $this->tanggal_perbaikan_selesai],
            'penilaian_akhir' => [$this->tanggal_penilaian_akhir_mulai, $this->tanggal_penilaian_akhir_selesai],
            default => [null, null],
        };
    }
}