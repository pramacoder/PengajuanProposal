<?php

namespace Database\Seeders;

use App\Models\Pt;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PtSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create specific PT instances (operators) tanpa factory
        $pts = [
            [
                'nama_pt' => 'Ahmad Rizki',
                'no_hp_pt' => '081234567890',
                'email_pt' => 'ahmad.rizki@example.com',
                'password' => Hash::make('123456'),
                'role' => 'operator',
                'is_active' => true,
            ],
            [
                'nama_pt' => 'Siti Nurhaliza',
                'no_hp_pt' => '081234567891',
                'email_pt' => 'siti.nurhaliza@example.com',
                'password' => Hash::make('123456'),
                'role' => 'operator',
                'is_active' => true,
            ],
            [
                'nama_pt' => 'Budi Santoso',
                'no_hp_pt' => '081234567892',
                'email_pt' => 'budi.santoso@example.com',
                'password' => Hash::make('123456'),
                'role' => 'operator',
                'is_active' => true,
            ],
            [
                'nama_pt' => 'Dewi Sartika',
                'no_hp_pt' => '081234567893',
                'email_pt' => 'dewi.sartika@example.com',
                'password' => Hash::make('123456'),
                'role' => 'operator',
                'is_active' => true,
            ],
            [
                'nama_pt' => 'Rudi Hermawan',
                'no_hp_pt' => '081234567894',
                'email_pt' => 'rudi.hermawan@example.com',
                'password' => Hash::make('123456'),
                'role' => 'operator',
                'is_active' => true,
            ]
        ];

        foreach ($pts as $pt) {
            Pt::create($pt);
        }

        // Create additional random PT instances dengan jumlah yang pasti
        Pt::factory(10)->create();

        echo "Seeder PT berhasil dibuat!\n";
        echo "Total PT: " . Pt::count() . "\n";
        echo "Akun untuk testing:\n";
        echo "1. ahmad.rizki@example.com / 123456\n";
        echo "2. siti.nurhaliza@example.com / 123456\n";
        echo "3. budi.santoso@example.com / 123456\n";
        echo "4. dewi.sartika@example.com / 123456\n";
        echo "5. rudi.hermawan@example.com / 123456\n";
    }
}