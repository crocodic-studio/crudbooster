<?php

namespace crocodicstudio\crudbooster\Modules\LogsModule;

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
        $this->loadRoutesFrom( __DIR__.'/logs_routes.php');
        $this->publishes([__DIR__.'/localization' => resource_path('lang/crudbooster')], 'cb_localization');
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
