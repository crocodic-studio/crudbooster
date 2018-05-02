<?php

namespace crocodicstudio\crudbooster\CBCoreModule;

use Route;

class CbRouter
{
    public static function routeController($prefix, $controller, $namespace = null)
    {
        $prefix = trim($prefix, '/').'/';

        try {
            Route::get($prefix, ['uses' => $controller.'@getIndex', 'as' => $controller.'GetIndex']);
            $ctrl = self::getControllerPath($controller, $namespace);
            foreach (self::getControllerMethods($ctrl) as $method) {
                $wildcards = self::getWildCard($method->getNumberOfParameters());
                self::setRoute($prefix, $controller, $method->name, $wildcards);
            }
        } catch (\Exception $e) {

        }
    }

    /**
     * @param $prefix
     * @param $controller
     * @param $method
     * @param $wildcards
     */
    private static function routePost($prefix, $controller, $method, $wildcards)
    {
        $methodName = str_after($method, 'post');
        $slug = self::makeSlug($methodName);
        Route::post($prefix.$slug.$wildcards, $controller.'@'.$method)
            ->name($controller.'Post'.$methodName);
    }

    /**
     * @param $prefix
     * @param $controller
     * @param $method
     * @param $wildcards
     */
    private static function routeGet($prefix, $controller, $method, $wildcards)
    {
        $methodName = str_after($method, 'get');
        $slug = self::makeSlug($methodName);
        $slug = ($slug == 'index') ? '' : $slug;
        Route::get($prefix.$slug.$wildcards, $controller.'@'.$method)
            ->name($controller.'Get'.$methodName);
    }

    /**
     * @param $ctrl
     * @return array|\ReflectionMethod[]
     * @throws \ReflectionException
     */
    private static function getControllerMethods($ctrl)
    {
        $methods = (new \ReflectionClass($ctrl))->getMethods(\ReflectionMethod::IS_PUBLIC);

        return array_filter($methods, function ($method) {
            return ($method->class !== 'Illuminate\Routing\Controller' && $method->name !== 'getIndex');
        });
    }

    /**
     * @param $controller
     * @param $namespace
     * @return string
     */
    private static function getControllerPath($controller, $namespace)
    {
        $ns = $namespace ?: ctrlNamespace();
        $ctrl = $ns.'\\'.$controller;

        return $ctrl;
    }

    /**
     * @param $prefix
     * @param $controller
     * @param $methodName
     * @param $wildcards
     */
    private static function setRoute($prefix, $controller, $methodName, $wildcards)
    {
        if (starts_with($methodName, 'get')) {
            self::routeGet($prefix, $controller, $methodName, $wildcards);
        } elseif (starts_with($methodName, 'post')) {
            self::routePost($prefix, $controller, $methodName, $wildcards);
        }
    }

    /**
     * @param $methodName
     * @return array|string
     */
    private static function makeSlug($methodName)
    {
        $slug = preg_split('/(?=[A-Z])/', $methodName) ?: [];
        $slug = array_filter($slug);
        $slug = strtolower(implode('-', $slug));

        return $slug;
    }

    private static function getWildCard($count)
    {
        $wildcards = explode('/', '{one?}/{two?}/{three?}/{four?}/{five?}');
        $fr = array_splice($wildcards, 0, $count);
        return  implode('/', $fr);
    }
}