<?php

namespace App\Repositories\Firebase;

class FormPenilaianConfigRepository extends BaseFirestoreRepository
{
    protected string $collection = 'form_penilaian_configs';

    public function getByFormPenilaianId(int $formId): ?array
    {
        $results = $this->where('form_penilaian_id', '=', $formId);
        return $results[0] ?? null;
    }

    public function getBySkimAndType(string $skim, string $jenisForm): ?array
    {
        $query = $this->getCollection()
            ->where('skim', '=', $skim)
            ->where('jenis_form', '=', $jenisForm)
            ->limit(1);

        foreach ($query->documents() as $doc) {
            if ($doc->exists()) {
                return array_merge(['id' => $doc->id()], $doc->data());
            }
        }

        return null;
    }

    public function createConfig(int $formPenilaianId, array $data): string
    {
        $config = [
            'form_penilaian_id' => $formPenilaianId,
            'jenis_form' => $data['jenis_form'],
            'skim' => $data['skim'] ?? null,
            'kategori' => $data['kategori'] ?? [],
            'kriteria' => $data['kriteria'] ?? [],
        ];

        return $this->create($config);
    }
}
