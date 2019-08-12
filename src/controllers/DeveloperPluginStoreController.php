<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 4/25/2019
 * Time: 9:28 PM
 */

namespace crocodicstudio\crudbooster\controllers;

use crocodicstudio\crudbooster\exceptions\CBValidationException;
use crocodicstudio\crudbooster\helpers\ModuleGenerator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DeveloperPluginStoreController extends Controller
{

    private $view = "crudbooster::dev_layouts.modules.plugin";

    public function __construct()
    {
        view()->share(['page_title'=>'Plugin Store']);
    }


    public function getIndex() {
        $data = [];
        $data['result'] = $this->fetchPluginData();
        return view($this->view.'.index',$data);
    }

    public function getUninstall($key)
    {
        $pluginData = $this->fetchPluginData();

        if(isset($pluginData[$key])) {
            if(file_exists(app_path("CBPlugins/".$key))) {
                rrmdir(app_path("CBPlugins/".$key));
                return cb()->redirectBack('Plugin has been uninstalled!','success');
            }else{
                return cb()->redirectBack('Failed to uninstall, plugin is not found');
            }
        }else {
            return cb()->redirectBack('Failed to uninstall, plugin key is not found');
        }
    }

    public function getInstall($key)
    {
        $pluginData = $this->fetchPluginData();

        if(isset($pluginData[$key])) {
            $plugin = $pluginData[$key];

            // Create temp file of zip plugin
            $temp = tmpfile();
            fwrite($temp, file_get_contents($plugin['url']));
            $filename = stream_get_meta_data($temp)['uri'];

            // Extract zip plugin
            $zip = new \ZipArchive;
            $res = $zip->open($filename);
            if ($res === TRUE) {
                $zip->extractTo(app_path('CBPlugins'));
                $dirName = $zip->getNameIndex(0);
                $zip->close();
                fclose($temp);

                // Rename
                rename(app_path("CBPlugins/".$dirName), app_path("CBPlugins/".$key));

                return cb()->redirectBack('Install / update plugin has been succesfull!','success');

            } else {
                return cb()->redirectBack("Failed to install/update, can't open the plugin archive");
            }
        }else{
            return cb()->redirectBack('Failed to install/update, plugin key is not found');
        }
    }

    private function fetchPluginData()
    {
        if($data = Cache::get("plugin_store_data")) {
            return $data;
        }

        $data = file_get_contents(base64_decode("aHR0cDovL2NydWRib29zdGVyLmNvbS9hcGkvcGx1Z2luP2FjY2Vzc190b2tlbj1iVmMvWm5wWU5TWnJNVlpZT0hFNVUydHFjU1U9"));
        $result = [];
        if($data) {
            $data = json_decode($data, true);
            if($data['status']==true) {

                foreach($data['data'] as $item) {
                    $key = $item['key'];
                    $result[ $key ] = $item;
                }

                $result = collect($result)->sortBy("name")->all();

                Cache::put("plugin_store_data", $result, now()->addDays(3));
            }
        }
        return $result;
    }
}