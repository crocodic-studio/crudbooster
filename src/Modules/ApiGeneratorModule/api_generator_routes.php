<?php

use crocodicstudio\crudbooster\middlewares\CBSuperadmin;

$namespace = cbModulesNS('ApiGeneratorModule');

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

    Route::get('api-generator/download-postman', 'DownloadPostman@getDownloadPostman')->name('AdminApiGeneratorControllerGetDownloadPostman');
    Route::get('api-generator/secret-key', 'AdminApiKeyController@getSecretKey')->name('AdminApiKeyControllerGetIndex');
    Route::get('api-generator/generator', 'AdminApiGeneratorController@getGenerator')->name('AdminApiGeneratorControllerGetGenerator');
    Route::get('api-generator/edit-api/{id?}', 'AdminApiGeneratorController@getEditApi')->name('AdminApiGeneratorControllerGetEditApi');
    Route::get('api-generator/generate-secret-key', 'AdminApiKeyController@getGenerateSecretKey')->name('AdminApiGeneratorControllerGetGenerateSecretKey');
    Route::get('api-generator/secret-key/status-apikey', 'AdminApiKeyController@getStatusApikey')->name('AdminApiGeneratorControllerGetStatusApikey');
    Route::get('api-generator/secret-key/delete-api-key', 'AdminApiKeyController@getDeleteApiKey')->name('AdminApiGeneratorControllerGetDeleteApiKey');
    Route::get('api-generator/column-table/{one?}/{type?}', 'AdminColumnsTableController@getColumnTable')->name('AdminApiGeneratorControllerGetColumnTable');
    Route::get('api-generator/delete-api/{id?}', 'AdminApiGeneratorController@getDeleteApi')->name('AdminApiGeneratorControllerGetDeleteApi');
    Route::get('api-generator/export-data', 'AdminApiGeneratorController@getExportData')->name('AdminApiGeneratorControllerGetExportData');
    Route::get('api-generator/data-query', 'AdminApiGeneratorController@getDataQuery')->name('AdminApiGeneratorControllerGetDataQuery');
    Route::get('api-generator/data-table', 'AdminApiGeneratorController@getDataTable')->name('AdminApiGeneratorControllerGetDataTable');
    Route::get('api-generator/data-modal-datatable', 'AdminApiGeneratorController@getDataModalDatatable')->name('AdminApiGeneratorControllerGetDataModalDatatable');
    Route::get('api-generator/update-single', 'AdminApiGeneratorController@getUpdateSingle')->name('AdminApiGeneratorControllerGetUpdateSingle');
    Route::get('api-generator/add', 'AdminApiGeneratorController@getAdd')->name('AdminApiGeneratorControllerGetAdd');
    Route::get('api-generator/edit/{id?}', 'AdminApiGeneratorController@getEdit')->name('AdminApiGeneratorControllerGetEdit');
    Route::get('api-generator/delete/{id?}', 'AdminApiGeneratorController@getDelete')->name('AdminApiGeneratorControllerGetDelete');
    Route::get('api-generator/detail/{id?}', 'AdminApiGeneratorController@getDetail')->name('AdminApiGeneratorControllerGetDetail');
    Route::get('api-generator/import-data', 'AdminApiGeneratorController@getImportData')->name('AdminApiGeneratorControllerGetImportData');
    Route::get('api-generator/delete-image', 'AdminApiGeneratorController@getDeleteImage')->name('AdminApiGeneratorControllerGetDeleteImage');

    Route::post('api-generator/export-data', 'AdminApiGeneratorController@postExportData')->name('AdminApiGeneratorControllerPostExportData');
    Route::post('api-generator/save-api-custom', 'AdminApiGeneratorController@postSaveApiCustom')->name('AdminApiGeneratorControllerPostSaveApiCustom');
    Route::post('api-generator/find-data', 'AdminApiGeneratorController@postFindData')->name('AdminApiGeneratorControllerPostFindData');
    Route::post('api-generator/add-save', 'AdminApiGeneratorController@postAddSave')->name('AdminApiGeneratorControllerPostAddSave');
    Route::post('api-generator/edit-save/{id?}', 'AdminApiGeneratorController@postEditSave')->name('AdminApiGeneratorControllerPostEditSave');
    //Route::post('api-generator/done-import', 'AdminApiGeneratorController@postDoneImport')->name('AdminApiGeneratorControllerPostDoneImport');
    Route::post('api-generator/do-import-chunk', 'AdminApiGeneratorController@postDoImportChunk')->name('AdminApiGeneratorControllerPostDoImportChunk');
    //Route::post('api-generator/do-upload-import-data', 'AdminApiGeneratorController@postDoUploadImportData')->name('AdminApiGeneratorControllerPostDoUploadImportData');
    Route::post('api-generator/action-selected', 'AdminApiGeneratorController@postActionSelected')->name('AdminApiGeneratorControllerPostActionSelected');
    //Route::post('api-generator/upload-summernote', 'AdminApiGeneratorController@postUploadSummernote')->name('AdminApiGeneratorControllerPostUploadSummernote');
    //Route::post('api-generator/upload-file', 'AdminApiGeneratorController@postUploadFile')->name('AdminApiGeneratorControllerPostUploadFile');
});


/* ROUTER FOR API GENERATOR */

Route::group(['middleware' => ['api', \crocodicstudio\crudbooster\Modules\ApiGeneratorModule\ApiKeysRepository::class], 'namespace' => ctrlNamespace()], function () {
    //Router for custom api defeault
    $dir = scandir(controllers_dir());
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