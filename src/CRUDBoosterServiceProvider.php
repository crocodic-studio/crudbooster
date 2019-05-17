<?php namespace crocodicstudio\crudbooster;

use crocodicstudio\crudbooster\commands\DeveloperUser;
use crocodicstudio\crudbooster\commands\Generate;
use crocodicstudio\crudbooster\commands\MigrateData;
use crocodicstudio\crudbooster\commands\MigrationData;
use crocodicstudio\crudbooster\controllers\scaffolding\singletons\ColumnSingleton;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider;
use crocodicstudio\crudbooster\commands\Install;
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

        // Register views
        $this->loadViewsFrom(__DIR__.'/views', 'crudbooster');
        $this->loadViewsFrom(__DIR__.'/types', 'types');

        // Publish the files
        $this->publishes([__DIR__.'/configs/crudbooster.php' => config_path('crudbooster.php')],'cb_config');            
        $this->publishes([__DIR__.'/localization' => resource_path('lang')], 'cb_localization');                 
        $this->publishes([__DIR__.'/database' => base_path('database')],'cb_migration');
        $this->publishes([__DIR__.'/templates/CBHook.stub'=> app_path('Http/CBHook.php')],'cb_hook');
        $this->publishes([__DIR__ . '/assets' =>public_path('cb_asset')],'cb_asset');
                    
        require __DIR__.'/validations/validation.php';        
        require __DIR__.'/routes.php';

        $this->registerTypeRoutes();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {                                   
        require __DIR__.'/helpers/Helper.php';

        // Singletons
        $this->app->singleton('crudbooster', function ()
        {
            return true;
        });
        $this->app->singleton('ColumnSingleton', ColumnSingleton::class);

        // Merging configuration
        $this->mergeConfigFrom(__DIR__.'/configs/crudbooster.php','crudbooster');


        // Register Commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                Install::class,
                Generate::class,
                DeveloperUser::class,
                MigrationData::class,
                MigrateData::class
            ]);
        }

        // Register additional library
        $this->app->register('Intervention\Image\ImageServiceProvider');
        $loader = AliasLoader::getInstance();
        $loader->alias('Image', 'Intervention\Image\Facades\Image');
        $loader->alias('CB', 'crocodicstudio\crudbooster\helpers\CB');
    }

    private function registerTypeRoutes() {
        $routes = rglob(__DIR__.DIRECTORY_SEPARATOR."types".DIRECTORY_SEPARATOR."Route.php");
        foreach($routes as $route) {
            require $route;
        }
    }



}
