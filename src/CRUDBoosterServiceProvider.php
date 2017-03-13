<?php namespace crocodicstudio\crudbooster;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\ServiceProvider;
use crocodicstudio\crudbooster\commands\CrudboosterInstallationCommand;
use crocodicstudio\crudbooster\commands\CrudboosterUpdateCommand;
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

        //Create symlink for uploads path
        if(!file_exists(public_path('uploads'))) {            
            app('files')->link(storage_path('app'), public_path('uploads'));
        }

        //Create vendor folder at public
        if(!file_exists(public_path('vendor'))) {
            mkdir(public_path('vendor'));
        }

        //Crate symlink for assets
        if(!file_exists(public_path('vendor/crudbooster'))) {
            app('files')->link(__DIR__.'/assets',public_path('vendor/crudbooster'));
        }

        $this->loadViewsFrom(__DIR__.'/views', 'crudbooster');
            
        if(file_exists(config_path('crudbooster.php'))) {            
            $this->mergeConfigFrom(__DIR__.'/configs/crudbooster.php','crudbooster');  
        }else{
            $this->publishes([__DIR__.'/configs/crudbooster.php' => config_path('crudbooster.php')],'cb_config');            
        }
        
        $this->publishes([__DIR__.'/localization' => resource_path('lang')], 'cb_localization');                 

        $this->publishes([__DIR__.'/database' => base_path('database')],'cb_migration');


        /* Integrate LFM to CRUDBooster */
        $this->publishes([
            __DIR__.'/laravel-filemanager' => base_path('vendor/unisharp/laravel-filemanager'),
        ],'cb_lfm');

        $this->publishes([
            __DIR__.'/laravel-filemanager/public' => public_path('vendor/laravel-filemanager'),
        ],'cb_lfm');        

        $this->publishes([
            __DIR__.'/laravel-filemanager/src/config/lfm.php' => config_path('lfm.php'),
        ],'cb_lfm');        

        $this->publishes([
            __DIR__.'/laravel-filemanager/src/views/script.blade.php' => resource_path('views/vendor/laravel-filemanager/script.blade.php'),
        ],'cb_lfm');

        $this->publishes([
            __DIR__.'/userfiles/views/vendor/crudbooster/type_components/readme.txt' => resource_path('views/vendor/crudbooster/type_components/readme.txt'),
        ],'cb_type_components');

        if(!file_exists(app_path('Http/Controllers/CBHook.php'))) {
            $this->publishes([__DIR__.'/userfiles/controllers/CBHook.php' => app_path('Http/Controllers/CBHook.php')],'CBHook');
        }

        if(!file_exists(app_path('Http/Controllers/AdminCmsUsersController.php'))) {
            $this->publishes([__DIR__.'/userfiles/controllers/AdminCmsUsersController.php' => app_path('Http/Controllers/AdminCmsUsersController.php')],'cb_user_controller');
        }


        /* Removing the default user and password reset, it makes you ambigous when using CRUDBooster */
        if(file_exists(base_path('database/migrations/2014_10_12_000000_create_users_table.php'))) {        
            @unlink(base_path('database/migrations/2014_10_12_000000_create_users_table.php'));
        }
        if(file_exists(base_path('database/migrations/2014_10_12_100000_create_password_resets_table.php'))) {            
            @unlink(base_path('database/migrations/2014_10_12_100000_create_password_resets_table.php'));
        }
                    
        require __DIR__.'/validations/validation.php';        
        require __DIR__.'/routes.php';    
            
        $this->app->booted(function () {
            $schedule = $this->app->make(\Illuminate\Console\Scheduling\Schedule::class);
            $schedule->command('mailqueues')->cron("* * * * * *");
        });    
        
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {                                   
        require __DIR__.'/helpers/Helper.php';              
        
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


        $this->app->register('Barryvdh\DomPDF\ServiceProvider');
        $this->app->register('Maatwebsite\Excel\ExcelServiceProvider');
        $this->app->register('Unisharp\Laravelfilemanager\LaravelFilemanagerServiceProvider');
        $this->app->register('Intervention\Image\ImageServiceProvider');

        $loader = AliasLoader::getInstance();
        $loader->alias('PDF', 'Barryvdh\DomPDF\Facade');
        $loader->alias('Excel', 'Maatwebsite\Excel\Facades\Excel');
        $loader->alias('Image', 'Intervention\Image\Facades\Image');
        $loader->alias('CRUDBooster', 'crocodicstudio\crudbooster\helpers\CRUDBooster');
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
