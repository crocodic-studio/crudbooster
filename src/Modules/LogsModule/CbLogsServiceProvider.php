<?php

namespace crocodicstudio\crudbooster\Modules\LogsModule;

use crocodicstudio\crudbooster\Modules\LogsModule\Listeners\Auth;
use crocodicstudio\crudbooster\Modules\LogsModule\Listeners\CRUD;
use crocodicstudio\crudbooster\Modules\MenuModule\DynamicMenus;
use Illuminate\Support\ServiceProvider;

class CbLogsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/views', 'CbLogger');
        $this->loadRoutesFrom( __DIR__.'/logs_routes.php');
        $this->publishes([__DIR__.'/localization' => resource_path('lang')], 'cb_localization');
        $this->loadMigrationsFrom(__DIR__ . '/migrations');
        Auth::registerListeners();
        CRUD::registerListeners();
        app('CbDynamicMenus')->addMenu('CbLogger::log');
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
