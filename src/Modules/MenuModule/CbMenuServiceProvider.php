<?php

namespace Crocodicstudio\Crudbooster\Modules\MenuModule;

use Illuminate\Support\ServiceProvider;

class CbMenuServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['view']->addNamespace('CbMenu', __DIR__.'/views');
        $this->loadRoutesFrom( __DIR__.'/menus_routes.php');
        $this->loadMigrationsFrom(__DIR__ . '/migrations');
        app('CbDynamicMenus')->addSuperAdminMenu('CbMenu::menu');
        app('CbDynamicMenus')->addMenu('CbMenu::dynamic_menus');
        $this->registerModule();

  /*    $hasDashboard = \Crocodicstudio\Crudbooster\Modules\MenuModule\MenuRepo::sidebarDashboard();
        if (false && $hasDashboard) {
            app('CbDynamicMenus')->addMenu('CbMenu::dashboard');
        }*/
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
        app('CbModulesRegistery')->addModule('menus', (object) [
            'name' => trans('crudbooster.Menu_Management'),
            'icon' => 'fa fa-bars',
            'path' => 'menu_management',
            'table_name' => 'cms_menus',
            'controller' => 'MenusController',
            'is_protected' => 1,
            'is_active' => 1,
        ]);
    }

}
