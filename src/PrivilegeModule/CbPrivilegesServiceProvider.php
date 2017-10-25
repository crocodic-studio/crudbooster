<?php

namespace crocodicstudio\crudbooster\PrivilegeModule;

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
        require __DIR__.'/privileges_routes.php';
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
