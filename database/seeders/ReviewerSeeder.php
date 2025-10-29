<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ReviewerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reviewers = [
            [
                'nama_reviewer' => 'Prof. Dr. I Made Suarta, S.H., M.H.',
                'no_hp_reviewer' => '081234567890',
                'email_reviewer' => 'made.suarta@unud.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'reviewer',
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_reviewer' => 'Dr. I Gusti Agung Ayu Ratna Dewi, S.T., M.T.',
                'no_hp_reviewer' => '081234567891',
                'email_reviewer' => 'ratna.dewi@unud.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'reviewer',
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_reviewer' => 'Prof. Dr. I Wayan Gede Artawan Eka Putra, S.Pd., M.Pd.',
                'no_hp_reviewer' => '081234567892',
                'email_reviewer' => 'artawan.putra@unud.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'reviewer',
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_reviewer' => 'Dr. I Made Sudarma, S.T., M.T.',
                'no_hp_reviewer' => '081234567893',
                'email_reviewer' => 'made.sudarma@unud.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'reviewer',
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_reviewer' => 'Prof. Dr. I Ketut Gede Darma Putra, S.E., M.Si.',
                'no_hp_reviewer' => '081234567894',
                'email_reviewer' => 'ketut.darma@unud.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'reviewer',
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_reviewer' => 'Dr. I Gusti Ayu Made Srinadi, S.Pd., M.Pd.',
                'no_hp_reviewer' => '081234567895',
                'email_reviewer' => 'srinadi@unud.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'reviewer',
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_reviewer' => 'Prof. Dr. I Made Rai Pramana, S.T., M.T.',
                'no_hp_reviewer' => '081234567896',
                'email_reviewer' => 'rai.pramana@unud.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'reviewer',
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_reviewer' => 'Dr. I Wayan Gede Suharta, S.Pd., M.Pd.',
                'no_hp_reviewer' => '081234567897',
                'email_reviewer' => 'suharta@unud.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'reviewer',
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_reviewer' => 'Prof. Dr. I Made Sudiana, S.T., M.T.',
                'no_hp_reviewer' => '081234567898',
                'email_reviewer' => 'made.sudiana@unud.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'reviewer',
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_reviewer' => 'Dr. I Gusti Ayu Made Sari, S.Pd., M.Pd.',
                'no_hp_reviewer' => '081234567899',
                'email_reviewer' => 'made.sari@unud.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'reviewer',
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_reviewer' => 'Prof. Dr. I Wayan Gede Artawan, S.T., M.T.',
                'no_hp_reviewer' => '081234567800',
                'email_reviewer' => 'artawan@unud.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'reviewer',
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_reviewer' => 'Dr. I Made Sudarsana, S.Pd., M.Pd.',
                'no_hp_reviewer' => '081234567801',
                'email_reviewer' => 'sudarsana@unud.ac.id',
                'password' => Hash::make('password123'),
                'role' => 'reviewer',
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('reviewers')->insert($reviewers);
    }
}

