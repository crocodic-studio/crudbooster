<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 4/25/2019
 * Time: 9:28 PM
 */

namespace crocodicstudio\crudbooster\controllers;


use crocodicstudio\crudbooster\exceptions\CBValidationException;
use crocodicstudio\crudbooster\helpers\CacheHelper;
use Illuminate\Support\Facades\DB;

class DeveloperMenusController extends Controller
{

    private $view = "crudbooster::dev_layouts.modules.menus";

    public function __construct()
    {
        view()->share(['page_title'=>'Menu Manager']);
    }

    public function getIndex() {
        $data = [];
        return view($this->view.".index",$data);
    }

    public function getAdd() {
        $data = [];
        $data['form_title'] = "Add Menu";
        $data['form_url'] = route('DeveloperMenusControllerPostAddSave');
        $data['modules'] = DB::table("cb_modules")->orderBy("name","asc")->get();
        return view($this->view.".form", $data);
    }

    public function getEdit($id) {
        $data = [];
        $data['form_title'] = "Edit Menu";
        $data['form_url'] = cb()->getDeveloperUrl("menus/edit-save/".$id);
        $data['modules'] = DB::table("cb_modules")->orderBy("name","asc")->get();
        $data['row'] = cb()->find("cb_menus", $id);
        return view($this->view.".form", $data);
    }

    public function postAddSave() {
        try {
            cb()->validation(["name", "icon", "type"]);

            $menu = [];
            $menu['name'] = request('name');
            $menu['icon'] = request('icon');
            $menu['type'] = request('type');
            if(request('type') == 'module') {
                $menu['cb_modules_id'] = request('cb_modules_id');
            }elseif (request('type') == 'url') {
                $menu['path'] = request('url_value');
            }elseif (request('type') == 'path') {
                $menu['path'] = request('path_value');
            } elseif (request("type") == "empty") {
                $menu['path'] = "javascript:void(0);";
            }

            DB::table("cb_menus")->insert($menu);

            CacheHelper::forgetGroup("sidebar_menu");

            return cb()->redirect(route("DeveloperMenusControllerGetIndex"),"The menu has been added!","success");

        } catch (CBValidationException $e) {
            return cb()->redirectBack($e->getMessage());
        }
    }

    public function postEditSave($id) {
        try {
            cb()->validation(["name", "icon", "type"]);

            $menu = [];
            $menu['name'] = request('name');
            $menu['icon'] = request('icon');
            $menu['type'] = request('type');
            if(request('type') == 'module') {
                $menu['cb_modules_id'] = request('cb_modules_id');
            }elseif (request('type') == 'url') {
                $menu['path'] = request('url_value');
            }elseif (request('type') == 'path') {
                $menu['path'] = request('path_value');
            } elseif (request("type") == "empty") {
                $menu['path'] = "javascript:void(0);";
            }
            DB::table("cb_menus")->where("id",$id)->update($menu);

            CacheHelper::forgetGroup("sidebar_menu");

            return cb()->redirect(route("DeveloperMenusControllerGetIndex"),"The menu has been saved!","success");

        } catch (CBValidationException $e) {
            return cb()->redirectBack($e->getMessage());
        }
    }

    public function postSaveSorting() {
        try {
            cb()->validation(['menus']);

            $menus = request('menus');
            $menus = json_decode($menus, true);
            if($menus) {
                $menus = $menus[0];
                if($menus) {
                    foreach($menus as $i=>$menu) {
                        $id = $menu['id'];
                        cb()->update("cb_menus",$id,["sort_number"=>$i,'parent_cb_menus_id'=>null]);
                        if($menu['children'][0]) {
                            foreach($menu['children'][0] as $ii=>$sub) {
                                $id = $sub['id'];
                                cb()->update("cb_menus",$id,["sort_number"=>$ii,"parent_cb_menus_id"=>$menu['id']]);
                                if($sub['children'][0]) {
                                    foreach($sub['children'][0] as $iii=>$subsub) {
                                        $id = $subsub['id'];
                                        cb()->update("cb_menus",$id,["sort_number"=>$iii,"parent_cb_menus_id"=>$sub['id']]);
                                    }
                                }
                            }
                        }
                    }
                }
            }

            CacheHelper::forgetGroup("sidebar_menu");

            return response()->json(['api_status'=>1,'api_message'=>'success']);

        } catch (CBValidationException $e) {
            return response()->json(['api_status'=>0,'api_message'=>$e->getMessage()]);
        }
    }

    public function getDelete($id) {
        DB::table("cb_menus")->where("id",$id)->delete();
        DB::table("cb_role_privileges")->where("cb_menus_id", $id)->delete();

        CacheHelper::forgetGroup("sidebar_menu");

        return cb()->redirectBack("The menu has been deleted!","success");
    }

}