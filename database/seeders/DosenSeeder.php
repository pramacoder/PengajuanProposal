<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DosenSeeder extends Seeder
{
    public function run(): void
    {
        $dosens = [
            ['nuptk' => '12345678901234567890', 'name' => 'Prof. Dr. I Made Sujana, S.T., M.T.',                   'email' => 'made.sujana@unud.ac.id',      'phone' => '081234567100', 'gelar_depan' => 'Prof. Dr.',  'gelar_belakang' => 'S.T., M.T.'],
            ['nuptk' => '12345678901234567891', 'name' => 'Dr. I Gusti Ayu Made Sari, S.Pd., M.Pd.',               'email' => 'sari@unud.ac.id',             'phone' => '081234567101', 'gelar_depan' => 'Dr.',        'gelar_belakang' => 'S.Pd., M.Pd.'],
            ['nuptk' => '12345678901234567892', 'name' => 'Prof. Dr. I Wayan Gede Suardana, S.T., M.T.',           'email' => 'suardana@unud.ac.id',         'phone' => '081234567102', 'gelar_depan' => 'Prof. Dr.',  'gelar_belakang' => 'S.T., M.T.'],
            ['nuptk' => '12345678901234567893', 'name' => 'Dr. I Made Sudarma Putra, S.Pd., M.Pd.',                'email' => 'sudarma.putra@unud.ac.id',    'phone' => '081234567103', 'gelar_depan' => 'Dr.',        'gelar_belakang' => 'S.Pd., M.Pd.'],
            ['nuptk' => '12345678901234567894', 'name' => 'Prof. Dr. I Gusti Agung Ayu Ratna Dewi, S.E., M.Si.',   'email' => 'ratna.dewi@unud.ac.id',       'phone' => '081234567104', 'gelar_depan' => 'Prof. Dr.',  'gelar_belakang' => 'S.E., M.Si.'],
            ['nuptk' => '12345678901234567895', 'name' => 'Dr. I Wayan Gede Artawan Eka Putra, S.T., M.T.',        'email' => 'artawan.eka@unud.ac.id',      'phone' => '081234567105', 'gelar_depan' => 'Dr.',        'gelar_belakang' => 'S.T., M.T.'],
            ['nuptk' => '12345678901234567896', 'name' => 'Prof. Dr. I Made Rai Pramana, S.T., M.T.',              'email' => 'rai.pramana@unud.ac.id',      'phone' => '081234567106', 'gelar_depan' => 'Prof. Dr.',  'gelar_belakang' => 'S.T., M.T.'],
            ['nuptk' => '12345678901234567897', 'name' => 'Dr. I Wayan Gede Suharta, S.Pd., M.Pd.',                'email' => 'suharta@unud.ac.id',          'phone' => '081234567107', 'gelar_depan' => 'Dr.',        'gelar_belakang' => 'S.Pd., M.Pd.'],
            ['nuptk' => '12345678901234567898', 'name' => 'Prof. Dr. I Made Sudiana, S.T., M.T.',                   'email' => 'made.sudiana@unud.ac.id',     'phone' => '081234567108', 'gelar_depan' => 'Prof. Dr.',  'gelar_belakang' => 'S.T., M.T.'],
            ['nuptk' => '12345678901234567899', 'name' => 'Dr. I Gusti Ayu Made Sari Dewi, S.Pd., M.Pd.',          'email' => 'made.sari@unud.ac.id',        'phone' => '081234567109', 'gelar_depan' => 'Dr.',        'gelar_belakang' => 'S.Pd., M.Pd.'],
        ];

        foreach ($dosens as $dosen) {
            DB::table('users')->insert([
                'identifier' => $dosen['nuptk'],
                'name' => $dosen['name'],
                'email' => $dosen['email'],
                'phone' => $dosen['phone'],
                'password' => Hash::make('password123'),
                'role' => 'dosen',
                'is_active' => true,
                'metadata' => json_encode([
                    'nuptk' => $dosen['nuptk'],
                    'gelar_depan' => $dosen['gelar_depan'],
                    'gelar_belakang' => $dosen['gelar_belakang'],
                ]),
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
