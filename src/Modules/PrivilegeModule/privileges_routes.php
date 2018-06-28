<?php
Route::group([
    'middleware' => ['web', \Crocodicstudio\Crudbooster\CBCoreModule\middlewares\CBSuperadmin::class],
    'prefix' => cbAdminPath(),
    'namespace' => cbModulesNS('PrivilegeModule'),
], function () {
    Route::get('privileges/', 'AdminPrivilegesController@getIndex')->name('AdminPrivilegesControllerGetIndex');
    Route::get('privileges/add', 'AdminPrivilegesController@getAdd')->name('AdminPrivilegesControllerGetAdd');
    Route::get('privileges/edit/{id?}', 'AdminPrivilegesController@getEdit')->name('AdminPrivilegesControllerGetEdit');
    Route::get('privileges/update-single', 'AdminPrivilegesController@getUpdateSingle')->name('AdminPrivilegesControllerGetUpdateSingle');
    Route::get('privileges/detail/{id?}', 'AdminPrivilegesController@getDetail')->name('AdminPrivilegesControllerGetDetail');
    Route::get('privileges/delete/{id?}', 'AdminPrivilegesController@getDelete')->name('AdminPrivilegesControllerGetDelete');
    Route::get('privileges/data-query', 'AdminPrivilegesController@getDataQuery')->name('AdminPrivilegesControllerGetDataQuery');
    Route::get('privileges/data-table', 'AdminPrivilegesController@getDataTable')->name('AdminPrivilegesControllerGetDataTable');
    Route::get('privileges/data-modal-datatable', 'AdminPrivilegesController@getDataModalDatatable')->name('AdminPrivilegesControllerGetDataModalDatatable');
    Route::post('privileges/add-save', 'AdminPrivilegesController@postAddSave')->name('AdminPrivilegesControllerPostAddSave');
    Route::post('privileges/edit-save/{id?}', 'AdminPrivilegesController@postEditSave')->name('AdminPrivilegesControllerPostEditSave');
    Route::post('privileges/action-selected', 'AdminPrivilegesController@postActionSelected')->name('AdminPrivilegesControllerPostActionSelected');
    Route::post('privileges/find-data', 'AdminPrivilegesController@postFindData')->name('AdminPrivilegesControllerPostFindData');
});
