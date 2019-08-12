<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 8/12/2019
 * Time: 11:59 PM
 */

namespace crocodicstudio\crudbooster\helpers;

use Illuminate\Support\Facades\Route;

class Plugin
{

    public static function has($key)
    {
        if(file_exists(app_path("CBPlugins/".$key))) return true;
        else return false;
    }

    public static function isNeedUpgrade($pluginKey, $versionToCompare)
    {
        $pluginJson = json_decode(file_get_contents(app_path("CBPlugins/".$pluginKey."/plugin.json")), true);
        if($pluginJson) {
            return version_compare($pluginJson['version'], $versionToCompare,"!=");
        }else{
            return false;
        }
    }

    public static function registerDefaultRoute($dir)
    {
        Route::group(['middleware' => ['web',\crocodicstudio\crudbooster\middlewares\CBDeveloper::class],
            'prefix'=>"developer/".getSetting('developer_path'),
            'namespace' => 'App\CBPlugins\\'.basename(dirname("./../".$dir)).'\Controllers'], function () use ($dir) {
            cb()->routeController("plugins/".basename(dirname("./../".$dir)),"\App\CBPlugins\\".basename(dirname("./../".$dir))."\Controllers\\".basename(dirname("./../".$dir))."Controller");
        });
    }

    public function getAll()
    {
        $plugins = scandir(app_path("CBPlugins"));

        $result = [];
        foreach($plugins as $plugin) {
            if($plugin != "." && $plugin != "..") {
                $basename = basename($plugin);
                $row = json_decode(file_get_contents(app_path("CBPlugins".DIRECTORY_SEPARATOR.$plugin.DIRECTORY_SEPARATOR."plugin.json")), true);
                if($row) {
                    $result[] = [
                        "name"=>$row['name'],
                        "version"=>$row['version'],
                        "icon"=>$row['icon'],
                        "url"=>route($basename."ControllerGetIndex")
                    ];
                }
            }
        }

        $result = collect($result)->sortBy("name")->values()->all();

        return $result;
    }
}