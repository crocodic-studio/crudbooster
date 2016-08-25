<?php

namespace crocodicstudio\crudbooster;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider;

class CRUDBoosterServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {        
        $this->publishes([  __DIR__.'/configs/crudbooster.php' => config_path('crudbooster.php')],'cb_config');
        
        $this->loadViewsFrom(__DIR__.'/views', 'crudbooster');
        
        $this->publishes([
            __DIR__.'/assets' => public_path('vendor/crudbooster'),
        ], 'cb_public');             

        $this->publishes([
            __DIR__.'/database' => base_path('database'),
        ],'cb_migration');


        /* Integrate LFM to CRUDBooster */
        $this->publishes([
            __DIR__.'/laravel-filemanager' => base_path('vendor/unisharp/laravel-filemanager'),
        ],'cb_lfm');

        $this->publishes([
            __DIR__.'/laravel-filemanager/public' => public_path('vendor/laravel-filemanager'),
        ],'cb_lfm');        

        $this->publishes([
            __DIR__.'/laravel-filemanager/src/config/lfm.php' => config_path('lfm.php'),
        ],'cb_lfm_config');        

        $this->publishes([
            __DIR__.'/laravel-filemanager/src/views/script.blade.php' => resource_path('views/vendor/laravel-filemanager/script.blade.php'),
        ],'cb_lfm_config');
        
        require __DIR__.'/helpers/Helper.php';
        require __DIR__.'/routes.php';        
        
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {                                 

        $this->app['crudbooster'] = $this->app->share(function ()
        {
            return true;
        });
    }
}
