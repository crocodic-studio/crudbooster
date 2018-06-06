<?php

namespace crocodicstudio\crudbooster\Modules\MenuModule;

use Illuminate\Support\ServiceProvider;

class CbMenuServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['view']->addNamespace('CbMenu', __DIR__.'/views');
        $this->loadRoutesFrom( __DIR__.'/menus_routes.php');
        $this->loadMigrationsFrom(__DIR__ . '/migrations');
        app('CbDynamicMenus')->addSuperAdminMenu('CbMenu::menu');
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
