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
use Illuminate\Support\Facades\DB;

class DeveloperModulesController extends Controller
{

    private $view = "crudbooster::dev_layouts.modules.modules";

    public function __construct()
    {
        view()->share(['pageTitle'=>'Modules']);
    }


    public function getIndex() {
        $data = [];
        $data['result'] = DB::table("cb_modules")->get();
        return view($this->view.'.index',$data);
    }

    public function getAdd() {
        $data = [];
        $data['tables'] = DB::connection()->getDoctrineSchemaManager()->listTableNames();
        return view($this->view.'.add', $data);
    }

    public function postAddSave() {
        try {
            cb()->validation(['name', 'table','icon']);

            (new ModuleGenerator(request('table'), request('name'), request('icon')))->make();

            return cb()->redirect(action("DeveloperModulesController@getIndex"),"New module has been created!","success");

        } catch (CBValidationException $e) {
            return cb()->redirectBack($e->getMessage());
        }
    }

    public function getEdit($id) {
        $data = [];
        $data['row'] = cb()->find("cb_modules", $id);
        return view($this->view.".edit", $data);
    }

    public function postEditSave($id) {
        try {
            cb()->validation(['name', 'icon']);

            cb()->updateCompact("cb_modules", $id, ['name','icon']);

            return cb()->redirect(action("DeveloperModulesController@getIndex"),"Module has been updated!","success");

        } catch (CBValidationException $e) {
            return cb()->redirectBack($e->getMessage());
        }
    }

    public function getDelete($id) {
        $module = cb()->find("cb_modules",$id);
        @unlink(app_path("Http/Controllers/".$module->controller));
        DB::table("cb_modules")->where("id", $id)->delete();
        DB::table("cb_menus")->where("cb_modules_id", $id)->delete();

        return cb()->redirectBack("The module has been deleted!","success");
    }

}