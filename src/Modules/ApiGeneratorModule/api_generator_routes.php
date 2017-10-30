<?php

use crocodicstudio\crudbooster\middlewares\CBSuperadmin;

$namespace = '\crocodicstudio\crudbooster\Modules\ApiGeneratorModule';

Route::group(['middleware' => ['web'], 'namespace' => $namespace], function () {
    Route::get('api/doc', ['uses' => 'AdminApiGeneratorController@apiDocumentation', 'as' => 'apiDocumentation']);
    Route::get('download-documentation-postman', ['uses' => 'AdminApiGeneratorController@getDownloadPostman', 'as' => 'downloadDocumentationPostman']);
});

Route::group([
    'middleware' => ['web', CBSuperadmin::class],
    'prefix' => cbConfig('ADMIN_PATH'),
    'namespace' => $namespace,
], function () use ($namespace) {
    CRUDBooster::routeController('api-generator', 'AdminApiGeneratorController', $namespace);
});