<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 2/4/2019
 * Time: 3:44 PM
 */

namespace crocodicstudio\crudbooster\helpers;


use crocodicstudio\crudbooster\controllers\scaffolding\ColumnSingleton;

class Module
{
    private $controller;
    private $controller_class;
    private $privilege;
    private $module;
    private $menu;

    public function __construct()
    {
        $routeArray = request()->route()->getAction();
        $this->controller = class_basename($routeArray['controller']);
        $this->controller_class = new ("\App\Http\Controllers\\".$this->controller)();

        $this->module = cb()->find("cb_modules",["controller"=>$this->controller]);
        $this->menu = cb()->find("cb_menus",["cb_modules_id"=>$this->module->id]);
        $this->menu = (!$this->menu)?cb()->find("cb_menus",["type"=>"path","path"=>request()->segment(2)]):$this->menu;
        $this->privilege = DB::table("cb_role_privileges")
            ->where("cb_menus_id", $this->menu->id)
            ->where("cb_roles_id", cb()->session()->roleId())
            ->first();
    }

    /**
     * @return \crocodicstudio\crudbooster\controllers\scaffolding\singletons\ColumnSingleton
     */
    public function getColumnSingleton()
    {
        return app('ColumnSingleton');
    }

    public function getPageTitle()
    {
        return $this->controller_class->getData("page_title")?:env('APP_NAME');
    }

    public function getTable()
    {
        return $this->controller_class->getData("table");
    }

    public function getIcon()
    {
        return $this->controller_class->getData('icon')?:"fa fa-bars";
    }

    public function getData($key) {
        return $this->controller_class->getData($key);
    }

    public function canBrowse() {
        if($this->privilege) {
            if($this->privilege->can_browse) return true;
            else return false;
        }else{
            return true;
        }
    }

    public function canCreate() {
        if($this->privilege) {
            if($this->privilege->can_create) return true;
            else return false;
        }else{
            return true;
        }
    }

    public function canRead() {
        if($this->privilege) {
            if($this->privilege->can_read) return true;
            else return false;
        }else{
            return true;
        }
    }

    public function canUpdate() {
        if($this->privilege) {
            if($this->privilege->can_update) return true;
            else return false;
        }else{
            return true;
        }
    }

    public function canDelete() {
        if($this->privilege) {
            if($this->privilege->can_delete) return true;
            else return false;
        }else{
            return true;
        }
    }

    public function addURL()
    {
        if(method_exists($this->controller_class, 'getAdd')) {
            return action($this->controller.'@getAdd');
        }else{
            return null;
        }
    }

    public function addSaveURL()
    {
        if(method_exists($this->controller_class, 'postAddSave')) {
            return action($this->controller.'@postAddSave');
        }else{
            return null;
        }
    }

    public function editURL($id = null)
    {
        if(method_exists($this->controller_class, 'getEdit')) {
            return action($this->controller.'@getEdit',['id'=>$id]);
        }else{
            return null;
        }
    }

    public function editSaveURL($id = null)
    {
        if(method_exists($this->controller_class, 'postEditSave')) {
            return action($this->controller.'@postEditSave',['id'=>$id]);
        }else{
            return null;
        }
    }

    public function detailURL($id=null)
    {
        if(method_exists($this->controller_class, 'getDetail')) {
            return action($this->controller.'@getDetail',['id'=>$id]);
        }else{
            return null;
        }
    }

    public function deleteURL($id=null)
    {
        if(method_exists($this->controller_class, 'getDelete')) {
            return action($this->controller.'@getDelete',['id'=>$id]);
        }else{
            return null;
        }
    }

    public function url($path = null)
    {
        if(method_exists($this->controller_class, 'getIndex')) {
            return trim(action($this->controller.'@getIndex').'/'.$path,'/');
        }else{
            return null;
        }
    }
}