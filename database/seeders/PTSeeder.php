<?php

namespace Database\Seeders;

use App\Models\Pt;
use Illuminate\Database\Seeder;

class PtSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create specific PT instances
        $pts = [
            [
                'nama_pt' => 'Universitas Gadjah Mada',
                'no_hp_pt' => '0274-515357',
                'email_pt' => 'info@ugm.ac.id'
            ],
            [
                'nama_pt' => 'Institut Teknologi Bandung',
                'no_hp_pt' => '022-2500935',
                'email_pt' => 'info@itb.ac.id'
            ],
            [
                'nama_pt' => 'Universitas Indonesia',
                'no_hp_pt' => '021-7867222',
                'email_pt' => 'info@ui.ac.id'
            ],
            [
                'nama_pt' => 'Institut Pertanian Bogor',
                'no_hp_pt' => '0251-8622642',
                'email_pt' => 'info@ipb.ac.id'
            ],
            [
                'nama_pt' => 'Universitas Airlangga',
                'no_hp_pt' => '031-5911151',
                'email_pt' => 'info@unair.ac.id'
            ]
        ];

        foreach ($pts as $pt) {
            Pt::factory()->create($pt);
        }

        // Create additional random PT instances
        Pt::factory()->count(5)->create();
    }
}