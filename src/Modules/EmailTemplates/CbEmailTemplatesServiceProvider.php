<?php

namespace Crocodicstudio\Crudbooster\Modules\EmailTemplates;

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
        app('CbDynamicMenus')->addSuperAdminMenu('CbEmailTpl::super_admin_menu');
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
        app('CbModulesRegistery')->addModule('email-templates', (object)             [
            'name' => trans('crudbooster.Email_Templates'),
            'icon' => 'fa fa-envelope-o',
            'path' => 'email_templates',
            'table_name' => 'cms_email_templates',
            'controller' => 'EmailTemplatesController',
            'is_protected' => 1,
            'is_active' => 1,
        ]);
    }

}
