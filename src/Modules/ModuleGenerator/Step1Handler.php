<?php

namespace Crocodicstudio\Crudbooster\Modules\ModuleGenerator;

use Crocodicstudio\Crudbooster\helpers\CRUDBooster;
use Crocodicstudio\Crudbooster\Modules\PrivilegeModule\PrivilegeRepo;
use Illuminate\Support\Facades\DB;

class Step1Handler
{
    public function showForm()
    {
        return view('CbModulesGen::step1');
    }

    public function handleFormSubmit()
    {
        $name = request('name');
        $table_name = request('table');
        $icon = request('icon');
        $path = request('path');

        if (! request('id')) {
            if (ModulesRepo::modulePathExists($path)) {
                //todo: should be translated
                backWithMsg('Sorry the slug has already exists, please choose another !', 'warning');
            }
            $id = $this->registerNewModule($table_name, $path, $name, $icon);
            return redirect()->route("AdminModulesControllerGetStep2", ["id" => $id]);
        }

        $id = request('id');
        ModulesRepo::updateById($id, compact("name", "table_name", "icon", "path"));

        $row = ModulesRepo::find($id);


        if (file_exists(controller_path($row->controller))) {
            $response = FileManipulator::readCtrlContent(str_replace('.', '', $row->controller));
        }else{
            $response = file_get_contents(__DIR__.'Step1Handler.php/'.str_replace('.', '', $row->controller).'.php');
        }

        if (strpos($response, "# START COLUMNS") !== true) {
            // return redirect()->back()->with(['message'=>'Sorry, is not possible to edit the module with Module Generator Tool. Prefix and or Suffix tag is missing !','message_type'=>'warning']);
        }

        return redirect()->route("AdminModulesControllerGetStep2", ["id" => $id]);
    }

    /**
     * @param $table_name
     * @param $path
     * @param $name
     * @param $icon
     * @return mixed
     */
    private function registerNewModule($table_name, $path, $name, $icon)
    {
        list($controller, $moduleId) = $this->insertModule($table_name, $path, $name, $icon);

        //Insert Menu
        if ($controller && request('create_menu')) {
            $this->insertMenu($name, $icon, $controller);
        }

        (new PrivilegeRepo())->grantAllPermissions($moduleId);

        //Refresh Session Roles
        CRUDBooster::refreshSessionRoles();

        return $moduleId;
    }

    /**
     * @param $name
     * @param $icon
     * @param $controller
     */
    private function insertMenu($name, $icon, $controller)
    {
        DB::table('cms_menus')->insert([
            'created_at' => YmdHis(),
            'name' => $name,
            'icon' => $icon,
            'path' => $controller.'GetIndex',
            'type' => 'Route',
            'is_active' => 1,
            'cms_roles' => cbUser()->cms_roles_id,
            'sorting' => DB::table('cms_menus')->where('parent_id', 0)->max('sorting') + 1,
            'parent_id' => 0,
        ]);
    }

    /**
     * @param $table_name
     * @param $path
     * @param $name
     * @param $icon
     * @return array
     */
    private function insertModule($table_name, $path, $name, $icon)
    {
        $created_at = YmdHis();

        $controller = ControllerGenerator::generateController($table_name, $path);
        $id = \DB::table('cms_modules')->insertGetId(compact("controller", "name", "table_name", "icon", "path", "created_at"));

        return [$controller, $id];
    }
}