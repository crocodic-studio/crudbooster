<?php

namespace crocodicstudio\crudbooster\StatisticModule;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;

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
