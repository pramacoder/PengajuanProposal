<?php

namespace App\Repositories\Firebase;

class RuangKontrolHistoryRepository extends BaseFirestoreRepository
{
    protected string $collection = 'ruang_kontrol_history';

    public function getByRuangKontrolId(int $ruangKontrolId): ?array
    {
        $results = $this->where('ruang_kontrol_id', '=', $ruangKontrolId);
        return $results[0] ?? null;
    }

    public function getByTahunAjaran(string $tahunAjaran): array
    {
        return $this->where('tahun_ajaran', '=', $tahunAjaran);
    }

    public function createHistory(int $ruangKontrolId, array $data): string
    {
        $history = [
            'ruang_kontrol_id' => $ruangKontrolId,
            'tahun_ajaran' => $data['tahun_ajaran'],
            'fase_pendaftaran' => $data['fase_pendaftaran'] ?? [],
            'fase_review' => $data['fase_review'] ?? [],
            'fase_perbaikan' => $data['fase_perbaikan'] ?? [],
            'fase_penilaian_akhir' => $data['fase_penilaian_akhir'] ?? [],
            'pengaturan_dana' => $data['pengaturan_dana'] ?? [],
            'metadata' => $data['metadata'] ?? [],
        ];

        return $this->create($history);
    }
}
