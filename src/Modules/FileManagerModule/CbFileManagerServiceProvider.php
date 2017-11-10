<?php

namespace crocodicstudio\crudbooster\Modules\FileManagerModule;

use Illuminate\Support\ServiceProvider;

class CbFileManagerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['view']->addNamespace('CbFileManager', __DIR__.'/views');
        $this->loadRoutesFrom( __DIR__.'/file_manager_routes.php');
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
