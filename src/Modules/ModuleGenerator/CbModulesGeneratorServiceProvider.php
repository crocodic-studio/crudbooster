<?php

namespace Crocodicstudio\Crudbooster\Modules\ModuleGenerator;

use Illuminate\Support\ServiceProvider;

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
        $this->loadMigrationsFrom(__DIR__.'/migrations');
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
        app('CbModulesRegistery')->addModule('modules', (object) [
            'name' => trans('crudbooster.Module_Generator'),
            'icon' => 'fa fa-database',
            'path' => 'module_generator',
            'table_name' => 'cms_moduls',
            'controller' => 'AdminModulesController',
            'is_protected' => 1,
            'is_active' => 1,
        ]);
    }
}
