<?php

namespace App\Fresns\Panel\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Booting the package.
     */
    public function boot()
    {
        View::share('langs', config('panel.langs'));
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
    }
}
