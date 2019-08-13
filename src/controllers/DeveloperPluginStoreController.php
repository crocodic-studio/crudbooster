<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 4/25/2019
 * Time: 9:28 PM
 */

namespace crocodicstudio\crudbooster\controllers;

use crocodicstudio\crudbooster\exceptions\CBValidationException;
use crocodicstudio\crudbooster\helpers\CurlHelper;
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

        if(request("refresh")) {
            $this->fetchPluginData(false );
            return cb()->redirectBack("Plugin list has been refreshed!","success");
        }

        $data = [];
        $data['result'] = $this->fetchPluginData();
        return view($this->view.'.index',$data);
    }

    public function postLoginAccount() {
        $curl = new CurlHelper("http://crudbooster.com/api/login_member");
        $curl->headers([
            "Access-Token"=>"bVc/ZnpYNSZrMVZYOHE5U2tqcSU=",
            "User-Agent"=>"CRUDBooster-Bot-Client"
        ]);
        $curl->data([
            "email"=>request("email"),
            "password"=>request("password")
        ]);
        $response = $curl->send();

        if($respArray = json_decode($response, true)) {
            if($respArray['status'] && isset($respArray['token'])) {
                session(['account_token'=>$respArray['token']]);
            }
            return response()->make($response,200,["Content-Type"=>"application/json"]);
        } else {
            return response()->json(['status'=>false,'message'=>'failed']);
        }
    }

    public function postRequestBuyPlugin() {
        $curl = new CurlHelper("http://crudbooster.com/api/request_buy_plugin");
        $curl->headers([
            "Access-Token"=>"bVc/ZnpYNSZrMVZYOHE5U2tqcSU=",
            "User-Agent"=>"CRUDBooster-Bot-Client"
        ]);
        $curl->data([
            "key"=>base64_encode(request("key")),
            "token"=>base64_encode(request("token")),
            "ref"=>base64_encode(cb()->getDeveloperUrl("plugins"))
        ]);
        $response = $curl->send();

        if($respArray = json_decode($response, true)) {
            if($respArray['status']) {
                $form = base64_decode($respArray['payment']);
                return response()->json(['status'=>true,'form'=>$form]);
            } else {
                return response()->json($respArray);
            }
        } else {
            return response()->json(['status'=>false,'message'=>'failed','raw'=>$response]);
        }
    }

    public function getUninstall($key)
    {
        $pluginData = $this->fetchPluginData();

        if(isset($pluginData[$key])) {
            if(file_exists(app_path("CBPlugins/".$key))) {
                rrmdir(app_path("CBPlugins/".$key));
                return response()->json(['status'=>true, 'message'=>'Plugin has been uninstalled!']);
            }else{
                return response()->json(['status'=>false,'message'=>'Failed to uninstall, plugin is not found']);
            }
        }else {
            return response()->json(['status'=>false,'message'=>'Failed to uninstall, plugin key is not found']);
        }
    }

    public function getInstall($key)
    {
        $pluginData = $this->fetchPluginData();

        if(isset($pluginData[$key])) {
            $plugin = $pluginData[$key];

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

                return response()->json(['status'=>true,'message'=>'Install / update plugin has been succesfull!']);

            } else {
                return response()->json(['status'=>false,'message'=>"Failed to install/update, can't open the plugin archive"]);
            }
        }else{
            return response()->json(['status'=>false,'message'=>'Failed to install/update, plugin key is not found']);
        }
    }

    private function fetchPluginData($cache = true)
    {
        if($cache === true && $data = Cache::get("plugin_store_data")) {
            return $data;
        }

        $result = [];

        try {
            $opts = [
                "http" => [
                    "method" => "GET",
                    "header" => "Access-Token: bVc/ZnpYNSZrMVZYOHE5U2tqcSU=\r\n".
                                "User-Agent: CRUDBooster-Bot-Client"
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