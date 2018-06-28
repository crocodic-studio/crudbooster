<?php

namespace Crocodicstudio\Crudbooster\Helpers;

class CbStructure
{
    public static function componentsPath(string $type = ''): string
    {
        $componentPath = implode(DIRECTORY_SEPARATOR, ['vendor', 'crocodicstudio', 'crudbooster', 'src', 'views', 'form', 'type_components', $type]);

        return base_path($componentPath);
    }

    public static function publishedComponentsPath(string $type = ''): string
    {
        $Path = implode(DIRECTORY_SEPARATOR, ['views', 'vendor', 'crudbooster', 'type_components', $type]);

        return resource_path($Path);
    }

    public static function controllerPath(string $controller): string
    {
        return self::controllersDir().$controller.'.php';
    }

    public static function controllersDir(): string
    {
        $_ = DIRECTORY_SEPARATOR;

        return app_path('Http'.$_.'Controllers'.$_);
    }

    public static function cbModulesNS(string $path = ''): string
    {
        return '\Crocodicstudio\Crudbooster\Modules\\'.$path;
    }

    public static function cbControllersNS(): string
    {
        return '\Crocodicstudio\Crudbooster\controllers';
    }

    public static function ctrlNamespace(): string
    {
        return 'App\Http\Controllers';
    }
}