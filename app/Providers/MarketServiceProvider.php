<?php

namespace App\Providers;

use App\Utilities\AppUtility;
use Illuminate\Support\ServiceProvider;

class MarketServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        AppUtility::macroMarketHeader();
    }
}
