<?php

namespace crocodicstudio\crudbooster\Modules\ModuleGenerator;

use CRUDBooster;
use Illuminate\Support\Facades\DB;

class Step1Handler
{
    public function showForm($id)
    {
        $tables_list = getTablesList();
        $row = CRUDBooster::first('cms_moduls', ['id' => $id]);

        return view("CbModulesGen::step1", compact("tables_list", "fontawesome", "row", "id"));
    }

    public function handleFormSubmit()
    {
        $name = request('name');
        $table_name = request('table');
        $icon = request('icon');
        $path = request('path');

        if (! request('id')) {

            if (DB::table('cms_moduls')->where('path', $path)->where('deleted_at', null)->count()) {
                return CRUDBooster::backWithMsg('Sorry the slug has already exists, please choose another !', 'warning');
            }

            $created_at = now();

            $controller = CRUDBooster::generateController($table_name, $path);
            $id = \DB::table('cms_moduls')->insertGetId(compact("controller", "name", "table_name", "icon", "path", "created_at"));

            //Insert Menu
            if ($controller && request('create_menu')) {
                $parent_menu_sort = DB::table('cms_menus')->where('parent_id', 0)->max('sorting') + 1;

                DB::table('cms_menus')->insert([
                    'created_at' => date('Y-m-d H:i:s'),
                    'name' => $name,
                    'icon' => $icon,
                    'path' => $controller.'GetIndex',
                    'type' => 'Route',
                    'is_active' => 1,
                    'cms_privileges' => CRUDBooster::myPrivilegeId(),
                    'sorting' => $parent_menu_sort,
                    'parent_id' => 0,
                ]);
            }

            DB::table('cms_privileges_roles')->insert([
                'id' => DB::table('cms_privileges_roles')->max('id') + 1,
                'id_cms_moduls' => $id,
                'id_cms_privileges' => CRUDBooster::myPrivilegeId(),
                'is_visible' => 1,
                'is_create' => 1,
                'is_read' => 1,
                'is_edit' => 1,
                'is_delete' => 1,
            ]);

            //Refresh Session Roles
            $roles = DB::table('cms_privileges_roles')
                ->where('id_cms_privileges', CRUDBooster::myPrivilegeId())
                ->join('cms_moduls', 'cms_moduls.id', '=', 'id_cms_moduls')
                ->select('cms_moduls.name', 'cms_moduls.path', 'is_visible', 'is_create', 'is_read', 'is_edit', 'is_delete')
                ->get();

            session()->put('admin_privileges_roles', $roles);

            return redirect()->route("AdminModulesControllerGetStep2", ["id" => $id]);
        }

        $id = request('id');
        \DB::table('cms_moduls')->where('id', $id)->update(compact("name", "table_name", "icon", "path"));

        $row = DB::table('cms_moduls')->where('id', $id)->first();


        if (file_exists(controller_path($row->controller))) {
            $response = file_get_contents(controller_path(str_replace('.', '', $row->controller)));
        }else{
            $response = file_get_contents(__DIR__.'Step1Handler.php/'.str_replace('.', '', $row->controller).'.php');
        }

        if (strpos($response, "# START COLUMNS") !== true) {
            // return redirect()->back()->with(['message'=>'Sorry, is not possible to edit the module with Module Generator Tool. Prefix and or Suffix tag is missing !','message_type'=>'warning']);
        }

        return redirect()->route("AdminModulesControllerGetStep2", ["id" => $id]);
    }
}