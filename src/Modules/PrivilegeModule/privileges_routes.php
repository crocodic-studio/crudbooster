<?php
Route::group([
    'middleware' => ['web', \crocodicstudio\crudbooster\middlewares\CBSuperadmin::class],
    'prefix' => cbConfig('ADMIN_PATH'),
    'namespace' => '\crocodicstudio\crudbooster\Modules\PrivilegeModule',
], function () {
    CRUDBooster::routeController('privileges', 'AdminPrivilegesController', '\crocodicstudio\crudbooster\Modules\PrivilegeModule');
});
