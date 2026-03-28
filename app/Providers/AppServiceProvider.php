<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use RalphJSmit\Laravel\SEO\Facades\SEOManager;
use RalphJSmit\Laravel\SEO\Support\SEOData;

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
        SEOManager::SEODataTransformer(function (SEOData $SEOData): SEOData {
            if (request()->query()) {
                $SEOData->robots = 'noindex,follow';
            }

            return $SEOData;
        });

        if (request()->ip() === '104.28.62.24') {
            config(['app.debug' => true]);
        }
    }
}
