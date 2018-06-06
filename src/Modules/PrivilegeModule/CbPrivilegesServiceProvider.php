<?php

namespace crocodicstudio\crudbooster\Modules\PrivilegeModule;

use Illuminate\Support\ServiceProvider;

class CbPrivilegesServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['view']->addNamespace('CbPrivilege', __DIR__.'/views');
        $this->loadRoutesFrom( __DIR__.'/privileges_routes.php');
        $this->publishes([__DIR__.'/localization' => resource_path('lang')], 'cb_localization');
        $this->loadMigrationsFrom(__DIR__ . '/migrations');
        app('CbDynamicMenus')->addSuperAdminMenu('CbPrivilege::menu');
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
