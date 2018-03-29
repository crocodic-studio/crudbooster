<?php

namespace crocodicstudio\crudbooster\CBCoreModule;

use Route;

class RouteController
{
    public static function routeController($prefix, $controller, $namespace = null)
    {
        $prefix = trim($prefix, '/').'/';

        $namespace = ($namespace) ?: ctrlNamespace();

        try {
            Route::get($prefix, ['uses' => $controller.'@getIndex', 'as' => $controller.'GetIndex']);

            $controller_methods = (new \ReflectionClass($namespace.'\\'.$controller))->getMethods(\ReflectionMethod::IS_PUBLIC);
            $wildcards = '/{one?}/{two?}/{three?}/{four?}/{five?}';
            foreach ($controller_methods as $method) {

                if ($method->class == 'Illuminate\Routing\Controller' || $method->name == 'getIndex') {
                    continue;
                }
                if (substr($method->name, 0, 3) == 'get') {
                    self::routeGet($prefix, $controller, $method, $wildcards);
                } elseif (substr($method->name, 0, 4) == 'post') {
                    self::routePost($prefix, $controller, $method, $wildcards);
                }
            }
        } catch (\Exception $e) {

        }
    }

    /**
     * @param $prefix
     * @param $controller
     * @param $method
     * @param $wildcards
     * @return array
     */
    private static function routePost($prefix, $controller, $method, $wildcards)
    {
        $methodName = substr($method->name, 4);
        $slug = array_filter(preg_split('/(?=[A-Z])/', $methodName));
        $slug = strtolower(implode('-', $slug));
        Route::post($prefix.$slug.$wildcards, [
            'uses' => $controller.'@'.$method->name,
            'as' => $controller.'Post'.$methodName,
        ]);
    }

    /**
     * @param $prefix
     * @param $controller
     * @param $method
     * @param $wildcards
     */
    private static function routeGet($prefix, $controller, $method, $wildcards)
    {
        $methodName = substr($method->name, 3);
        $slug = array_filter(preg_split('/(?=[A-Z])/', $methodName));
        $slug = strtolower(implode('-', $slug));
        $slug = ($slug == 'index') ? '' : $slug;
        Route::get($prefix.$slug.$wildcards,
            ['uses' => $controller.'@'.$method->name,
            'as' => $controller.'Get'.$methodName]);
    }
}