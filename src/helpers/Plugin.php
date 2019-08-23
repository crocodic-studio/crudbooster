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

    public function getAllThemes() {
        $plugins_from_user = $this->getAll();
        $plugins_from_master = $this->getAll(__DIR__."/../views/themes");
        $result = [];
        $plugins = array_merge($plugins_from_master, $plugins_from_user);
        foreach($plugins as $plugin) {
            if($plugin['type'] == "theme") {
                $result[] = $plugin;
            }
        }
        return $result;
    }

    public function getAll($path = null)
    {
        $path = ($path)?:app_path("CBPlugins");
        $plugins = scandir($path);

        $result = [];
        foreach($plugins as $plugin) {
            if($plugin != "." && $plugin != "..") {
                $basename = basename($plugin);
                $row = json_decode(file_get_contents($path.DIRECTORY_SEPARATOR.$plugin.DIRECTORY_SEPARATOR."plugin.json"), true);
                if($row) {
                    try {
                        $row['url'] = route($basename."ControllerGetIndex");
                    } catch (\Exception $e) {
                        $row['url'] = null;
                    }
                    $result[] = $row;
                }
            }
        }

        $result = collect($result)->sortBy("name")->values()->all();

        return $result;
    }
}