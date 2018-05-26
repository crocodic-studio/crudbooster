<?php
namespace crocodicstudio\crudbooster;

use crocodicstudio\crudbooster\CBCoreModule\CbUser;
use crocodicstudio\crudbooster\CBCoreModule\Facades\CbRouter;
use crocodicstudio\crudbooster\Modules\ApiGeneratorModule\CbApiGeneratorServiceProvider;
use crocodicstudio\crudbooster\Modules\AuthModule\CbAuthServiceProvider;
use crocodicstudio\crudbooster\Modules\EmailTemplates\CbEmailTemplatesServiceProvider;
use crocodicstudio\crudbooster\Modules\FileManagerModule\CbFileManagerServiceProvider;
use crocodicstudio\crudbooster\Modules\LogsModule\CbLogsServiceProvider;
use crocodicstudio\crudbooster\Modules\MenuModule\CbMenuServiceProvider;
use crocodicstudio\crudbooster\Modules\ModuleGenerator\CbModulesGeneratorServiceProvider;
use crocodicstudio\crudbooster\Modules\NotificationsModule\CbNotificationsServiceProvider;
use crocodicstudio\crudbooster\Modules\PrivilegeModule\CbPrivilegesServiceProvider;
use crocodicstudio\crudbooster\Modules\SettingModule\CbSettingsServiceProvider;
use crocodicstudio\crudbooster\Modules\StatisticModule\CbStatisticsServiceProvider;
use Illuminate\Support\ServiceProvider;
use crocodicstudio\crudbooster\commands\InstallationCommand;
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
        $this->publishes([__DIR__.'/CBCoreModule/configs/crudbooster.php' => config_path('crudbooster.php')], 'cb_config');
        $this->publishes([__DIR__.'/localization' => resource_path('lang')], 'cb_localization');

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
            __DIR__.'/CBCoreModule/publieshed_files/readme.txt' => resource_path('views/vendor/crudbooster/type_components/readme.txt'),
        ], 'cb_type_components');

        if (! file_exists(app_path('Http/Controllers/AdminUsersController.php'))) {
            $this->publishes([__DIR__.'/CBCoreModule/publieshed_files/AdminUsersController.php' => app_path('Http/Controllers/AdminUsersController.php')], 'cb_user_controller');
        }


        $this->defineValidationRules();
        $this->loadRoutesFrom( __DIR__.'/routes.php');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/configs/crudbooster.php', 'crudbooster');

        $this->app->singleton('crudbooster', function () {
            return true;
        });

        $this->commands(InstallationCommand::class);

        $this->defineAuthGuard();

        $this->registerThirdPartyPackages();

        $this->setAliases();

        $this->app->singleton(\crocodicstudio\crudbooster\CBCoreModule\CbRouter::class);
        $this->registerCrudBoosterModules();
    }

    private function defineValidationRules()
    {
        \Validator::extend('alpha_spaces', function ($attribute, $value) {
            // This will only accept alpha and spaces.
            // If you want to accept hyphens use: /^[\pL\s-]+$/u.
            return preg_match('/^[\pL\s]+$/u', $value);
        }, 'The :attribute should be letters only');
    }

    private function defineAuthGuard()
    {
        config()->offsetSet('auth.providers.cb_users', ['driver' => 'eloquent', 'model' => CbUser::class,]);
        config()->offsetSet('auth.guards.cbAdmin', ['driver' => 'session', 'provider' => 'cb_users']);
    }

    private function setAliases(): void
    {
        $loader = AliasLoader::getInstance();
        $loader->alias('PDF', 'Barryvdh\DomPDF\Facade');
        $loader->alias('Excel', 'Maatwebsite\Excel\Facades\Excel');
        $loader->alias('Image', 'Intervention\Image\Facades\Image');
        $loader->alias('CRUDBooster', 'crocodicstudio\crudbooster\helpers\CRUDBooster');
        $loader->alias('CB', 'crocodicstudio\crudbooster\helpers\CRUDBooster');
        $loader->alias('DbInspector', 'crocodicstudio\crudbooster\helpers\DbInspector');
        $loader->alias('CbRouter', CbRouter::class);
    }

    private function registerThirdPartyPackages(): void
    {
        $this->app->register('Barryvdh\DomPDF\ServiceProvider');
        $this->app->register('Maatwebsite\Excel\ExcelServiceProvider');
        $this->app->register('Unisharp\Laravelfilemanager\LaravelFilemanagerServiceProvider');
        $this->app->register('Intervention\Image\ImageServiceProvider');
        $this->app->register('Imanghafoori\Widgets\WidgetsServiceProvider');
        $this->app->register('Imanghafoori\Responder\LaravelResponderServiceProvider');
    }

    private function registerCrudBoosterModules()
    {
        $this->app->register(CbAuthServiceProvider::class);
        $this->app->register(CbApiGeneratorServiceProvider::class);
        $this->app->register(CbModulesGeneratorServiceProvider::class);
        $this->app->register(CbSettingsServiceProvider::class);
        $this->app->register(CbStatisticsServiceProvider::class);
        $this->app->register(CbPrivilegesServiceProvider::class);
        $this->app->register(CbMenuServiceProvider::class);
        $this->app->register(CbFileManagerServiceProvider::class);
        $this->app->register(CbNotificationsServiceProvider::class);
        $this->app->register(CbEmailTemplatesServiceProvider::class);
        $this->app->register(CbLogsServiceProvider::class);
    }
}
