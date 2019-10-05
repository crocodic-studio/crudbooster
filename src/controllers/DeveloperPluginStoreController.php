<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 4/25/2019
 * Time: 9:28 PM
 */

namespace crocodicstudio\crudbooster\controllers;

use crocodicstudio\crudbooster\exceptions\CBValidationException;
use crocodicstudio\crudbooster\helpers\ComposerHelper;
use crocodicstudio\crudbooster\helpers\CurlHelper;
use crocodicstudio\crudbooster\helpers\ModuleGenerator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class DeveloperPluginStoreController extends Controller
{

    private $view = "crudbooster::dev_layouts.modules.plugin";

    public function __construct()
    {
        view()->share(['page_title'=>'Plugin Store']);
    }


    public function getIndex() {

        if(request("refresh")) {
            $this->fetchPluginData(false );
            return cb()->redirectBack("Plugin list has been refreshed!","success");
        }

        $data = [];
        $data['result'] = $this->fetchPluginData();
        return view($this->view.'.index',$data);
    }

    public function getUninstall($key)
    {
        $pluginData = $this->fetchPluginData();

        if(isset($pluginData[$key])) {
            if(file_exists(app_path("CBPlugins/".$key))) {

                if(isset($pluginData['source']) && $pluginData['source'] == 'composer') {
                    if(isset($pluginData['package'])) {
                        ComposerHelper::composerRemove($pluginData['package']);
                    }
                }

                rrmdir(app_path("CBPlugins/".$key));

                return response()->json(['status'=>true, 'message'=>'Plugin has been uninstalled!']);
            }else{
                return response()->json(['status'=>false,'message'=>'Failed to uninstall, plugin is not found']);
            }
        }else {
            return response()->json(['status'=>false,'message'=>'Failed to uninstall, plugin key is not found']);
        }
    }

    private function recursiveCopy($src,$dst) {
        $dir = opendir($src);
        @mkdir($dst);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if ( is_dir($src . '/' . $file) ) {
                    $this->recursiveCopy($src . '/' . $file,$dst . '/' . $file);
                }
                else {
                    copy($src . '/' . $file,$dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }

    public function getInstall($key)
    {
        ini_set("memory_limit","192M");
        set_time_limit(500);

        $pluginData = $this->fetchPluginData();

        try {
            if(isset($pluginData[$key])) {
                $plugin = $pluginData[$key];

                if(isset($plugin['source']) && $plugin['source'] == "composer") {

                    if(isset($plugin['package']) && isset($plugin['service_provider'])) {
                        // Make a composer
                        $output = ComposerHelper::composerRequire($plugin['package'], $plugin['service_provider']);

                        Artisan::call("migrate");

                        return response()->json(['status'=>true,'message'=>$output]);
                    } else {
                        return response()->json(['status'=>true,'message'=>'Installation is failed, there is no package and or service provider']);
                    }

                } else {
                    // Create temp file of zip plugin
                    $temp = tmpfile();
                    fwrite($temp, file_get_contents($plugin['url_download']));
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
                        if(file_exists(app_path("CBPlugins/".$key))) rrmdir(app_path("CBPlugins/".$key));
                        rename(app_path("CBPlugins/".$dirName), app_path("CBPlugins/".$key));

                        // Read Plugin JSON
                        $pluginJson = json_decode(file_get_contents(app_path("CBPlugins/".$key."/plugin.json")), true);

                        // Check if has asset
                        if($pluginJson && $pluginJson['asset']) {
                            // Check destination folder is ready
                            if(file_exists(public_path("cb_asset/".$key))) {
                                rrmdir(public_path("cb_asset/".$key));
                            }

                            // Create directory empty
                            mkdir(public_path("cb_asset/".$key));

                            // Copy asset
                            $this->recursiveCopy(app_path("CBPlugins/".$key."/".$pluginJson['asset']), public_path("cb_asset/".$key));
                        }

                        //Migrate
                        Artisan::call("migrate");

                        return response()->json(['status'=>true,'message'=>'Install / update plugin has been succesfull!']);

                    } else {
                        return response()->json(['status'=>false,'message'=>"Failed to install/update, can't open the plugin archive"]);
                    }
                }

            }else{
                return response()->json(['status'=>false,'message'=>'Failed to install/update, plugin key is not found']);
            }
        } catch (\Exception $e) {
            return response()->json(['status'=>false,'message'=>'Something went wrong!']);
        }
    }

    private function fetchPluginData($cache = true)
    {
        if($cache === true && $data = Cache::get("plugin_store_data")) {
            return $data;
        }

        $result = [];

        try {
            $no_cache = ($cache)?0:1;
            $opts = [
                "http" => [
                    "method" => "GET",
                    "header" => "Access-Token: bVc/ZnpYNSZrMVZYOHE5U2tqcSU=\r\n".
                                "User-Agent: CRUDBooster-Bot-Client\r\n".
                                "No-Cache: ".$no_cache
                ]
            ];

            $context = stream_context_create($opts);
            $data = file_get_contents(base64_decode("aHR0cDovL2NydWRib29zdGVyLmNvbS9hcGkvcGx1Z2luP2FjY2Vzc190b2tlbj1iVmMvWm5wWU5TWnJNVlpZT0hFNVUydHFjU1U9"), false, $context);
            
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
        } catch (\Exception $e) {

        }

        return $result;
    }
}