<?php

namespace Crocodicstudio\Crudbooster\Modules\PrivilegeModule;

use Illuminate\Support\ServiceProvider;

class CbPrivilegesServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['view']->addNamespace('CbPrivilege', __DIR__.'/views');
        $this->loadRoutesFrom( __DIR__.'/privileges_routes.php');
        $this->publishes([__DIR__.'/localization' => resource_path('lang')], 'cb_localization');
        $this->loadMigrationsFrom(__DIR__ . '/migrations');
        app('CbDynamicMenus')->addSuperAdminMenu('CbPrivilege::menu');
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
        app('CbModulesRegistery')->addModule('privileges', (object) [
            'name' => trans('crudbooster.Privileges'),
            'icon' => 'fa fa-cog',
            'path' => 'privileges',
            'table_name' => 'cms_privileges',
            'controller' => 'PrivilegesController',
            'is_protected' => 1,
            'is_active' => 1,
        ]);
    }

}
