<?php

use crocodicstudio\crudbooster\middlewares\CBSuperadmin;

$namespace = '\crocodicstudio\crudbooster\Modules\ApiGeneratorModule';

Route::group(['middleware' => ['web'], 'namespace' => $namespace], function () {
    Route::get('api/doc', ['uses' => 'AdminApiGeneratorController@apiDocumentation', 'as' => 'apiDocumentation']);
    Route::get('download-documentation-postman', ['uses' => 'AdminApiGeneratorController@getDownloadPostman', 'as' => 'downloadDocumentationPostman']);
});

Route::group([
    'middleware' => ['web', CBSuperadmin::class],
    'prefix' => cbAdminPath(),
    'namespace' => $namespace,
], function () {
    Route::get('api-generator/', ['uses' => 'AdminApiGeneratorController@getIndex', 'as' => 'AdminApiGeneratorControllerGetIndex']);

    Route::get('api-generator/download-postman', ['uses' => 'AdminApiGeneratorController@getDownloadPostman', 'as' => 'AdminApiGeneratorControllerGetDownloadPostman']);
    Route::get('api-generator/screet-key', ['uses' => 'AdminApiGeneratorController@getScreetKey', 'as' => 'AdminApiGeneratorControllerGetScreetKey']);
    Route::get('api-generator/generator', ['uses' => 'AdminApiGeneratorController@getGenerator', 'as' => 'AdminApiGeneratorControllerGetGenerator']);
    Route::get('api-generator/edit-api/{id?}', ['uses' => 'AdminApiGeneratorController@getEditApi', 'as' => 'AdminApiGeneratorControllerGetEditApi']);
    Route::get('api-generator/generate-screet-key', ['uses' => 'AdminApiGeneratorController@getGenerateScreetKey', 'as' => 'AdminApiGeneratorControllerGetGenerateScreetKey']);
    Route::get('api-generator/status-apikey', ['uses' => 'AdminApiGeneratorController@getStatusApikey', 'as' => 'AdminApiGeneratorControllerGetStatusApikey']);
    Route::get('api-generator/delete-api-key', ['uses' => 'AdminApiGeneratorController@getDeleteApiKey', 'as' => 'AdminApiGeneratorControllerGetDeleteApiKey']);
    Route::get('api-generator/column-table/{one?}/{type?}', ['uses' => 'AdminApiGeneratorController@getColumnTable', 'as' => 'AdminApiGeneratorControllerGetColumnTable']);
    Route::get('api-generator/delete-api/{id?}', ['uses' => 'AdminApiGeneratorController@getDeleteApi', 'as' => 'AdminApiGeneratorControllerGetDeleteApi']);
    Route::get('api-generator/export-data', ['uses' => 'AdminApiGeneratorController@getExportData', 'as' => 'AdminApiGeneratorControllerGetExportData']);
    Route::get('api-generator/data-query', ['uses' => 'AdminApiGeneratorController@getDataQuery', 'as' => 'AdminApiGeneratorControllerGetDataQuery']);
    Route::get('api-generator/data-table', ['uses' => 'AdminApiGeneratorController@getDataTable', 'as' => 'AdminApiGeneratorControllerGetDataTable']);
    Route::get('api-generator/data-modal-datatable', ['uses' => 'AdminApiGeneratorController@getDataModalDatatable', 'as' => 'AdminApiGeneratorControllerGetDataModalDatatable']);
    Route::get('api-generator/update-single', ['uses' => 'AdminApiGeneratorController@getUpdateSingle', 'as' => 'AdminApiGeneratorControllerGetUpdateSingle']);
    Route::get('api-generator/add', ['uses' => 'AdminApiGeneratorController@getAdd', 'as' => 'AdminApiGeneratorControllerGetAdd']);
    Route::get('api-generator/edit/{id?}', ['uses' => 'AdminApiGeneratorController@getEdit', 'as' => 'AdminApiGeneratorControllerGetEdit']);
    Route::get('api-generator/delete/{id?}', ['uses' => 'AdminApiGeneratorController@getDelete', 'as' => 'AdminApiGeneratorControllerGetDelete']);
    Route::get('api-generator/detail/{id?}', ['uses' => 'AdminApiGeneratorController@getDetail', 'as' => 'AdminApiGeneratorControllerGetDetail']);
    Route::get('api-generator/import-data', ['uses' => 'AdminApiGeneratorController@getImportData', 'as' => 'AdminApiGeneratorControllerGetImportData']);
    Route::get('api-generator/delete-image', ['uses' => 'AdminApiGeneratorController@getDeleteImage', 'as' => 'AdminApiGeneratorControllerGetDeleteImage']);

    Route::post('api-generator/export-data', ['uses' => 'AdminApiGeneratorController@postExportData', 'as' => 'AdminApiGeneratorControllerPostExportData',]);
    Route::post('api-generator/save-api-custom', ['uses' => 'AdminApiGeneratorController@postSaveApiCustom', 'as' => 'AdminApiGeneratorControllerPostSaveApiCustom',]);
    Route::post('api-generator/find-data', ['uses' => 'AdminApiGeneratorController@postFindData', 'as' => 'AdminApiGeneratorControllerPostFindData',]);
    Route::post('api-generator/add-save', ['uses' => 'AdminApiGeneratorController@postAddSave', 'as' => 'AdminApiGeneratorControllerPostAddSave',]);
    Route::post('api-generator/edit-save/{id?}', ['uses' => 'AdminApiGeneratorController@postEditSave', 'as' => 'AdminApiGeneratorControllerPostEditSave',]);
    Route::post('api-generator/done-import', ['uses' => 'AdminApiGeneratorController@postDoneImport', 'as' => 'AdminApiGeneratorControllerPostDoneImport',]);
    Route::post('api-generator/do-import-chunk', ['uses' => 'AdminApiGeneratorController@postDoImportChunk', 'as' => 'AdminApiGeneratorControllerPostDoImportChunk',]);
    Route::post('api-generator/do-upload-import-data', ['uses' => 'AdminApiGeneratorController@postDoUploadImportData', 'as' => 'AdminApiGeneratorControllerPostDoUploadImportData',]);
    Route::post('api-generator/action-selected', ['uses' => 'AdminApiGeneratorController@postActionSelected', 'as' => 'AdminApiGeneratorControllerPostActionSelected',]);
    Route::post('api-generator/upload-summernote', ['uses' => 'AdminApiGeneratorController@postUploadSummernote', 'as' => 'AdminApiGeneratorControllerPostUploadSummernote',]);
    Route::post('api-generator/upload-file', ['uses' => 'AdminApiGeneratorController@postUploadFile', 'as' => 'AdminApiGeneratorControllerPostUploadFile',]);
});


/* ROUTER FOR API GENERATOR */

Route::group(['middleware' => ['api', '\crocodicstudio\crudbooster\middlewares\CBAuthAPI'], 'namespace' => ctrlNamespace()], function () {
    //Router for custom api defeault

    $dir = scandir(base_path(controllers_dir()));
    foreach ($dir as $Ctrl) {
        $Ctrl = str_replace('.php', '', $Ctrl);
        $names = array_filter(preg_split('/(?=[A-Z])/', str_replace('Controller', '', $Ctrl)));
        $names = strtolower(implode('_', $names));

        if (substr($names, 0, 4) == 'api_') {
            $names = str_replace('api_', '', $names);
            Route::any('api/'.$names, $Ctrl.'@execute_api');
        }
    }
});