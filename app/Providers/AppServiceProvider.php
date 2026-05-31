<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Models\Proposal;
use App\Observers\ProposalObserver;
use App\Helpers\NumberFormatHelper;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {

    }

    public function boot(): void
    {
        Proposal::observe(ProposalObserver::class);

        Blade::directive('formatId', function ($expression) {
            return "<?php echo \\App\\Helpers\\NumberFormatHelper::format({$expression}); ?>";
        });

        Blade::directive('rupiahId', function ($expression) {
            return "<?php echo \\App\\Helpers\\NumberFormatHelper::rupiah({$expression}); ?>";
        });
    }
}
