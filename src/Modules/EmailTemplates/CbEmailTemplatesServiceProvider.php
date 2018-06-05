<?php

namespace crocodicstudio\crudbooster\Modules\EmailTemplates;

use Illuminate\Support\ServiceProvider;

class CbEmailTemplatesServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app['view']->addNamespace('CbEmailTpl', __DIR__.'/views');
        $this->loadRoutesFrom( __DIR__.'/email_templates_routes.php');
        $this->loadMigrationsFrom(__DIR__ . '/migrations');
        app('CbDynamicMenus')->addMenu('CbEmailTpl::super_admin_menu');
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
