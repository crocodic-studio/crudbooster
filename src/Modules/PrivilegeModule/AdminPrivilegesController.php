<?php

namespace crocodicstudio\crudbooster\Modules\PrivilegeModule;

use crocodicstudio\crudbooster\controllers\CBController;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use CRUDBooster;

class AdminPrivilegesController extends CBController
{
    /**
     * AdminPrivilegesController constructor.
     */
    public function __construct()
    {
        $this->table = 'cms_privileges';
        $this->primary_key = 'id';
        $this->title_field = "name";
    }

    public function cbInit()
    {
        $this->setButtons();

        $this->col = [];
        $this->col[] = ["label" => "ID", "name" => "id"];
        $this->col[] = ["label" => "Name", "name" => "name"];
        $this->col[] = [
            "label" => "Superadmin",
            "name" => "is_superadmin",
            "callback" => function ($row) {
                return ($row->is_superadmin) ? "<span class='label label-success'>Superadmin</span>" : "<span class='label label-default'>Standard</span>";
            },
        ];

        $this->form = [];
        $this->form[] = ["label" => "Name", "name" => "name", 'required' => true];
        $this->form[] = ["label" => "Is Superadmin", "name" => "is_superadmin", 'required' => true];
        $this->form[] = ["label" => "Theme Color", "name" => "theme_color", 'required' => true];
    }

    public function getAdd()
    {
        $this->cbLoader();

        $id = 0;
        $data['page_title'] = "Add Data";
        $data['moduls'] = DB::table("cms_moduls")
            ->where('is_protected', 0)
            ->select("cms_moduls.*",
                DB::raw("(select is_visible from cms_privileges_roles where id_cms_moduls = cms_moduls.id and id_cms_privileges = '$id') as is_visible"),
                DB::raw("(select is_create  from cms_privileges_roles where id_cms_moduls = cms_moduls.id and id_cms_privileges = '$id') as is_create"),
                DB::raw("(select is_read    from cms_privileges_roles where id_cms_moduls = cms_moduls.id and id_cms_privileges = '$id') as is_read"),
                DB::raw("(select is_edit    from cms_privileges_roles where id_cms_moduls = cms_moduls.id and id_cms_privileges = '$id') as is_edit"),
                DB::raw("(select is_delete  from cms_privileges_roles where id_cms_moduls  = cms_moduls.id and id_cms_privileges = '$id') as is_delete"))
            ->orderby("name", "asc")
            ->get();

        $data['page_menu'] = Route::getCurrentRoute()->getActionName();

        return view('CbPrivilege::privileges', $data);
    }

    public function postAddSave()
    {
        $this->cbInit();
        $this->validation();
        $this->inputAssignment();

        DB::table($this->table)->insert($this->arr);
        $id = $this->arr[$this->primary_key];

        $this->setTheme();

        foreach (Request::input("privileges", []) as $id_modul => $data) {
            $arrs = array_get_keys($data, ['is_visible', 'is_create', 'is_read', 'is_edit', 'is_delete'], 0);
            $arrs['id_cms_privileges'] = $id;
            $arrs['id_cms_moduls'] = $id_modul;
            DB::table("cms_privileges_roles")->insert($arrs);
            //$module = DB::table('cms_moduls')->where('id', $id_modul)->first();
        }

        $this->refreshSessionRoles();

        CRUDBooster::redirect(CRUDBooster::mainpath(), trans("crudbooster.alert_add_data_success"), 'success');
    }

    public function getEdit($id)
    {
        $this->cbLoader();

        $role = DB::table($this->table)->where("id", $id)->first() ?: new \stdClass();

        $page_title = trans('crudbooster.edit_data_page_title', ['module' => 'Privilege', 'name' => $role->name]);

        $page_menu = Route::getCurrentRoute()->getActionName();

        return view('CbPrivilege::privileges', compact('role', 'page_title', 'page_menu', 'id'));
    }

    public function postEditSave($id)
    {
        $this->cbInit();
        $row = CRUDBooster::first($this->table, $id);

        $this->validation($id);
        $this->inputAssignment($id);

        $this->findRow($id)->update($this->arr);
        foreach (Request::input("privileges", []) as $id_modul => $data) {
            //Check Menu
            //$module = DB::table('cms_moduls')->where('id', $id_modul)->first();
            $arrs = array_get_keys($data, ['is_visible', 'is_create', 'is_read', 'is_edit', 'is_delete',], 0);
            $this->savePermissions($id, $id_modul, $arrs);
        }

        if ($id == CRUDBooster::myPrivilegeId()) {
            $this->refreshSessionRoles();
            $this->setTheme();
        }

        CRUDBooster::redirect(CRUDBooster::mainpath(), trans("crudbooster.alert_update_data_success", [
            'module' => "Privilege",
            'title' => $row->name,
        ]), 'success');
    }

    public function getDelete($id)
    {
        $this->cbLoader();

        $row = $this->findRow($id)->first();

        $this->findRow($id)->delete();
        DB::table("cms_privileges_roles")->where("id_cms_privileges", $row->id)->delete();

        CRUDBooster::redirect(CRUDBooster::mainpath(), trans("crudbooster.alert_delete_data_success"), 'success');
    }

    /**
     */
    private function refreshSessionRoles()
    {
        $roles = DB::table('cms_privileges_roles')->where('id_cms_privileges', CRUDBooster::myPrivilegeId())->join('cms_moduls', 'cms_moduls.id', '=', 'id_cms_moduls')->select('cms_moduls.name', 'cms_moduls.path', 'is_visible', 'is_create', 'is_read', 'is_edit', 'is_delete')->get();
        session()->put('admin_privileges_roles', $roles);
    }

    /**
     * @param $id
     * @param $id_modul
     * @param $arrs
     * @return mixed
     */
    private function savePermissions($id, $id_modul, $arrs)
    {
        $currentPermission = DB::table('cms_privileges_roles')->where('id_cms_moduls', $id_modul)->where('id_cms_privileges', $id)->first();

        if ($currentPermission) {
            return DB::table('cms_privileges_roles')->where('id', $currentPermission->id)->update($arrs);
        }

        $arrs['id'] = DB::table('cms_privileges_roles')->max('id') + 1;
        $arrs['id_cms_privileges'] = $id;
        $arrs['id_cms_moduls'] = $id_modul;
        DB::table("cms_privileges_roles")->insert($arrs);
        return $arrs;
    }

    private function setTheme()
    {
        session()->put('theme_color', $this->arr['theme_color']);
    }

    private function setButtons()
    {
        $this->button_import = false;
        $this->button_export = false;
        $this->button_action_style = 'button_icon';
        $this->button_detail = false;
        $this->button_bulk_action = false;
    }
}
