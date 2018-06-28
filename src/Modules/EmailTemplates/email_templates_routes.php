<?php
Route::group([
    'middleware' => ['web', \Crocodicstudio\Crudbooster\CBCoreModule\middlewares\CBSuperadmin::class],
    'prefix' => cbAdminPath(),
    'namespace' => cbModulesNS('EmailTemplates'),
], function (){
    Route::get('email-templates/', ['uses' => 'AdminEmailTemplatesController@getIndex', 'as' => 'AdminEmailTemplatesControllerGetIndex']);
    Route::get('email-templates/data-query', ['uses' => 'AdminEmailTemplatesController@getDataQuery', 'as' => 'AdminEmailTemplatesControllerGetDataQuery']);
    Route::get('email-templates/data-table', ['uses' => 'AdminEmailTemplatesController@getDataTable', 'as' => 'AdminEmailTemplatesControllerGetDataTable']);
    Route::get('email-templates/data-modal-datatable', ['uses' => 'AdminEmailTemplatesController@getDataModalDatatable', 'as' => 'AdminEmailTemplatesControllerGetDataModalDatatable']);
    Route::get('email-templates/update-single', ['uses' => 'AdminEmailTemplatesController@getUpdateSingle', 'as' => 'AdminEmailTemplatesControllerGetUpdateSingle']);
    Route::get('email-templates/add', ['uses' => 'AdminEmailTemplatesController@getAdd', 'as' => 'AdminEmailTemplatesControllerGetAdd']);
    Route::get('email-templates/edit/{id?}', ['uses' => 'AdminEmailTemplatesController@getEdit', 'as' => 'AdminEmailTemplatesControllerGetEdit']);
    Route::get('email-templates/delete/{id?}', ['uses' => 'AdminEmailTemplatesController@getDelete', 'as' => 'AdminEmailTemplatesControllerGetDelete']);
    Route::get('email-templates/detail/{id?}', ['uses' => 'AdminEmailTemplatesController@getDetail', 'as' => 'AdminEmailTemplatesControllerGetDetail']);
    Route::get('email-templates/delete-image/{id?}', ['uses' => 'AdminEmailTemplatesController@getDeleteImage', 'as' => 'AdminEmailTemplatesControllerGetDeleteImage']);

    Route::post('email-templates/find-data', ['uses' => 'AdminEmailTemplatesController@postFindData', 'as' => 'AdminEmailTemplatesControllerPostFindData',]);
    Route::post('email-templates/add-save', ['uses' => 'AdminEmailTemplatesController@postAddSave', 'as' => 'AdminEmailTemplatesControllerPostAddSave',]);
    Route::post('email-templates/edit-save/{id?}', ['uses' => 'AdminEmailTemplatesController@postEditSave', 'as' => 'AdminEmailTemplatesControllerPostEditSave',]);
    Route::post('email-templates/action-selected', ['uses' => 'AdminEmailTemplatesController@postActionSelected', 'as' => 'AdminEmailTemplatesControllerPostActionSelected',]);
});
