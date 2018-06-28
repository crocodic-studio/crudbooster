<?php

namespace Crocodicstudio\Crudbooster\CBCoreModule;

use Route;

class CbRouter
{
    public function routeController(string $prefix, string $controller, string $namespace = '')
    {
        $prefix = trim($prefix, '/').'/';

        try {
            Route::get($prefix, $controller.'@getIndex')->name($controller.'GetIndex');
            $ctrl = $this->getControllerPath($controller, $namespace);
            foreach ($this->getControllerMethods($ctrl) as $method) {
                $wildcards = $this->getWildCard($method->getNumberOfParameters());
                $this->setRoute($prefix, $controller, $method->name, $wildcards);
            }
        } catch (\Exception $e) {
            dd($e);
        }
    }

    /**
     * @param $controller
     * @param $namespace
     * @return string
     */
    private function getControllerPath(string $controller, string $namespace) : string
    {
        if (starts_with($controller, '\\')) {
            return $controller;
        }
        $ns = $namespace ?: ctrlNamespace();
        $ctrl = $ns.'\\'.$controller;

        return $ctrl;
    }

    /**
     * @param $ctrl
     * @return array|\ReflectionMethod[]
     * @throws \ReflectionException
     */
    private function getControllerMethods(string $ctrl) : array
    {
        $methods = (new \ReflectionClass($ctrl))->getMethods(\ReflectionMethod::IS_PUBLIC);

        return array_filter($methods, function ($method) {
            return ($method->class !== 'Illuminate\Routing\Controller' && $method->name !== 'getIndex');
        });
    }

    /**
     * @param int $count
     * @return string
     */
    private function getWildCard(int $count) : string
    {
        $wildcards = ['{one?}', '{two?}', '{three?}', '{four?}', '{five?}'];

        return '/'.implode('/', array_splice($wildcards, 0, $count));
    }

    /**
     * @param $prefix
     * @param $controller
     * @param $methodName
     * @param $wildcards
     */
    private function setRoute(string $prefix, string $controller, string $methodName, string $wildcards)
    {
        if (starts_with($methodName, 'get')) {
            $this->routeGet($prefix, $controller, $methodName, $wildcards);
        } elseif (starts_with($methodName, 'post')) {
            $this->routePost($prefix, $controller, $methodName, $wildcards);
        }
    }

    /**
     * @param $prefix
     * @param $controller
     * @param $method
     * @param $wildcards
     */
    private function routeGet(string $prefix, string $controller, string $method, string $wildcards)
    {
        $methodName = str_after($method, 'get');
        $slug = $this->makeSlug($methodName);
        $slug = ($slug == 'index') ? '' : $slug;
        Route::get($prefix.$slug.$wildcards, $controller.'@'.$method)->name($controller.'Get'.$methodName);
    }

    /**
     * @param string $methodName
     * @return array|string
     */
    private function makeSlug(string $methodName): string
    {
        $slug = preg_split('/(?=[A-Z])/', $methodName) ?: [];
        $slug = array_filter($slug);
        $slug = strtolower(implode('-', $slug));

        return $slug;
    }

    /**
     * @param $prefix
     * @param $controller
     * @param $method
     * @param $wildcards
     */
    private function routePost(string $prefix, string $controller, string $method, string $wildcards)
    {
        $methodName = str_after($method, 'post');
        $slug = $this->makeSlug($methodName);
        Route::post($prefix.$slug.$wildcards, $controller.'@'.$method)->name($controller.'Post'.$methodName);
    }
}