<?php

namespace Crocodicstudio\Crudbooster\Modules\AuthModule;

use Illuminate\Support\ServiceProvider;

class CbAuthServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['view']->addNamespace('CbAuth', __DIR__.'/views');
        $this->loadRoutesFrom( __DIR__.'/auth_routes.php');
        $this->loadMigrationsFrom(__DIR__.'/migrations');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
    }
}
