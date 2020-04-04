<?php
namespace crocodicstudio\crudbooster\helpers;

use crocodicstudio\crudbooster\middlewares\CBBackend;
use crocodicstudio\crudbooster\middlewares\CBSuperAdmin;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

trait RouteTrait
{

    /**
     * Route for crudbooster system modules
     */
    public static function routeCBModules() {
        Route::group([
            'middleware' => ['web', CBBackend::class, CBSuperAdmin::class],
            'prefix' => config('crudbooster.ADMIN_PATH'),
            'namespace' => '\crocodicstudio\crudbooster\controllers',
        ], function () {

            cb()->cbNamespaceRouteController("users","AdminCmsUsersController");
            cb()->cbNamespaceRouteController("api-generator","AdminApiGeneratorController");
            cb()->cbNamespaceRouteController("notification","AdminNotificationController");
            cb()->cbNamespaceRouteController("email-templates","AdminEmailTemplatesController");
            cb()->cbNamespaceRouteController("logs","AdminLogsController");
            cb()->cbNamespaceRouteController("menus","AdminMenusController");
            cb()->cbNamespaceRouteController("modules","AdminModulesController");
            cb()->cbNamespaceRouteController("roles","AdminRolesController");
            cb()->cbNamespaceRouteController("settings","AdminSettingsController");
            cb()->cbNamespaceRouteController("statistic-builder","AdminStatisticBuilderController");

        });
    }

    /**
     * Route for modules
     */
    public static function routeAdminModules() {
        Route::group([
            'middleware' => ['web', '\crocodicstudio\crudbooster\middlewares\CBBackend'],
            'prefix' => config('crudbooster.ADMIN_PATH'),
            'namespace' => 'App\Http\Controllers',
        ], function () {

            Route::get("/","AdminDashboardController@getIndex");
            Route::get("profile","AdminProfileController@getIndex");
            Route::post("profile/save","AdminProfileController@postSave");

            try {
                $moduls = DB::table('cms_moduls')
                ->where('path', '!=', '')
                ->where('controller', '!=', '')
                ->where('is_protected', 0)
                ->where('deleted_at', null)
                ->get();
                foreach ($moduls as $v) {
                    cb()->routeController($v->path, $v->controller);
                }
            } catch (Exception $e) {
                Log::debug($e);
            }
        });
    }

    /**
     * Route for file streaming handler
     */
    public static function routeFileHandler() {
        Route::get('uploads/{one?}/{two?}/{three?}/{four?}/{five?}', ['uses' => '\crocodicstudio\crudbooster\controllers\FileController@getPreview', 'as' => 'fileControllerPreview']);
    }

    /**
     * Route for Admin Auth
     */
    public static function routeAdminAuth() {
        Route::group(['middleware' => ['web'], 'prefix' => config('crudbooster.ADMIN_PATH'), 'namespace' => '\crocodicstudio\crudbooster\controllers'], function () {
            Route::post('unlock-screen', ['uses' => 'AdminAuthController@postUnlockScreen', 'as' => 'postUnlockScreen']);
            Route::get('lock-screen', ['uses' => 'AdminAuthController@getLockscreen', 'as' => 'getLockScreen']);
            Route::post('forgot', ['uses' => 'AdminAuthController@postForgot', 'as' => 'postForgot']);
            Route::get('forgot', ['uses' => 'AdminAuthController@getForgot', 'as' => 'getForgot']);
            Route::post('register', ['uses' => 'AdminAuthController@postRegister', 'as' => 'postRegister']);
            Route::get('register', ['uses' => 'AdminAuthController@getRegister', 'as' => 'getRegister']);
            Route::get('logout', ['uses' => 'AdminAuthController@getLogout', 'as' => 'getLogout']);
            Route::post('login', ['uses' => 'AdminAuthController@postLogin', 'as' => 'postLogin']);
            Route::get('login', ['uses' => 'AdminAuthController@getLogin', 'as' => 'getLogin']);
        });
    }

    /**
     * Auto generate route for Controller the has an Api prefix
     */
    public static function routeAPI() {
        Route::group(['middleware' => ['api', '\crocodicstudio\crudbooster\middlewares\CBAuthAPI'], 'namespace' => 'App\Http\Controllers'], function () {
            $dir = scandir(base_path("app/Http/Controllers"));
            foreach ($dir as $v) {
                $v = str_replace('.php', '', $v);
                $names = array_filter(preg_split('/(?=[A-Z])/', str_replace('Controller', '', $v)));
                $names = strtolower(implode('_', $names));

                if (substr($names, 0, 4) == 'api_') {
                    $names = str_replace('api_', '', $names);
                    Route::any('api/'.$names, $v.'@execute_api');
                }
            }
        });

        Route::group(['middleware' => ['web'], 'namespace' => '\crocodicstudio\crudbooster\controllers'], function () {
            Route::get('api-documentation', ['uses' => 'AdminApiGeneratorController@apiDocumentation', 'as' => 'apiDocumentation']);
            Route::get('download-documentation-postman', ['uses' => 'AdminApiGeneratorController@getDownloadPostman', 'as' => 'downloadDocumentationPostman']);
        });
    }
}