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
