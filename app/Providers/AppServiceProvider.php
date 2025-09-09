<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Proposal;
use App\Observers\ProposalObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Proposal Observer
        Proposal::observe(ProposalObserver::class);
    }
}
