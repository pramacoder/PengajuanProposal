<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PimpinanPTSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pimpinanPTs = [
            [
                'nama_pt' => 'Prof. Dr. I Made Surya Wijaya, S.T., M.T.',
                'no_hp_pt' => '081234567910',
                'email_pt' => 'pimpinan.pt@unud.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'pimpinan_pt',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_pt' => 'Prof. Dr. I Gusti Ayu Made Sari Dewi, S.Pd., M.Pd.',
                'no_hp_pt' => '081234567911',
                'email_pt' => 'pimpinan.pt2@unud.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'pimpinan_pt',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Insert atau update data Pimpinan PT
        foreach ($pimpinanPTs as $pimpinanPT) {
            DB::table('pts')->updateOrInsert(
                ['email_pt' => $pimpinanPT['email_pt']],
                $pimpinanPT
            );
        }
    }
}



