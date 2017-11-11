<?php

namespace crocodicstudio\crudbooster\Modules\PrivilegeModule;

use Illuminate\Support\ServiceProvider;

class CbNotificationsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadRoutesFrom( __DIR__.'/notifications_routes.php');
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
