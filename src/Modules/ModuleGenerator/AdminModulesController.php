<?php

namespace crocodicstudio\crudbooster\Modules\ModuleGenerator;

use crocodicstudio\crudbooster\controllers\CBController;
use crocodicstudio\crudbooster\controllers\FormValidator;
use crocodicstudio\crudbooster\helpers\CRUDBooster;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AdminModulesController extends CBController
{
    public function cbInit()
    {
        $this->table = 'cms_moduls';
        $this->primaryKey = 'id';
        $this->titleField = 'name';
        $this->limit = 100;
        $this->buttonAdd = false;
        $this->buttonExport = false;
        $this->buttonImport = false;
        $this->buttonFilter = false;
        $this->buttonDetail = false;
        $this->buttonBulkAction = false;
        $this->buttonActionStyle = 'button_icon';
        $this->orderby = ['is_protected' => 'asc', 'name' => 'asc'];

        $this->makeColumns();

        $this->form = Form::makeForm($this->table);

        $this->scriptJs = "
 			$(function() {
 				$('#table_name').change(function() {
					var v = $(this).val();
					$('#path').val(v);
				})	
 			}) ";

        $this->addAction[] = [
            'label' => 'Module Wizard',
            'icon' => 'fa fa-wrench',
            'url' => CRUDBooster::mainpath('step1').'/[id]',
            "showIf" => "[is_protected] == 0",
        ];

        $this->indexButton[] = ['label' => 'Generate New Module', 'icon' => 'fa fa-plus', 'url' => CRUDBooster::mainpath('step1'), 'color' => 'success'];
    }
    // public function getIndex() {
    // 	$data['page_title'] = 'Module Generator';
    // 	$data['result'] = DB::table('cms_moduls')->where('is_protected',0)->orderby('name','asc')->get();
    // 	$this->cbView('CbModulesGen::index',$data);
    // }	

    private function makeColumns()
    {
        $this->col = [
            ['label' => 'name', 'name' => 'name'],
            ['label' => "Table", 'name' => "table_name"],
            ['label' => "Path", 'name' => "path"],
            ['label' => "Controller", 'name' => "controller"],
            ['label' => "Protected", 'name' => "is_protected", "visible" => false],
        ];
    }

    function hookBeforeDelete($ids)
    {
        foreach ($ids as $id) {
            $controller = ModulesRepo::getControllerName($id);
            DB::table('cms_menus')->where('path', 'like', '%'.$controller.'%')->delete();
            @unlink(controller_path($controller));
        }

        return $ids;
    }

    public function getTableColumns($table)
    {
        $columns = \Schema::getColumnListing($table);

        return response()->json($columns);
    }

    public function getCheckSlug($slug)
    {
        $check = ModulesRepo::countByPath($slug);
        $lastId = DB::table('cms_moduls')->max('id') + 1;

        return response()->json(['total' => $check, 'lastid' => $lastId]);
    }

    public function getAdd()
    {
        $this->cbLoader();

        return redirect()->route("ModulesControllerGetStep1");
    }

    public function getStep1($id = 0, Step1Handler $handler)
    {
        $this->cbLoader();

        return $handler->showForm($id);
    }

    public function getStep2($id, Step2Handler $handler)
    {
        $this->cbLoader();

        return $handler->showForm($id);
    }

    public function postStep2(Step1Handler $handler)
    {
        $this->cbLoader();

        return $handler->handleFormSubmit();
    }

    public function postStep3(Step2Handler $handler)
    {
        $this->cbLoader();

        return $handler->handleFormSubmit();
    }

    public function getStep3($id, Step3Handler $step3)
    {
        $this->cbLoader();

        return $step3->showForm($id);
    }

    public function getTypeInfo($type = 'text')
    {
        header("Content-Type: application/json");
        echo file_get_contents(CbComponentsPath($type).'/info.json');
    }

    public function postStep4(Step3Handler $handler)
    {
        $this->cbLoader();

        return $handler->handleFormSubmit();
    }

    public function getStep4($id, Step4Handler $handler)
    {
        $this->cbLoader();

        return $handler->showForm($id);
    }

    public function postStepFinish(Step4Handler $handler)
    {
        $this->cbLoader();

        return $handler->handleFormSubmit();
    }

    public function postAddSave()
    {
        $this->cbLoader();
        app(FormValidator::class)->validate(null, $this->form, $this);
        $this->inputAssignment();

        //Generate Controller
        $route_basename = basename(request('path'));
        if ($this->arr['controller'] == '') {
            $this->arr['controller'] = ControllerGenerator::generateController(request('table_name'), $route_basename);
        }

        $this->arr['created_at'] = YmdHis();
        $this->arr['id'] = $this->table()->max('id') + 1;
        $this->table()->insert($this->arr);

        //Insert Menu
        if ($this->arr['controller']) {
            $this->createMenuForModule();
        }

        $id_modul = $this->arr['id'];

        $user_id_privileges = auth('cbAdmin')->user()->id_cms_privileges;
        DB::table('cms_privileges_roles')->insert([
            'id_cms_moduls' => $id_modul,
            'id_cms_privileges' => $user_id_privileges,
            'can_see_module' => 1,
            'can_create' => 1,
            'can_read' => 1,
            'can_edit' => 1,
            'can_delete' => 1,
        ]);

        //Refresh Session Roles
        CRUDBooster::refreshSessionRoles();

        //$ref_parameter = Request::input('ref_parameter');
        if (request('return_url')) {
            CRUDBooster::redirect(request('return_url'), cbTrans("alert_add_data_success"), 'success');
        }
        if (request('submit') == cbTrans('button_save_more')) {
            CRUDBooster::redirect(CRUDBooster::mainpath('add'), cbTrans('alert_add_data_success'), 'success');
        }
        CRUDBooster::redirect(CRUDBooster::mainpath(), cbTrans('alert_add_data_success'), 'success');
    }

    private function createMenuForModule()
    {
        $parent_menu_sort = DB::table('cms_menus')->where('parent_id', 0)->max('sorting') + 1;
        $parent_menu_id = DB::table('cms_menus')->insertGetId([
            'created_at' => YmdHis(),
            'name' => $this->arr['name'],
            'icon' => $this->arr['icon'],
            'path' => '#',
            'type' => 'URL External',
            'is_active' => 1,
            'cms_privileges' => auth('cbAdmin')->user()->id_cms_privileges,
            'sorting' => $parent_menu_sort,
            'parent_id' => 0,
        ]);

        $arr = [
            'created_at' => YmdHis(),
            'type' => 'Route',
            'is_active' => 1,
            'cms_privileges' => auth('cbAdmin')->user()->id_cms_privileges,
            'parent_id' => $parent_menu_id,
        ];

        DB::table('cms_menus')->insert([
                'name' => cbTrans('text_default_add_new_module', ['module' => $this->arr['name']]),
                'icon' => 'fa fa-plus',
                'path' => $this->arr['controller'].'GetAdd',
                'sorting' => 1,
            ] + $arr);

        DB::table('cms_menus')->insert([
                'name' => cbTrans('text_default_list_module', ['module' => $this->arr['name']]),
                'icon' => 'fa fa-bars',
                'path' => $this->arr['controller'].'GetIndex',
                'cms_privileges' => auth('cbAdmin')->user()->id_cms_privileges,
                'sorting' => 2,
            ] + $arr);
    }

    public function postEditSave($id)
    {
        $this->cbLoader();

        //$row = $this->table()->where($this->primaryKey, $id)->first();

        app(FormValidator::class)->validate($id, $this->form, $this);

        $this->inputAssignment();

        //Generate Controller
        $route_basename = basename(request('path'));
        if ($this->arr['controller'] == '') {
            $this->arr['controller'] = ControllerGenerator::generateController(request('table_name'), $route_basename);
        }

        $this->findRow($id)->update($this->arr);

        //Refresh Session Roles
        CRUDBooster::refreshSessionRoles();

        backWithMsg(cbTrans('alert_update_data_success'));
    }
}
