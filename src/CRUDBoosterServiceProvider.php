<?php
namespace crocodicstudio\crudbooster;

use crocodicstudio\crudbooster\Modules\ApiGeneratorModule\CbApiGeneratorServiceProvider;
use crocodicstudio\crudbooster\Modules\AuthModule\CbAuthServiceProvider;
use crocodicstudio\crudbooster\Modules\FileManagerModule\CbFileManagerServiceProvider;
use crocodicstudio\crudbooster\Modules\MenuModule\CbMenuServiceProvider;
use crocodicstudio\crudbooster\Modules\ModuleGenerator\CbModulesGeneratorServiceProvider;
use crocodicstudio\crudbooster\Modules\PrivilegeModule\CbPrivilegesServiceProvider;
use crocodicstudio\crudbooster\Modules\SettingModule\CbSettingsServiceProvider;
use crocodicstudio\crudbooster\Modules\StatisticModule\CbStatisticsServiceProvider;
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
        $this->loadViewsFrom(__DIR__.'/views', 'crudbooster');
        $this->publishes([__DIR__.'/configs/crudbooster.php' => config_path('crudbooster.php')], 'cb_config');
        $this->publishes([__DIR__.'/localization' => resource_path('lang')], 'cb_localization');
        $this->publishes([__DIR__.'/database/migrations' => base_path('database/migrations/crudbooster')], 'cb_migration');
        $this->publishes([__DIR__.'/database/seeds' => base_path('database/seeds')], 'cb_migration');

        /* Integrate LFM to CRUDBooster */
        $this->publishes([
            __DIR__.'/laravel-filemanager' => base_path('vendor/unisharp/laravel-filemanager'),
        ], 'cb_lfm');

        $this->publishes([
            __DIR__.'/laravel-filemanager/public' => public_path('vendor/laravel-filemanager'),
        ], 'cb_lfm');

        $this->publishes([
            __DIR__.'/laravel-filemanager/src/config/lfm.php' => config_path('lfm.php'),
        ], 'cb_lfm');

        $this->publishes([
            __DIR__.'/laravel-filemanager/src/views/script.blade.php' => resource_path('views/vendor/laravel-filemanager/script.blade.php'),
        ], 'cb_lfm');

        $this->publishes([
            __DIR__.'/userfiles/views/vendor/crudbooster/type_components/readme.txt' => resource_path('views/vendor/crudbooster/type_components/readme.txt'),
        ], 'cb_type_components');

        if (! file_exists(app_path('Http/Controllers/CBHook.php'))) {
            $this->publishes([__DIR__.'/userfiles/controllers/CBHook.php' => app_path('Http/Controllers/CBHook.php')], 'CBHook');
        }

        if (! file_exists(app_path('Http/Controllers/AdminUsersController.php'))) {
            $this->publishes([__DIR__.'/userfiles/controllers/AdminUsersController.php' => app_path('Http/Controllers/AdminUsersController.php')], 'cb_user_controller');
        }

        require __DIR__.'/validations/validation.php';
        $this->loadRoutesFrom( __DIR__.'/routes.php');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        require __DIR__.'/helpers/Helper.php';

        $this->mergeConfigFrom(__DIR__.'/configs/crudbooster.php', 'crudbooster');

        $this->app->singleton('crudbooster', function () {
            return true;
        });

        $this->commands([
            commands\Mailqueues::class,
        ]);

        $this->registerCrudboosterCommand();

        $this->commands('crudboosterinstall');
        $this->commands('crudboosterupdate');

        $this->app->register('Barryvdh\DomPDF\ServiceProvider');
        $this->app->register('Maatwebsite\Excel\ExcelServiceProvider');
        $this->app->register('Unisharp\Laravelfilemanager\LaravelFilemanagerServiceProvider');
        $this->app->register('Intervention\Image\ImageServiceProvider');
        $this->app->register('Imanghafoori\Widgets\WidgetsServiceProvider');

        $loader = AliasLoader::getInstance();
        $loader->alias('PDF', 'Barryvdh\DomPDF\Facade');
        $loader->alias('Excel', 'Maatwebsite\Excel\Facades\Excel');
        $loader->alias('Image', 'Intervention\Image\Facades\Image');
        $loader->alias('CRUDBooster', 'crocodicstudio\crudbooster\helpers\CRUDBooster');
        $loader->alias('CB', 'crocodicstudio\crudbooster\helpers\CB');
        $this->app->register(CbAuthServiceProvider::class);
        $this->app->register(CbApiGeneratorServiceProvider::class);
        $this->app->register(CbModulesGeneratorServiceProvider::class);
        $this->app->register(CbSettingsServiceProvider::class);
        $this->app->register(CbStatisticsServiceProvider::class);
        $this->app->register(CbPrivilegesServiceProvider::class);
        $this->app->register(CbMenuServiceProvider::class);
        $this->app->register(CbFileManagerServiceProvider::class);
    }

    private function registerCrudboosterCommand()
    {
        $this->app->singleton('crudboosterinstall', function () {
            return new CrudboosterInstallationCommand;
        });

        $this->app->singleton('crudboosterupdate', function () {
            return new CrudboosterUpdateCommand;
        });
    }
}
