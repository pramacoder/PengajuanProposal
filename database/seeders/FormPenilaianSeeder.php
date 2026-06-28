<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FormPenilaian;
use App\Models\User;
use App\Helpers\ProposalHelper;

class FormPenilaianSeeder extends Seeder
{
    public function run(): void
    {
        // Get an operator or admin user to set as creator
        $creator = User::where('role', 'operator')->first() ?? User::first();
        $creatorId = $creator ? $creator->id : null;

        $skims = ['RE', 'RSH', 'KC', 'PM', 'PI', 'K', 'KI', 'VGK', 'AI', 'GFT'];

        // 1. Seed Administrative Checklist Forms has been removed based on user preference
        // to retain the hardcoded checklist grouping for 'administratif'.

        // 2. Seed Substantive Criteria Forms
        foreach ($skims as $skim) {
            $criteria = ProposalHelper::getSubstantifCriteria($skim);
            if (empty($criteria)) {
                continue;
            }

            $fields = [];
            foreach ($criteria as $item) {
                if (isset($item['sub_kriteria']) && !empty($item['sub_kriteria'])) {
                    foreach ($item['sub_kriteria'] as $subItem) {
                        $fields[] = [
                            'label' => $subItem['kriteria'],
                            'type' => 'integer_scale',
                            'description' => 'Bobot: ' . $subItem['bobot'] . '% (Kategori: ' . $item['kriteria'] . ')',
                            'required' => true,
                            'weight' => $subItem['bobot']
                        ];
                    }
                } else {
                    $fields[] = [
                        'label' => $item['kriteria'],
                        'type' => 'integer_scale',
                        'description' => 'Bobot: ' . $item['bobot'] . '%',
                        'required' => true,
                        'weight' => $item['bobot']
                    ];
                }
            }

            if (!empty($fields)) {
                FormPenilaian::updateOrCreate(
                    [
                        'jenis_form' => 'substantif',
                        'skim' => $skim,
                    ],
                    [
                        'nama_form' => 'Penilaian Substantif PKM-' . $skim,
                        'is_active' => true,
                        'tahun_ajaran' => '2025/2026',
                        'fields' => $fields,
                        'created_by' => $creatorId,
                    ]
                );

                // Also seed for substantive seleksi stage using same criteria
                FormPenilaian::updateOrCreate(
                    [
                        'jenis_form' => 'substantif_seleksi',
                        'skim' => $skim,
                    ],
                    [
                        'nama_form' => 'Penilaian Substantif Seleksi PKM-' . $skim,
                        'is_active' => true,
                        'tahun_ajaran' => '2025/2026',
                        'fields' => $fields,
                        'created_by' => $creatorId,
                    ]
                );
            }
        }
    }
}
