<?php

namespace crocodicstudio\crudbooster\AuthModule;

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
        require __DIR__.'/auth_routes.php';
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
