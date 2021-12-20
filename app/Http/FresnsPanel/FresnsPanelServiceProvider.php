<?php

namespace App\Http\FresnsPanel;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class FresnsPanelServiceProvider extends ServiceProvider
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
