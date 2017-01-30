<?php namespace crocodicstudio\crudbooster;

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
        $this->loadViewsFrom(__DIR__.'/views', 'crudbooster');

        $this->publishes([  __DIR__.'/configs/crudbooster.php' => config_path('crudbooster.php')],'cb_config');                
        
        $this->publishes([__DIR__.'/localization' => resource_path('lang')], 'cb_localization'); 

        $this->publishes([__DIR__.'/assets' => public_path('vendor/crudbooster')], 'cb_public');             

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
        ],'cb_lfm_config');        

        $this->publishes([
            __DIR__.'/laravel-filemanager/src/views/script.blade.php' => resource_path('views/vendor/laravel-filemanager/script.blade.php'),
        ],'cb_lfm_config');

        $this->publishes([
            __DIR__.'/userfiles/views/vendor/crudbooster/type_components/readme.txt' => resource_path('views/vendor/crudbooster/type_components/readme.txt'),
        ],'cb_type_components');

        if(!file_exists(app_path('Http/Controllers/CBHook.php'))) {
            $this->publishes([__DIR__.'/userfiles/controllers/CBHook.php' => app_path('Http/Controllers/CBHook.php')],'CBHook');
        }

        if(!file_exists(app_path('Http/Controllers/AdminCmsUsersController.php'))) {
            $this->publishes([__DIR__.'/userfiles/controllers/AdminCmsUsersController.php' => app_path('Http/Controllers/AdminCmsUsersController.php')],'cb_user_controller');
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

        $this->app['crudbooster'] = $this->app->share(function ()
        {
            return true;
        });

        $this->commands([
            commands\Mailqueues::class            
        ]);
    }
}
