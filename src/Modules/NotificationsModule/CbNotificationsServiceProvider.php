<?php

namespace Crocodicstudio\Crudbooster\Modules\NotificationsModule;

use Illuminate\Support\ServiceProvider;

class CbNotificationsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom( __DIR__.'/views', 'CbNotifications');
        $this->loadRoutesFrom( __DIR__.'/notifications_routes.php');
        $this->loadMigrationsFrom(__DIR__ . '/migrations');
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
        app('CbModulesRegistery')->addModule('notifications', (object) [
            'name' => trans('crudbooster.Notifications'),
            'icon' => 'fa fa-cog',
            'path' => 'notifications',
            'table_name' => 'cms_notifications',
            'controller' => 'NotificationsController',
            'is_protected' => 1,
            'is_active' => 1,
        ]);
    }

}
