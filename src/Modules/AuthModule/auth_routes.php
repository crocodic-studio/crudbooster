<?php
$namespace = '\crocodicstudio\crudbooster\Modules\AuthModule';
$prefix = cbAdminPath();

Route::group(['middleware' => ['web'], 'prefix' => $prefix, 'namespace' => $namespace], function () {
    Route::get('/', ['uses' => 'AuthController@getIndex', 'as' => 'AuthControllerGetIndex']);
    Route::post('unlock-screen', ['uses' => 'AuthController@postUnlockScreen', 'as' => 'postUnlockScreen']);
    Route::get('lock-screen', ['uses' => 'AuthController@getLockscreen', 'as' => 'getLockScreen']);
    Route::post('forgot', ['uses' => 'AuthController@postForgot', 'as' => 'postForgot']);
    Route::get('forgot', ['uses' => 'AuthController@getForgot', 'as' => 'getForgot']);
    Route::post('register', ['uses' => 'AuthController@postRegister', 'as' => 'postRegister']);
    Route::get('register', ['uses' => 'AuthController@getRegister', 'as' => 'getRegister']);
    Route::get('logout', ['uses' => 'AuthController@getLogout', 'as' => 'getLogout']);
    Route::post('login', ['uses' => 'AuthController@postLogin', 'as' => 'postLogin']);
    Route::get('login', ['uses' => 'AuthController@getLogin', 'as' => 'getLogin']);
});