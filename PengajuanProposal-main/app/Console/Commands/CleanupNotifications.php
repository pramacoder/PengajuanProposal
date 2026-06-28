<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NotificationService;

class CleanupNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:cleanup {--days=30 : Number of days to keep notifications}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up old notifications from the database';

    /**
     * Execute the console command.
     */
    public function handle(NotificationService $notificationService): int
    {
        $days = $this->option('days');
        
        $this->info("Membersihkan notifikasi yang lebih dari {$days} hari...");
        
        try {
            $deletedCount = $notificationService->cleanupOldNotifications();
            
            $this->info("Berhasil menghapus {$deletedCount} notifikasi lama.");
            
            if ($deletedCount > 0) {
                $this->info("Notifikasi yang lebih dari {$days} hari telah dibersihkan.");
            } else {
                $this->info("Tidak ada notifikasi lama yang perlu dibersihkan.");
            }
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Gagal membersihkan notifikasi: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
