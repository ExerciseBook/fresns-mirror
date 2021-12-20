<?php

namespace App\Http\FresnsApi\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class FresnsApiServiceProvider extends ServiceProvider
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
        $routePaths = [
            'User',
            'Member',
            'Message',
            'Info',
            'Editor',
            'Content',
        ];

        foreach($routePaths as $path) {
            $this->loadRoutesFrom(__DIR__.'/../'.$path.'/FsRouteApi.php');
        }
    }
}
