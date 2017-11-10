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
