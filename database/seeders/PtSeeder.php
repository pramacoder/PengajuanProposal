<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PtSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pts = [
            [
                'nama_pt' => 'Dr. I Made Surya Wijaya, S.T., M.T.',
                'no_hp_pt' => '081234567900',
                'email_pt' => 'made.surya@unud.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'operator',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_pt' => 'Dr. I Gusti Ayu Made Sari Dewi, S.Pd., M.Pd.',
                'no_hp_pt' => '081234567901',
                'email_pt' => 'sari.dewi@unud.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'operator',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_pt' => 'Prof. Dr. I Wayan Gede Suardana, S.T., M.T.',
                'no_hp_pt' => '081234567902',
                'email_pt' => 'suardana@unud.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'operator',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_pt' => 'Dr. I Made Sudarma Putra, S.Pd., M.Pd.',
                'no_hp_pt' => '081234567903',
                'email_pt' => 'sudarma.putra@unud.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'operator',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_pt' => 'Prof. Dr. I Gusti Agung Ayu Ratna Sari, S.E., M.Si.',
                'no_hp_pt' => '081234567904',
                'email_pt' => 'ratna.sari@unud.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'operator',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_pt' => 'Dr. I Wayan Gede Artawan Eka Putra, S.T., M.T.',
                'no_hp_pt' => '081234567905',
                'email_pt' => 'artawan.eka@unud.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'operator',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('pts')->insert($pts);
    }
}

