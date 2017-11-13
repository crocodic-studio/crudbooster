<?php

use crocodicstudio\crudbooster\middlewares\CBBackend;
use crocodicstudio\crudbooster\middlewares\CBSuperadmin;

Route::group([
    'middleware' => ['web', CBSuperadmin::class],
    'prefix' => cbAdminPath(),
    'namespace' => '\crocodicstudio\crudbooster\Modules\MenuModule',
], function () {
    Route::get('menus/', ['uses' => 'AdminMenusController@getIndex', 'as' => 'AdminMenusControllerGetIndex']);
    Route::get('menus/export-data', ['uses' => 'AdminMenusController@getExportData', 'as' => 'AdminMenusControllerGetExportData']);
    Route::get('menus/data-query', ['uses' => 'AdminMenusController@getDataQuery', 'as' => 'AdminMenusControllerGetDataQuery']);
    Route::get('menus/data-table', ['uses' => 'AdminMenusController@getDataTable', 'as' => 'AdminMenusControllerGetDataTable']);
    Route::get('menus/data-modal-datatable', ['uses' => 'AdminMenusController@getDataModalDatatable', 'as' => 'AdminMenusControllerGetDataModalDatatable']);
    Route::get('menus/update-single', ['uses' => 'AdminMenusController@getUpdateSingle', 'as' => 'AdminMenusControllerGetUpdateSingle']);
    Route::get('menus/add', ['uses' => 'AdminMenusController@getAdd', 'as' => 'AdminMenusControllerGetAdd']);
    Route::get('menus/edit/{id?}', ['uses' => 'AdminMenusController@getEdit', 'as' => 'AdminMenusControllerGetEdit']);
    Route::get('menus/delete/{id?}', ['uses' => 'AdminMenusController@getDelete', 'as' => 'AdminMenusControllerGetDelete']);
    Route::get('menus/detail/{id?}', ['uses' => 'AdminMenusController@getDetail', 'as' => 'AdminMenusControllerGetDetail']);
    Route::get('menus/import-data', ['uses' => 'AdminMenusController@getImportData', 'as' => 'AdminMenusControllerGetImportData']);
    Route::get('menus/delete-image', ['uses' => 'AdminMenusController@getDeleteImage', 'as' => 'AdminMenusControllerGetDeleteImage']);

    Route::post('menus/save-menu', ['uses' => 'AdminMenusController@postSaveMenu', 'as' => 'AdminMenusControllerPostSaveMenu',]);
    Route::post('menus/export-data', ['uses' => 'AdminMenusController@postExportData', 'as' => 'AdminMenusControllerPostExportData',]);
    Route::post('menus/add-save', ['uses' => 'AdminMenusController@postAddSave', 'as' => 'AdminMenusControllerPostAddSave',]);
    Route::post('menus/edit-save/{id?}', ['uses' => 'AdminMenusController@postEditSave', 'as' => 'AdminMenusControllerPostEditSave',]);
    Route::post('menus/find-data', ['uses' => 'AdminMenusController@postFindData', 'as' => 'AdminMenusControllerPostFindData',]);
    Route::post('menus/done-import', ['uses' => 'AdminMenusController@postDoneImport', 'as' => 'AdminMenusControllerPostDoneImport',]);
    Route::post('menus/do-import-chunk', ['uses' => 'AdminMenusController@postDoImportChunk', 'as' => 'AdminMenusControllerPostDoImportChunk',]);
    Route::post('menus/do-upload-import-data', [
        'uses' => 'AdminMenusController@postDoUploadImportData',
        'as' => 'AdminMenusControllerPostDoUploadImportData',
    ]);
    Route::post('menus/action-selected', ['uses' => 'AdminMenusController@postActionSelected', 'as' => 'AdminMenusControllerPostActionSelected',]);
    Route::post('menus/upload-summernote', ['uses' => 'AdminMenusController@postUploadSummernote', 'as' => 'AdminMenusControllerPostUploadSummernote',]);
    Route::post('menus/upload-file', ['uses' => 'AdminMenusController@postUploadFile', 'as' => 'AdminMenusControllerPostUploadFile',]);
});

if (! Request::is(cbAdminPath())) {
    return;
}

$dashboard_menu = DB::table('cms_menus')->where('is_dashboard', 1)->first();
// ROUTER FOR OWN CONTROLLER FROM CB
Route::group(['middleware' => ['web', CBBackend::class], 'prefix' => cbAdminPath(), 'namespace' => ctrlNamespace(),
], function () use ($dashboard_menu) {

    if (! $dashboard_menu) {
        return;
    }
    $dashboard_type = $dashboard_menu->type;
    $path = $dashboard_menu->path;


    if ($dashboard_type == 'Statistic') {
        Route::get('/', '\\crocodicstudio\\crudbooster\\Modules\\StatisticModule\\AdminStatisticBuilderController@getDashboard');
    } elseif ($dashboard_type == 'Module') {
        $module = CRUDBooster::first('cms_moduls', ['path' => $path]);
        Route::get('/', $module->controller.'@getIndex');
    } elseif ($dashboard_type == 'Route') {
        $action = str_replace("Controller", "Controller@", $path);
        $action = str_replace(['Get', 'Post'], ['get', 'post'], $action);
        Route::get('/', $action);
    } elseif ($dashboard_type == 'Controller & Method') {
        Route::get('/', $path);
    } elseif ($dashboard_type == 'URL') {
        redirect($path);
    }
});

Route::group(['middleware' => ['web', CBBackend::class], 'prefix' => cbAdminPath(), 'namespace' => '\crocodicstudio\crudbooster\controllers',
], function () use ($dashboard_menu) {
    if (! $dashboard_menu) {
        CRUDBooster::routeController('/', '\crocodicstudio\crudbooster\Modules\AuthModule\AuthController');
    }
});
