<?php

namespace Crocodicstudio\Crudbooster\Modules\SettingModule;

use Illuminate\Support\ServiceProvider;

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
        $this->loadMigrationsFrom(__DIR__ . '/migrations');
        app('CbDynamicMenus')->addSuperAdminMenu('CbSettings::menu');
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
        app('CbModulesRegistery')->addModule('settings', (object) [
            'name' => trans('crudbooster.settings'),
            'icon' => 'fa fa-cog',
            'path' => 'settings',
            'table_name' => 'cms_settings',
            'controller' => 'SettingsController',
            'is_protected' => 1,
            'is_active' => 1,
        ]);
    }

}
