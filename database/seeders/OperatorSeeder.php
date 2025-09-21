<?php

namespace Database\Seeders;

use App\Models\PT;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class OperatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 5 operator (PT) for testing
        PT::create([
            'nama_pt' => 'Dr. Siti Nurhaliza, M.Kom',
            'email_pt' => 'siti.nurhaliza@univ.ac.id',
            'no_hp_pt' => '081234567901',
            'password' => Hash::make('operator123'),
            'role' => 'operator',
            'is_active' => true,
        ]);

        PT::create([
            'nama_pt' => 'Prof. Dr. Ahmad Wijaya, S.T, M.T',
            'email_pt' => 'ahmad.wijaya@univ.ac.id',
            'no_hp_pt' => '081234567902',
            'password' => Hash::make('operator123'),
            'role' => 'operator',
            'is_active' => true,
        ]);

        PT::create([
            'nama_pt' => 'Dr. Rina Sari, S.Kom, M.Kom',
            'email_pt' => 'rina.sari@univ.ac.id',
            'no_hp_pt' => '081234567903',
            'password' => Hash::make('operator123'),
            'role' => 'operator',
            'is_active' => true,
        ]);

        PT::create([
            'nama_pt' => 'Ir. Bambang Sutrisno, M.T',
            'email_pt' => 'bambang.sutrisno@univ.ac.id',
            'no_hp_pt' => '081234567904',
            'password' => Hash::make('operator123'),
            'role' => 'operator',
            'is_active' => true,
        ]);

        PT::create([
            'nama_pt' => 'Dr. Endang Rahayu, S.Pd, M.Pd',
            'email_pt' => 'endang.rahayu@univ.ac.id',
            'no_hp_pt' => '081234567905',
            'password' => Hash::make('operator123'),
            'role' => 'operator',
            'is_active' => true,
        ]);

        echo "Seeder operator (PT) berhasil dibuat!\n";
        echo "Total operator: " . PT::count() . "\n";
        echo "Akun operator untuk testing:\n";
        echo "1. siti.nurhaliza@univ.ac.id / operator123\n";
        echo "2. ahmad.wijaya@univ.ac.id / operator123\n";
        echo "3. rina.sari@univ.ac.id / operator123\n";
        echo "4. bambang.sutrisno@univ.ac.id / operator123\n";
        echo "5. endang.rahayu@univ.ac.id / operator123\n";
    }
}
