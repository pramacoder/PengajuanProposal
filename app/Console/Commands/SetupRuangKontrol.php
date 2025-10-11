<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RuangKontrol;

class SetupRuangKontrol extends Command
{
    protected $signature = 'setup:ruang-kontrol';
    protected $description = 'Setup default ruang kontrol data';

    public function handle()
    {
        $this->info('Setting up Ruang Kontrol...');
        
        // Check if data already exists
        $existing = RuangKontrol::first();
        
        if ($existing) {
            $this->info('Data already exists. Updating...');
            $existing->status_pendaftaran = 'terbuka';
            $existing->status_perbaikan = 'terbuka';
            $existing->save();
            $this->info('Updated existing data.');
        } else {
            $this->info('Creating new data...');
            RuangKontrol::create([
                'status_pendaftaran' => 'terbuka',
                'status_perbaikan' => 'terbuka',
            ]);
            $this->info('Created new data.');
        }
        
        // Verify
        $ruang = RuangKontrol::first();
        $this->info('Current status:');
        $this->info('Pendaftaran: ' . $ruang->status_pendaftaran);
        $this->info('Perbaikan: ' . $ruang->status_perbaikan);
        
        return Command::SUCCESS;
    }
}
