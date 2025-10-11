<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Proposal;

class CheckProposalStatus extends Command
{
    protected $signature = 'check:proposal-status';
    protected $description = 'Check proposal status and validation';

    public function handle()
    {
        $this->info('Checking proposal status...');
        
        $proposals = Proposal::all();
        
        $this->info('Total proposals: ' . $proposals->count());
        
        foreach ($proposals as $proposal) {
            $this->info('Proposal ID: ' . $proposal->id_proposal);
            $this->info('Status: ' . $proposal->status);
            $this->info('Status Validasi: ' . $proposal->status_validasi);
            $this->info('Reviewer Substantif 1: ' . $proposal->id_reviewer_substantif_1);
            $this->info('Reviewer Substantif 2: ' . $proposal->id_reviewer_substantif_2);
            $this->info('---');
        }
        
        return Command::SUCCESS;
    }
}
