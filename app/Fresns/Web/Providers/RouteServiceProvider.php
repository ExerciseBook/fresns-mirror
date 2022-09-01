<?php

/*
 * Fresns (https://fresns.org)
 * Copyright (C) 2021-Present Jarvis Tang
 * Released under the Apache-2.0 License.
 */

namespace App\Fresns\Web\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    public function boot()
    {
        parent::boot();
    }

    public function map()
    {
        // Routing is disabled when data cannot be queried from the database
        try {
            if (! fs_db_config('FresnsEngine')) {
                return;
            }
        } catch (\Throwable $e) {
            return;
        }

        $url = config('app.url');
        $host = str_replace(['http://', 'https://'], '', rtrim($url, '/'));
        $currentAccessHost = \request()->httpHost();

        // 通过 php artisan serve 启动时，app.url 如果与访问访问域名不同，则不设置路由的 domain 限制。
        // 解决的问题：插件直达域名首页。允许插件自定义域名首页。
        // 原理：同一路由，后面的会覆盖前面的。通过 domain 限制，可以达到效果。
        if ($host != $currentAccessHost) {
            $host = null;
        }

        Route::group([
            'domain' => $host,
        ], function () {
            $this->mapApiRoutes();
            $this->mapWebRoutes();
        });
    }

    protected function mapApiRoutes()
    {
        Route::prefix('api')->name('fresns.api.')->group(__DIR__.'/../Routes/api.php');
    }

    protected function mapWebRoutes()
    {
        Route::name('fresns.')->group(__DIR__.'/../Routes/web.php');
    }
}
