<?php

use crocodicstudio\crudbooster\middlewares\CBBackend;

Route::group([
    'middleware' => ['web', CBBackend::class],
    'prefix' => cbAdminPath(),
    'namespace' => cbModulesNS('NotificationsModule'),
], function () {
    Route::get('notifications/', ['uses' => 'AdminNotificationsController@getIndex', 'as' => 'AdminNotificationsControllerGetIndex']);
    Route::get('notifications/latest-json', ['uses' => 'AdminNotificationsController@getLatestJson', 'as' => 'AdminNotificationsControllerGetLatestJson']);
    Route::get('notifications/read/{id?}', ['uses' => 'AdminNotificationsController@getRead', 'as' => 'AdminNotificationsControllerGetRead']);
    Route::get('notifications/data-query', ['uses' => 'AdminNotificationsController@getDataQuery', 'as' => 'AdminNotificationsControllerGetDataQuery']);
    Route::get('notifications/update-single', ['uses' => 'AdminNotificationsController@getUpdateSingle', 'as' => 'AdminNotificationsControllerGetUpdateSingle']);
    Route::get('notifications/add', ['uses' => 'AdminNotificationsController@getAdd', 'as' => 'AdminNotificationsControllerGetAdd']);
    Route::get('notifications/edit/{id?}', ['uses' => 'AdminNotificationsController@getEdit', 'as' => 'AdminNotificationsControllerGetEdit']);
    Route::get('notifications/delete/{id?}', ['uses' => 'AdminNotificationsController@getDelete', 'as' => 'AdminNotificationsControllerGetDelete']);
    Route::get('notifications/detail/{id?}', ['uses' => 'AdminNotificationsController@getDetail', 'as' => 'AdminNotificationsControllerGetDetail']);
});