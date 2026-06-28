<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PimpinanPTSeeder extends Seeder
{
    public function run(): void
    {
        $pimpinanPTs = [
            ['identifier' => 'PIM-001', 'name' => 'Prof. Dr. I Made Surya Wijaya, S.T., M.T.',            'email' => 'pimpinan.pt@unud.ac.id',  'phone' => '081234567910'],
            ['identifier' => 'PIM-002', 'name' => 'Prof. Dr. I Gusti Ayu Made Sari Dewi, S.Pd., M.Pd.',  'email' => 'pimpinan.pt2@unud.ac.id', 'phone' => '081234567911'],
        ];

        foreach ($pimpinanPTs as $pim) {
            DB::table('users')->updateOrInsert(
                ['email' => $pim['email']],
                [
                    'identifier' => $pim['identifier'],
                    'name' => $pim['name'],
                    'phone' => $pim['phone'],
                    'password' => Hash::make('password123'),
                    'role' => 'pimpinan_pt',
                    'is_active' => true,
                    'metadata' => json_encode([]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
