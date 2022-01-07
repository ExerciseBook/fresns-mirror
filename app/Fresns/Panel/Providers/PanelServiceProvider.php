<?php

namespace App\Fresns\Panel\Providers;

use App\Fresns\Panel\Http\Exceptions\Handler;
use Illuminate\Support\ServiceProvider;
use App\Fresns\Panel\Http\Middleware\Authenticate;
use Illuminate\Contracts\Debug\ExceptionHandler;

class PanelServiceProvider extends ServiceProvider
{
    /**
     * Booting the package.
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerViews();

        \Config::set('auth.guards.panel', [
            'driver' => 'session',
            'provider' => 'panel',
        ]);

        \Config::set('auth.providers.panel', [
            'driver' => 'eloquent',
            'model' => \App\Models\User::class,
        ]);

        $this->loadRoutesFrom(__DIR__.'/../Routes/panel.php');
        \Route::aliasMiddleware('panelAuth', Authenticate::class);

        // register exception hanlder
        $this->app->bind(
            ExceptionHandler::class,
            Handler::class
        );
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->registerConfig();
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register views.
     *
     * @return void
     */
    protected function registerViews()
    {
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'panel');
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom(__DIR__.'/../Config/panel.php', 'panel');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    protected function registerTranslations()
    {
        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'panel');
    }
}
