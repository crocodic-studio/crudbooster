<?php

namespace crocodicstudio\crudbooster\Modules\ModuleGenerator;

use crocodicstudio\crudbooster\controllers\CBController;
use crocodicstudio\crudbooster\controllers\FormValidator;
use crocodicstudio\crudbooster\helpers\CRUDBooster;
use crocodicstudio\crudbooster\Modules\PrivilegeModule\PrivilegeRepo;
use Illuminate\Support\Facades\DB;

class AdminModulesController extends CBController
{
    public function cbInit()
    {
        $this->table = 'cms_modules';
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
    // 	$data['result'] = DB::table('cms_modules')->where('is_protected',0)->orderby('name','asc')->get();
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
        $lastId = DB::table('cms_modules')->max('id') + 1;

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
        if ($this->arr['controller'] == '') {
            $this->arr['controller'] = ControllerGenerator::generateController(request('table_name'), basename(request('path')));
        }

        $this->arr['created_at'] = YmdHis();
        $this->table()->insert($this->arr);

        //Insert Menu
        if ($this->arr['controller']) {
            (new CreateMenuForNewModule())->execute($this->arr['controller'], $this->arr['name'], $this->arr['icon']);
        }

        $moduleId = $this->arr['id'];

        (new PrivilegeRepo())->grantAllPermissions($moduleId);

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
