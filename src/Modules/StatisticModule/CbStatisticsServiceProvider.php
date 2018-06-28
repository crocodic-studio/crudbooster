<?php

namespace Crocodicstudio\Crudbooster\Modules\StatisticModule;

use Illuminate\Support\ServiceProvider;

class CbStatisticsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['view']->addNamespace('CbStatistics', __DIR__.'/views');
        $this->loadRoutesFrom( __DIR__.'/statistic_route.php');
        $this->loadMigrationsFrom(__DIR__ . '/migrations');
        app('CbDynamicMenus')->addSuperAdminMenu('CbStatistics::menu');
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
        app('CbModulesRegistery')->addModule('statistic-builder', (object) [
            'name' => trans('crudbooster.Statistic_Builder'),
            'icon' => 'fa fa-dashboard',
            'path' => 'statistic_builder',
            'table_name' => 'cms_statistics',
            'controller' => 'StatisticBuilderController',
            'is_protected' => 1,
            'is_active' => 1,
        ]);
    }

}
