<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Models\Proposal;
use App\Observers\ProposalObserver;
use App\Services\FirebaseService;
use App\Repositories\Firebase\NotificationRepository;
use App\Repositories\Firebase\FormPenilaianConfigRepository;
use App\Repositories\Firebase\ReviewDetailRepository;
use App\Repositories\Firebase\RuangKontrolHistoryRepository;
use App\Repositories\Firebase\SimbelmawaReportRepository;
use App\Helpers\NumberFormatHelper;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(FirebaseService::class);
        $this->app->singleton(NotificationRepository::class);
        $this->app->singleton(FormPenilaianConfigRepository::class);
        $this->app->singleton(ReviewDetailRepository::class);
        $this->app->singleton(RuangKontrolHistoryRepository::class);
        $this->app->singleton(SimbelmawaReportRepository::class);
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
