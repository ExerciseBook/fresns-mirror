<?php

namespace App\Http\FresnsInstall;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class FresnsInstallServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerRoutes();
    }

    private function registerRoutes()
    {
        $this->loadRoutesFrom(__DIR__.'/FsRouteWeb.php');
    }
}
