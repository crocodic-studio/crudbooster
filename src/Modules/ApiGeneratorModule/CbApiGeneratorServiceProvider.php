<?php

namespace Crocodicstudio\Crudbooster\Modules\ApiGeneratorModule;

use Illuminate\Support\ServiceProvider;

class CbApiGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['view']->addNamespace('CbApiGen', __DIR__.'/views');
        $this->loadRoutesFrom(__DIR__.'/api_generator_routes.php');
        $this->loadMigrationsFrom(__DIR__ . '/migrations');
        app('CbDynamicMenus')->addSuperAdminMenu('CbApiGen::menu');
        $this->registerModule();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
    }

    private function registerModule()
    {
        app('CbModulesRegistery')->addModule('api-generator', (object) [
            'name' => trans('crudbooster.API_Generator'),
            'icon' => 'fa fa-cloud-download',
            'path' => 'api_generator',
            'table_name' => '',
            'controller' => 'ApiCustomController',
            'is_protected' => 1,
            'is_active' => 1,
        ]);
    }

}
