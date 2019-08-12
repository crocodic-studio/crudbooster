<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 4/25/2019
 * Time: 9:28 PM
 */

namespace crocodicstudio\crudbooster\controllers;


use crocodicstudio\crudbooster\exceptions\CBValidationException;
use Doctrine\DBAL\Query\QueryException;
use Illuminate\Support\Facades\DB;

class DeveloperRolesController extends Controller
{
    private $view = "crudbooster::dev_layouts.modules.roles";

    public function __construct()
    {
        view()->share(['page_title'=>'Role Manager']);
    }

    public function getIndex() {
        $data = [];
        $data['result'] = DB::table("cb_roles")->get();
        return view($this->view.".index",$data);
    }

    public function getAdd() {
        $data = [];
        $data['menus'] = DB::table("cb_menus")->orderBy("name","asc")->get();
        return view($this->view.".add", $data);
    }

    public function postAddSave() {
        DB::beginTransaction();

        try {
            cb()->validation(["name", "menus"]);

            $role = [];
            $role['name'] = request('name');
            $roles_id = DB::table("cb_roles")->insertGetId($role);

            foreach (request("menus") as $menus_id) {
                @$access = request("access")[$menus_id];
                $privilege = [];
                $privilege["cb_roles_id"] = $roles_id;
                $privilege["cb_menus_id"] = $menus_id;
                $privilege["can_browse"]  = @$access['can_browse']?:0;
                $privilege["can_create"] = @$access['can_create']?:0;
                $privilege["can_read"] = @$access['can_read']?:0;
                $privilege["can_update"] = @$access['can_update']?:0;
                $privilege["can_delete"] = @$access['can_delete']?:0;
                DB::table("cb_role_privileges")->insert($privilege);
            }

            DB::commit();

            return cb()->redirect(route("DeveloperRolesControllerGetIndex"),"The role has been saved!","success");

        } catch (CBValidationException $e) {
            return cb()->redirectBack($e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            return cb()->redirectBack($e->getMessage());
        }
    }

    public function getEdit($id) {
        $data = [];
        $data['row'] = cb()->find("cb_roles", $id);

        $menus = DB::table("cb_menus")
        ->leftjoin("cb_role_privileges",function($join) use ($id) {
            $join->on("cb_role_privileges.cb_menus_id","=","cb_menus.id")->where("cb_role_privileges.cb_roles_id", $id);
        })
        ->orderBy("cb_menus.name","asc")
        ->select("cb_menus.*","can_browse","can_create","can_read","can_update","can_delete")
        ->get();
        $data['menus'] = $menus;

        return view($this->view.".edit",$data);
    }

    private function existsPrivilege($cb_roles_id, $cb_menus_id) {
        if($row = cb()->find("cb_role_privileges",['cb_roles_id'=>$cb_roles_id,'cb_menus_id'=>$cb_menus_id])) {
            return $row->id;
        }else{
            return null;
        }
    }

    public function postEditSave($id) {
        try {
            cb()->validation(['name', 'menus']);

            cb()->updateCompact("cb_roles",$id,['name']);

            foreach (request("menus") as $menus_id) {
                @$access = request("access")[$menus_id];
                $privilege = [];
                $privilege["cb_roles_id"] = $id;
                $privilege["cb_menus_id"] = $menus_id;
                $privilege["can_browse"]  = @$access['can_browse']?:0;
                $privilege["can_create"] = @$access['can_create']?:0;
                $privilege["can_read"] = @$access['can_read']?:0;
                $privilege["can_update"] = @$access['can_update']?:0;
                $privilege["can_delete"] = @$access['can_delete']?:0;
                if($privilege_id = $this->existsPrivilege($id, $menus_id)) {
                    DB::table("cb_role_privileges")->where("id", $privilege_id)->update($privilege);
                }else{
                    DB::table("cb_role_privileges")->insert($privilege);
                }
            }

            return cb()->redirect(route("DeveloperRolesControllerGetIndex"),"The role has been saved!","success");

        } catch (CBValidationException $e) {

            return cb()->redirectBack($e->getMessage());
        }
    }

    public function getDelete($id) {
        DB::table("cb_roles")->where("id", $id)->delete();
        DB::table("cb_role_privileges")->where("cb_roles_id",$id)->delete();

        return cb()->redirect(route("DeveloperRolesControllerGetIndex"), "The role has been deleted!","success");
    }

}