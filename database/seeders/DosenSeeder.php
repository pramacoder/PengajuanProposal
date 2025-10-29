<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DosenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dosens = [
            [
                'nuptk' => '12345678901234567890',
                'nama_dosen' => 'Prof. Dr. I Made Sujana, S.T., M.T.',
                'gelar_depan' => 'Prof. Dr.',
                'gelar_belakang' => 'S.T., M.T.',
                'email_dosen' => 'made.sujana@unud.ac.id',
                'no_hp_dosen' => '081234567100',
                'password' => Hash::make('password123'),
                'role' => 'dosen',
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nuptk' => '12345678901234567891',
                'nama_dosen' => 'Dr. I Gusti Ayu Made Sari, S.Pd., M.Pd.',
                'gelar_depan' => 'Dr.',
                'gelar_belakang' => 'S.Pd., M.Pd.',
                'email_dosen' => 'sari@unud.ac.id',
                'no_hp_dosen' => '081234567101',
                'password' => Hash::make('password123'),
                'role' => 'dosen',
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nuptk' => '12345678901234567892',
                'nama_dosen' => 'Prof. Dr. I Wayan Gede Suardana, S.T., M.T.',
                'gelar_depan' => 'Prof. Dr.',
                'gelar_belakang' => 'S.T., M.T.',
                'email_dosen' => 'suardana@unud.ac.id',
                'no_hp_dosen' => '081234567102',
                'password' => Hash::make('password123'),
                'role' => 'dosen',
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nuptk' => '12345678901234567893',
                'nama_dosen' => 'Dr. I Made Sudarma Putra, S.Pd., M.Pd.',
                'gelar_depan' => 'Dr.',
                'gelar_belakang' => 'S.Pd., M.Pd.',
                'email_dosen' => 'sudarma.putra@unud.ac.id',
                'no_hp_dosen' => '081234567103',
                'password' => Hash::make('password123'),
                'role' => 'dosen',
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nuptk' => '12345678901234567894',
                'nama_dosen' => 'Prof. Dr. I Gusti Agung Ayu Ratna Dewi, S.E., M.Si.',
                'gelar_depan' => 'Prof. Dr.',
                'gelar_belakang' => 'S.E., M.Si.',
                'email_dosen' => 'ratna.dewi@unud.ac.id',
                'no_hp_dosen' => '081234567104',
                'password' => Hash::make('password123'),
                'role' => 'dosen',
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nuptk' => '12345678901234567895',
                'nama_dosen' => 'Dr. I Wayan Gede Artawan Eka Putra, S.T., M.T.',
                'gelar_depan' => 'Dr.',
                'gelar_belakang' => 'S.T., M.T.',
                'email_dosen' => 'artawan.eka@unud.ac.id',
                'no_hp_dosen' => '081234567105',
                'password' => Hash::make('password123'),
                'role' => 'dosen',
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nuptk' => '12345678901234567896',
                'nama_dosen' => 'Prof. Dr. I Made Rai Pramana, S.T., M.T.',
                'gelar_depan' => 'Prof. Dr.',
                'gelar_belakang' => 'S.T., M.T.',
                'email_dosen' => 'rai.pramana@unud.ac.id',
                'no_hp_dosen' => '081234567106',
                'password' => Hash::make('password123'),
                'role' => 'dosen',
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nuptk' => '12345678901234567897',
                'nama_dosen' => 'Dr. I Wayan Gede Suharta, S.Pd., M.Pd.',
                'gelar_depan' => 'Dr.',
                'gelar_belakang' => 'S.Pd., M.Pd.',
                'email_dosen' => 'suharta@unud.ac.id',
                'no_hp_dosen' => '081234567107',
                'password' => Hash::make('password123'),
                'role' => 'dosen',
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nuptk' => '12345678901234567898',
                'nama_dosen' => 'Prof. Dr. I Made Sudiana, S.T., M.T.',
                'gelar_depan' => 'Prof. Dr.',
                'gelar_belakang' => 'S.T., M.T.',
                'email_dosen' => 'made.sudiana@unud.ac.id',
                'no_hp_dosen' => '081234567108',
                'password' => Hash::make('password123'),
                'role' => 'dosen',
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nuptk' => '12345678901234567899',
                'nama_dosen' => 'Dr. I Gusti Ayu Made Sari Dewi, S.Pd., M.Pd.',
                'gelar_depan' => 'Dr.',
                'gelar_belakang' => 'S.Pd., M.Pd.',
                'email_dosen' => 'made.sari@unud.ac.id',
                'no_hp_dosen' => '081234567109',
                'password' => Hash::make('password123'),
                'role' => 'dosen',
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('dosens')->insert($dosens);
    }
}

