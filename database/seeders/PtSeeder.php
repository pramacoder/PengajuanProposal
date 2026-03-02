<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PtSeeder extends Seeder
{
    public function run(): void
    {
        $operators = [
            ['identifier' => 'OPR-001', 'name' => 'Dr. I Made Surya Wijaya, S.T., M.T.',                 'email' => 'operator.surya@unud.ac.id',        'phone' => '081234567900'],
            ['identifier' => 'OPR-002', 'name' => 'Dr. I Gusti Ayu Made Sari Dewi, S.Pd., M.Pd.',        'email' => 'operator.sari.dewi@unud.ac.id',    'phone' => '081234567901'],
            ['identifier' => 'OPR-003', 'name' => 'Prof. Dr. I Wayan Gede Suardana, S.T., M.T.',         'email' => 'operator.suardana@unud.ac.id',     'phone' => '081234567902'],
            ['identifier' => 'OPR-004', 'name' => 'Dr. I Made Sudarma Putra, S.Pd., M.Pd.',              'email' => 'operator.sudarma@unud.ac.id',      'phone' => '081234567903'],
            ['identifier' => 'OPR-005', 'name' => 'Prof. Dr. I Gusti Agung Ayu Ratna Sari, S.E., M.Si.', 'email' => 'operator.ratna@unud.ac.id',        'phone' => '081234567904'],
            ['identifier' => 'OPR-006', 'name' => 'Dr. I Wayan Gede Artawan Eka Putra, S.T., M.T.',      'email' => 'operator.artawan@unud.ac.id',      'phone' => '081234567905'],
        ];

        foreach ($operators as $op) {
            DB::table('users')->insert([
                'identifier' => $op['identifier'],
                'name' => $op['name'],
                'email' => $op['email'],
                'phone' => $op['phone'],
                'password' => Hash::make('password123'),
                'role' => 'operator',
                'is_active' => true,
                'metadata' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
