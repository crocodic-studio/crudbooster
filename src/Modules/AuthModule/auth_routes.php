<?php
$namespace = cbModulesNS('AuthModule');
$prefix = cbAdminPath();

Route::group(['middleware' => ['web'], 'prefix' => $prefix, 'namespace' => $namespace], function () {
    Route::post('forgot', 'ForgetPasswordController@postForgot')->name('postForgot');
    Route::get('forgot', 'ForgetPasswordController@getForgot')->name('getForgot');
    //Route::post('register', 'AuthController@postRegister')->name('postRegister');
    //Route::get('register', 'AuthController@getRegister')->name('getRegister');
    Route::get('logout', 'LoginController@logout')->name('getLogout');
    Route::post('login', 'LoginController@postLogin')->name('postLogin');
    Route::get('login', 'LoginController@login')->name('getLogin');
});