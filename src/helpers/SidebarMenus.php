<?php

namespace crocodicstudio\crudbooster\helpers;


use crocodicstudio\crudbooster\controllers\CBController;
use crocodicstudio\crudbooster\models\SidebarModel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SidebarMenus
{
    /**
     * @param $menu
     * @return SidebarModel
     */
    private function assignToModel($menu) {
        $model = new SidebarModel();
        $model->setId($menu->id);
        if($menu->type == "url") {
            $model->setUrl($menu->path);
            $model->setIcon($menu->icon);
            $model->setName($menu->name);
            $model->setBasepath(basename($model->getUrl()));
        }elseif ($menu->type == "module") {
            $module = cb()->find("cb_modules", $menu->cb_modules_id);
            $className = '\App\Http\Controllers\\'.$module->controller;
            $controllerClass = new $className();
            /** @var CBController $controllerClass */
            $model->setUrl(cb()->getAdminUrl($controllerClass->getData("permalink")));
            $model->setPermalink($controllerClass->getData("permalink"));
            $model->setIcon($module->icon);
            $model->setName($module->name);
            $model->setBasepath(config('crudbooster.ADMIN_PATH').'/'.basename($model->getUrl()));
        }elseif ($menu->type == "path") {
            $model->setUrl(cb()->getAdminUrl($menu->path));
            $model->setPermalink($menu->path);
            $model->setIcon($menu->icon);
            $model->setName($menu->name);
            $model->setBasepath(config('crudbooster.ADMIN_PATH').'/'.basename($model->getUrl()));
        }

        if(request()->is($model->getBasepath()."*")) {
            $model->setIsActive(true);
        }else{
            $model->setIsActive(false);
        }

        return $model;
    }

    private function loadData($parent_id = null) {
        $menus = DB::table("cb_menus");
        if($parent_id) {
            $menus->where("parent_cb_menus_id",$parent_id);
        }else{
            $menus->whereNull("parent_cb_menus_id");
        }
        return $menus->orderBy("sort_number","asc")->get();
    }

    private function rolePrivilege($cb_roles_id, $cb_menus_id) {
        return cb()->find("cb_role_privileges",['cb_roles_id'=>$cb_roles_id,'cb_menus_id'=>$cb_menus_id]);
    }

    private function checkPrivilege($roles_id,$menu) {
        if($roles_id) {
            $privilege = $this->rolePrivilege($roles_id, $menu->id);
            if($privilege && !$privilege->can_browse) {
                return false;
            }
        }

        return true;
    }

    public function all($withPrivilege = true) {
        $roles_id = ($withPrivilege)?cb()->session()->roleId():null;
        $idHash = "menuUser".$roles_id.auth()->id();
        if($menu = CacheHelper::getItemInGroup($idHash,"sidebar_menu")) return $menu;

        $menus = $this->loadData();
        $result = [];
        foreach($menus as $menu) {

            if($withPrivilege && !$this->checkPrivilege($roles_id, $menu)) continue;

            $sidebarModel = $this->assignToModel($menu);
            if($menus2 = $this->loadData($menu->id)) {
                $sub1 = [];
                foreach ($menus2 as $menu2) {

                    if($withPrivilege && !$this->checkPrivilege($roles_id, $menu2)) continue;

                    $sidebarModel2 = $this->assignToModel($menu2);

                    if($menus3 = $this->loadData($menu2->id)) {
                        $sub2 = [];
                        foreach ($menus3 as $menu3) {

                            if($withPrivilege && !$this->checkPrivilege($roles_id, $menu3)) continue;

                            $sidebarModel3 = $this->assignToModel($menu3);

                            $sub2[] = $sidebarModel3;
                        }
                        $sidebarModel2->setSub($sub2);
                    }
                    $sub1[] = $sidebarModel2;
                }
                $sidebarModel->setSub($sub1);
            }
            $result[] = $sidebarModel;
        }

        CacheHelper::putInGroup($idHash, $result,"sidebar_menu", 3600);
        return $result;
    }

}