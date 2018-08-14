<?php namespace crocodicstudio\crudbooster;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider;
use crocodicstudio\crudbooster\commands\CrudboosterInstallationCommand;
use crocodicstudio\crudbooster\commands\CrudboosterUpdateCommand;
use Illuminate\Foundation\AliasLoader;
use App;

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
        $this->publishes([__DIR__.'/configs/crudbooster.php' => config_path('crudbooster.php')],'cb_config');            
        $this->publishes([__DIR__.'/localization' => resource_path('lang')], 'cb_localization');                 
        $this->publishes([__DIR__.'/database' => base_path('database')],'cb_migration');

        $this->publishes([
            __DIR__.'/userfiles/views/vendor/crudbooster/type_components/readme.txt' => resource_path('views/vendor/crudbooster/type_components/readme.txt'),
        ],'cb_type_components');

        if(!file_exists(app_path('Http/Controllers/CBHook.php'))) {
            $this->publishes([__DIR__.'/userfiles/controllers/CBHook.php' => app_path('Http/Controllers/CBHook.php')],'CBHook');
        }

        if(!file_exists(app_path('Http/Controllers/AdminCmsUsersController.php'))) {
            $this->publishes([__DIR__.'/userfiles/controllers/AdminCmsUsersController.php' => app_path('Http/Controllers/AdminCmsUsersController.php')],'cb_user_controller');
        }        

        $this->publishes([
            __DIR__.'/assets'=>public_path('vendor/crudbooster')
        ],'cb_asset');  
                    
        require __DIR__.'/validations/validation.php';        
        require __DIR__.'/routes.php';                        
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {                                   
        require __DIR__.'/helpers/Helper.php';      

        $this->mergeConfigFrom(__DIR__.'/configs/crudbooster.php','crudbooster');        
        
        $this->app->singleton('crudbooster', function ()
        {
            return true;
        });

        $this->commands([
            commands\Mailqueues::class            
        ]);

        $this->registerCrudboosterCommand();

        $this->commands('crudboosterinstall');
        $this->commands('crudboosterupdate');
        $this->commands(['\crocodicstudio\crudbooster\commands\CrudboosterVersionCommand']);

        $this->app->register('Barryvdh\DomPDF\ServiceProvider');
        $this->app->register('Maatwebsite\Excel\ExcelServiceProvider');
        $this->app->register('Unisharp\Laravelfilemanager\LaravelFilemanagerServiceProvider');
        $this->app->register('Intervention\Image\ImageServiceProvider');

        $loader = AliasLoader::getInstance();
        $loader->alias('PDF', 'Barryvdh\DomPDF\Facade');
        $loader->alias('Excel', 'Maatwebsite\Excel\Facades\Excel');
        $loader->alias('Image', 'Intervention\Image\Facades\Image');
        $loader->alias('CRUDBooster', 'crocodicstudio\crudbooster\helpers\CRUDBooster');
        $loader->alias('CB', 'crocodicstudio\crudbooster\helpers\CB');
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
