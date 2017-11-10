<?php

namespace crocodicstudio\crudbooster\Modules\ModuleGenerator;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;

class CbModulesGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['view']->addNamespace('CbModulesGen', __DIR__.'/views');
        $this->loadRoutesFrom( __DIR__.'/module_generator_routes.php');
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
