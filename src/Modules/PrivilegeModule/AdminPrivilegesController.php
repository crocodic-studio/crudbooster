<?php

namespace crocodicstudio\crudbooster\Modules\PrivilegeModule;

use crocodicstudio\crudbooster\controllers\CBController;
use crocodicstudio\crudbooster\controllers\FormValidator;
use crocodicstudio\crudbooster\helpers\CRUDBooster;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;

class AdminPrivilegesController extends CBController
{
    /**
     * AdminPrivilegesController constructor.
     */
    public function __construct()
    {
        $this->table = 'cms_privileges';
        $this->primaryKey = 'id';
        $this->titleField = "name";
    }

    public function cbInit()
    {
        $this->setButtons();
        $this->makeCols();
        $this->makeForm();
    }

    public function getAdd()
    {
        $this->cbLoader();

        $id = 0;
        $data = [
            'page_title' => 'Add Data',
            'page_menu' => Route::getCurrentRoute()->getActionName(),
        ];
        $data['moduls'] = $this->table('cms_modules')
            ->where('is_protected', 0)
            ->select('cms_modules.*',
                DB::raw("(select can_see_module from cms_privileges_roles where id_cms_modules = cms_modules.id and id_cms_privileges = '$id') as can_see_module"),
                DB::raw("(select can_create  from cms_privileges_roles where id_cms_modules = cms_modules.id and id_cms_privileges = '$id') as can_create"),
                DB::raw("(select can_read    from cms_privileges_roles where id_cms_modules = cms_modules.id and id_cms_privileges = '$id') as can_read"),
                DB::raw("(select can_edit    from cms_privileges_roles where id_cms_modules = cms_modules.id and id_cms_privileges = '$id') as can_edit"),
                DB::raw("(select can_delete  from cms_privileges_roles where id_cms_modules  = cms_modules.id and id_cms_privileges = '$id') as can_delete"))
            ->orderby('name', 'asc')
            ->get();


        return view('CbPrivilege::privileges', $data);
    }

    public function postAddSave()
    {
        $this->cbInit();
        app(FormValidator::class)->validate(null, $this->form, $this);
        $this->inputAssignment();

        $this->table()->insert($this->arr);
        $id = $this->arr[$this->primaryKey];

        $this->setTheme();

        foreach (Request::input('privileges', []) as $moduleId => $data) {
            $arrs = array_get_keys($data, ['can_see_module', 'can_create', 'can_read', 'can_edit', 'can_delete'], 0);
            $arrs['id_cms_privileges'] = $id;
            $arrs['id_cms_modules'] = $moduleId;
            $this->table('cms_privileges_roles')->insert($arrs);
            //$module = DB::table('cms_modules')->where('id', $moduleId)->first();
        }

        CRUDBooster::refreshSessionRoles();

        CRUDBooster::redirect(CRUDBooster::mainpath(), cbTrans('alert_add_data_success'), 'success');
    }

    public function getEdit($id)
    {
        $this->cbLoader();

        $role = $this->findRow($id)->first() ?: new \stdClass();

        $page_title = cbTrans('edit_data_page_title', ['module' => 'Privilege', 'name' => $role->name]);

        return view('CbPrivilege::privileges', compact('role', 'page_title', 'id'));
    }

    public function postEditSave($id)
    {
        $this->cbInit();
        $row = CRUDBooster::first($this->table, $id);

        app(FormValidator::class)->validate($id, $this->form, $this->table);
        $this->inputAssignment($id);

        $this->findRow($id)->update($this->arr);
        foreach (Request::input('privileges', []) as $moduleId => $data) {
            //Check Menu

            $arrs = array_get_keys($data, ['can_see_module', 'can_create', 'can_read', 'can_edit', 'can_delete',], 0);
            $this->savePermissions($id, $moduleId, $arrs);
        }

        if ($id == auth('cbAdmin')->user()->id_cms_privileges) {
            CRUDBooster::refreshSessionRoles();
            $this->setTheme();
        }

        CRUDBooster::redirect(CRUDBooster::mainpath(), cbTrans('alert_update_data_success', [
            'module' => 'Privilege',
            'title' => $row->name,
        ]), 'success');
    }

    public function getDelete($id)
    {
        $this->cbLoader();

        $row = $this->findRow($id)->first();

        $this->findRow($id)->delete();
        $this->table('cms_privileges_roles')->where('id_cms_privileges', $row->id)->delete();

        CRUDBooster::redirect(CRUDBooster::mainpath(), cbTrans('alert_delete_data_success'), 'success');
    }

    /**
     * @param $id
     * @param $moduleId
     * @param $arrs
     * @return mixed
     */
    private function savePermissions($id, $moduleId, $arrs)
    {
        $conditions = [
            'id_cms_modules' => $moduleId,
            'id_cms_privileges' => $id,
        ];
        $permissionID = $this->table('cms_privileges_roles')->where($conditions)->value('id');

        if ($permissionID) {
            return $this->table('cms_privileges_roles')->where('id', $permissionID)->update($arrs);
        }

        $arrs['id_cms_modules'] = $moduleId;
        $arrs['id_cms_privileges'] = $id;
        $this->table('cms_privileges_roles')->insert($arrs);
        return $arrs;
    }

    private function setTheme()
    {
        session()->put('theme_color', $this->arr['theme_color']);
    }

    private function setButtons()
    {
        $this->buttonImport = false;
        $this->buttonExport = false;
        $this->buttonActionStyle = 'button_icon';
        $this->buttonDetail = false;
        $this->buttonBulkAction = false;
    }

    private function makeForm()
    {
        $this->form = [];
        $this->form[] = ['label' => 'Name', 'name' => 'name', 'required' => true];
        $this->form[] = ['label' => 'Is Superadmin', 'name' => 'is_superadmin', 'required' => true];
        $this->form[] = ['label' => 'Theme Color', 'name' => 'theme_color', 'required' => true];
    }

    private function makeCols()
    {
        $this->col = [];
        $this->col[] = ['label' => 'ID', 'name' => 'id'];
        $this->col[] = ['label' => 'Name', 'name' => 'name'];
        $this->col[] = [
            'label' => 'Superadmin',
            'name' => 'is_superadmin',
            'callback' => function ($row) {
                return ($row->is_superadmin) ? "<span class='label label-success'>Superadmin</span>" : "<span class='label label-default'>Standard</span>";
            },
        ];
    }
}
