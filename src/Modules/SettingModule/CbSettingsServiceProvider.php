<?php

namespace crocodicstudio\crudbooster\Modules\SettingModule;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;

class CbSettingsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['view']->addNamespace('CbSettings', __DIR__.'/views');
        $this->loadRoutesFrom( __DIR__.'/settings_routes.php');
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
