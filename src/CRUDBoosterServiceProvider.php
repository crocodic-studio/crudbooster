<?php

namespace Crocodicstudio\Crudbooster;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider;
use Crocodicstudio\Crudbooster\Commands\CrudboosterInstallationCommand;
use Crocodicstudio\Crudbooster\Commands\CrudboosterUpdateCommand;
use Illuminate\Foundation\AliasLoader;

class CRUDBoosterServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */

    public function boot()
    {        
                                
        $this->loadViewsFrom(__DIR__.'/views', 'crudbooster');
        $this->publishes([__DIR__.'/publishable/config/crudbooster.php' => config_path('crudbooster.php')],'cb_config');            
        $this->publishes([__DIR__.'/publishable/lang' => resource_path('lang')], 'cb_localization');                 
        $this->publishes([__DIR__.'/publishable/database' => base_path('database')],'cb_migration');


        /* Integrate LFM to CRUDBooster */
        $this->publishes([
            __DIR__.'/publishable/laravel-filemanager' => base_path('vendor/unisharp/laravel-filemanager'),
        ],'cb_lfm');

        $this->publishes([
            __DIR__.'/publishable/laravel-filemanager/public' => public_path('vendor/laravel-filemanager'),
        ],'cb_lfm');        

        $this->publishes([
            __DIR__.'/publishable/laravel-filemanager/src/config/lfm.php' => config_path('lfm.php'),
        ],'cb_lfm');        

        $this->publishes([
            __DIR__.'/publishable/laravel-filemanager/src/views/script.blade.php' => resource_path('views/vendor/laravel-filemanager/script.blade.php'),
        ],'cb_lfm');

        $this->publishes([
            __DIR__.'/publishable/views/vendor/crudbooster/type_components/readme.txt' => resource_path('views/vendor/crudbooster/type_components/readme.txt'),
        ],'cb_type_components');

        if(!file_exists(app_path('Http/Controllers/CBHook.php'))) {
            $this->publishes([__DIR__.'/publishable/controllers/CBHook.php' => app_path('Http/Controllers/CBHook.php')],'CBHook');
        }

        if(!file_exists(app_path('Http/Controllers/AdminCmsUsersController.php'))) {
            $this->publishes([__DIR__.'/publishable/controllers/AdminCmsUsersController.php' => app_path('Http/Controllers/AdminCmsUsersController.php')],'cb_user_controller');
        }
        
                    
        require __DIR__.'/Validations/validation.php';        
        require __DIR__.'/routes.php';                        
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {                                   
        require __DIR__.'/Helpers/Helper.php';      

        $this->mergeConfigFrom(__DIR__.'/Config/crudbooster.php','crudbooster');        
        
        $this->app->singleton('crudbooster', function ()
        {
            return true;
        });

        $this->commands([
            Commands\Mailqueues::class            
        ]);

        $this->registerCrudboosterCommand();

        $this->commands('crudboosterinstall');
        $this->commands('crudboosterupdate');


        $this->app->register('Barryvdh\DomPDF\ServiceProvider');
        $this->app->register('Maatwebsite\Excel\ExcelServiceProvider');
        $this->app->register('Unisharp\Laravelfilemanager\LaravelFilemanagerServiceProvider');
        $this->app->register('Intervention\Image\ImageServiceProvider');

        $loader = AliasLoader::getInstance();
        $loader->alias('PDF', 'Barryvdh\DomPDF\Facade');
        $loader->alias('Excel', 'Maatwebsite\Excel\Facades\Excel');
        $loader->alias('Image', 'Intervention\Image\Facades\Image');
        $loader->alias('CRUDBooster', 'Crocodicstudio\\Crudbooster\\Helpers\\CRUDBooster');
        $loader->alias('CB', 'Crocodicstudio\Crudbooster\Helpers\CB');
    }


    private function registerCrudboosterCommand()
    {
        $this->app->singleton('crudboosterinstall',function() {
            return new CrudboosterInstallationCommand;
        });

        $this->app->singleton('crudboosterupdate',function() {
            return new CrudboosterUpdateCommand;
        });        
    }
}
