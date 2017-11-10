<?php

namespace crocodicstudio\crudbooster\Modules\AuthModule;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;

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
