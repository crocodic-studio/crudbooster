<?php
$namespace = cbModulesNS('AuthModule');
$prefix = cbAdminPath();

Route::group(['middleware' => ['web'], 'prefix' => $prefix, 'namespace' => $namespace], function () {
    Route::get('/', 'AuthController@getIndex')->name('AuthControllerGetIndex');
    Route::post('unlock-screen', 'AuthController@postUnlockScreen')->name('postUnlockScreen');
    Route::get('lock-screen', 'AuthController@getLockscreen')->name('getLockScreen');
    Route::post('forgot', 'AuthController@postForgot')->name('postForgot');
    Route::get('forgot', 'AuthController@getForgot')->name('getForgot');
    Route::post('register', 'AuthController@postRegister')->name('postRegister');
    Route::get('register', 'AuthController@getRegister')->name('getRegister');
    Route::get('logout', 'AuthController@getLogout')->name('getLogout');
    Route::post('login', 'LoginController@postLogin')->name('postLogin');
    Route::get('login', 'AuthController@getLogin')->name('getLogin');
});