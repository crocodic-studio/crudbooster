<?php

use crocodicstudio\crudbooster\middlewares\CBBackend;
use crocodicstudio\crudbooster\middlewares\CBSuperadmin;
use crocodicstudio\crudbooster\Modules\MenuModule\MenuTypes;

Route::group([
    'middleware' => ['web', CBSuperadmin::class],
    'prefix' => cbAdminPath(),
    'namespace' => cbModulesNS('MenuModule'),
], function () {
    $ctrl = 'AdminMenusController';
    Route::get('menus/', $ctrl.'@getIndex')->name($ctrl.'GetIndex');
    Route::get('menus/export-data', $ctrl.'@getExportData')->name($ctrl.'GetExportData');
    Route::post('menus/export-data', $ctrl.'@postExportData')->name($ctrl.'PostExportData');
    Route::get('menus/import-data', $ctrl.'@getImportData')->name($ctrl.'GetImportData');
    Route::post('menus/do-import-chunk', $ctrl.'@postDoImportChunk')->name($ctrl.'PostDoImportChunk');
    Route::get('menus/data-query', $ctrl.'@getDataQuery')->name($ctrl.'GetDataQuery');
    Route::get('menus/data-table', $ctrl.'@getDataTable')->name($ctrl.'GetDataTable');
    Route::get('menus/data-modal-datatable', $ctrl.'@getDataModalDatatable')->name($ctrl.'GetDataModalDatatable');
    Route::get('menus/update-single', $ctrl.'@getUpdateSingle')->name($ctrl.'GetUpdateSingle');
    Route::get('menus/add', $ctrl.'@getAdd')->name($ctrl.'GetAdd');
    Route::get('menus/edit/{id?}', $ctrl.'@getEdit')->name($ctrl.'GetEdit');
    Route::get('menus/delete/{id?}', $ctrl.'@getDelete')->name($ctrl.'GetDelete');
    Route::get('menus/detail/{id?}', $ctrl.'@getDetail')->name($ctrl.'GetDetail');
    Route::get('menus/delete-image', $ctrl.'@getDeleteImage')->name($ctrl.'GetDeleteImage');
    Route::post('menus/save-menu', $ctrl.'@postSaveMenu')->name($ctrl.'PostSaveMenu');
    Route::post('menus/export-data', $ctrl.'@postExportData')->name($ctrl.'PostExportData');
    Route::post('menus/add-save', $ctrl.'@postAddSave')->name($ctrl.'PostAddSave');
    Route::post('menus/edit-save/{id?}', $ctrl.'@postEditSave')->name($ctrl.'PostEditSave');
    Route::post('menus/find-data', $ctrl.'@postFindData')->name($ctrl.'PostFindData');
    Route::post('menus/action-selected', $ctrl.'@postActionSelected')->name($ctrl.'PostActionSelected');
});

if (! Request::is(cbAdminPath())) {
    return;
}

$dashboardMenu = DB::table('cms_menus')->where('is_dashboard', 1)->first();
// ROUTER FOR OWN CONTROLLER FROM CB
Route::group([
    'middleware' => ['web', CBBackend::class],
    'prefix' => cbAdminPath(),
    'namespace' => ctrlNamespace(),
], function () use ($dashboardMenu) {

    if (! $dashboardMenu) {
        return;
    }
    $dashboardType = $dashboardMenu->type;
    $path = $dashboardMenu->path;

    if ($dashboardType == MenuTypes::Statistic) {
        Route::get('/', cbModulesNS('StatisticModule\\AdminStatisticBuilderController@getDashboard'));
    } elseif ($dashboardType == MenuTypes::Module) {
        $module = CRUDBooster::first('cms_moduls', ['path' => $path]);
        Route::get('/', $module->controller.'@getIndex');
    } elseif ($dashboardType == MenuTypes::route) {
        $action = str_replace("Controller", "Controller@", $path);
        $action = str_replace(['Get', 'Post'], ['get', 'post'], $action);
        Route::get('/', $action);
    } elseif ($dashboardType == MenuTypes::ControllerMethod) {
        Route::get('/', $path);
    } elseif ($dashboardType == MenuTypes::url) {
        redirect($path);
    }
});

Route::group([
    'middleware' => ['web', CBBackend::class],
    'prefix' => cbAdminPath(),
    'namespace' => cbControllersNS(),
], function () use ($dashboardMenu) {
    if (! $dashboardMenu) {
        CRUDBooster::routeController('/', cbModulesNS('AuthModule\AuthController'));
    }
});
