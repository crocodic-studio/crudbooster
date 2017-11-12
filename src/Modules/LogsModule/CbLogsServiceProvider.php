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
