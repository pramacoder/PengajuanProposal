<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ReviewerSeeder extends Seeder
{
    public function run(): void
    {
        $reviewers = [
            ['identifier' => 'REV-001', 'name' => 'Prof. Dr. I Made Suarta, S.H., M.H.',                   'email' => 'made.suarta@unud.ac.id',     'phone' => '081234567890'],
            ['identifier' => 'REV-002', 'name' => 'Dr. I Gusti Agung Ayu Ratna Dewi, S.T., M.T.',          'email' => 'reviewer.ratna@unud.ac.id',  'phone' => '081234567891'],
            ['identifier' => 'REV-003', 'name' => 'Prof. Dr. I Wayan Gede Artawan Eka Putra, S.Pd., M.Pd.','email' => 'artawan.putra@unud.ac.id',   'phone' => '081234567892'],
            ['identifier' => 'REV-004', 'name' => 'Dr. I Made Sudarma, S.T., M.T.',                        'email' => 'made.sudarma@unud.ac.id',    'phone' => '081234567893'],
            ['identifier' => 'REV-005', 'name' => 'Prof. Dr. I Ketut Gede Darma Putra, S.E., M.Si.',       'email' => 'ketut.darma@unud.ac.id',     'phone' => '081234567894'],
            ['identifier' => 'REV-006', 'name' => 'Dr. I Gusti Ayu Made Srinadi, S.Pd., M.Pd.',            'email' => 'srinadi@unud.ac.id',         'phone' => '081234567895'],
            ['identifier' => 'REV-007', 'name' => 'Prof. Dr. I Made Rai Pramana, S.T., M.T.',              'email' => 'reviewer.pramana@unud.ac.id','phone' => '081234567896'],
            ['identifier' => 'REV-008', 'name' => 'Dr. I Wayan Gede Suharta, S.Pd., M.Pd.',                'email' => 'reviewer.suharta@unud.ac.id','phone' => '081234567897'],
            ['identifier' => 'REV-009', 'name' => 'Prof. Dr. I Made Sudiana, S.T., M.T.',                   'email' => 'reviewer.sudiana@unud.ac.id','phone' => '081234567898'],
            ['identifier' => 'REV-010', 'name' => 'Dr. I Gusti Ayu Made Sari, S.Pd., M.Pd.',               'email' => 'reviewer.sari@unud.ac.id',   'phone' => '081234567899'],
            ['identifier' => 'REV-011', 'name' => 'Prof. Dr. I Wayan Gede Artawan, S.T., M.T.',            'email' => 'artawan@unud.ac.id',         'phone' => '081234567800'],
            ['identifier' => 'REV-012', 'name' => 'Dr. I Made Sudarsana, S.Pd., M.Pd.',                    'email' => 'reviewer.sudarsana@unud.ac.id','phone' => '081234567801'],
        ];

        foreach ($reviewers as $reviewer) {
            DB::table('users')->insert([
                'identifier' => $reviewer['identifier'],
                'name' => $reviewer['name'],
                'email' => $reviewer['email'],
                'phone' => $reviewer['phone'],
                'password' => Hash::make('password123'),
                'role' => 'reviewer',
                'is_active' => true,
                'metadata' => json_encode([]),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
