<?php

namespace App\Repositories\Firebase;

class SimbelmawaReportRepository extends BaseFirestoreRepository
{
    protected string $collection = 'simbelmawa_reports';

    public function getByReportId(int $reportId): ?array
    {
        $results = $this->where('simbelmawa_report_id', '=', $reportId);
        return $results[0] ?? null;
    }

    public function getByTahunAjaran(string $tahunAjaran): array
    {
        return $this->where('tahun_ajaran', '=', $tahunAjaran);
    }

    public function createReport(int $reportId, array $data): string
    {
        $report = [
            'simbelmawa_report_id' => $reportId,
            'tahun_ajaran' => $data['tahun_ajaran'],
            'judul_proposal_lolos_pimnas' => $data['judul_proposal_lolos_pimnas'] ?? [],
            'prestasi' => $data['prestasi'] ?? [],
            'metadata' => $data['metadata'] ?? [],
        ];

        return $this->create($report);
    }
}
