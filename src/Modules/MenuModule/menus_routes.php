<?php

Route::group([
    'middleware' => ['web', \crocodicstudio\crudbooster\middlewares\CBSuperadmin::class],
    'prefix' => cbConfig('ADMIN_PATH'),
    'namespace' => '\crocodicstudio\crudbooster\Modules\MenuModule',
], function () {
    CRUDBooster::routeController('menus', 'AdminMenusController', '\crocodicstudio\crudbooster\Modules\MenuModule');
});

