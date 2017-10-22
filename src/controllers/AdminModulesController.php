<?php namespace crocodicstudio\crudbooster\controllers;

use crocodicstudio\crudbooster\controllers\Controller;
use crocodicstudio\crudbooster\controllers\Helpers\FontAwesome;
use crocodicstudio\crudbooster\ModuleGenerator\Step1Handler;
use crocodicstudio\crudbooster\ModuleGenerator\Step3Handler;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\PDF;
use Illuminate\Support\Facades\Excel;
use CRUDBooster;
use PhpParser\Error;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter;

class AdminModulesController extends CBController
{
    public function cbInit()
    {
        $this->table = 'cms_moduls';
        $this->primary_key = 'id';
        $this->title_field = "name";
        $this->limit = 100;
        $this->button_add = false;
        $this->button_export = false;
        $this->button_import = false;
        $this->button_filter = false;
        $this->button_detail = false;
        $this->button_bulk_action = false;
        $this->button_action_style = 'button_icon';
        $this->orderby = ['is_protected' => 'asc', 'name' => 'asc'];

        $this->col = [];
        $this->col[] = ["label" => "Name", "name" => "name"];
        $this->col[] = ["label" => "Table", "name" => "table_name"];
        $this->col[] = ["label" => "Path", "name" => "path"];
        $this->col[] = ["label" => "Controller", "name" => "controller"];
        $this->col[] = ["label" => "Protected", "name" => "is_protected", "visible" => false];

        $this->makeForm();

        $this->script_js = "
 			$(function() {
 				$('#table_name').change(function() {
					var v = $(this).val();
					$('#path').val(v);
				})	
 			}) ";

        $this->addaction[] = [
            'label' => 'Module Wizard',
            'icon' => 'fa fa-wrench',
            'url' => CRUDBooster::mainpath('step1').'/[id]',
            "showIf" => "[is_protected] == 0",
        ];

        $this->index_button[] = ['label' => 'Generate New Module', 'icon' => 'fa fa-plus', 'url' => CRUDBooster::mainpath('step1'), 'color' => 'success'];
    }
    // public function getIndex() {
    // 	$data['page_title'] = 'Module Generator';
    // 	$data['result'] = DB::table('cms_moduls')->where('is_protected',0)->orderby('name','asc')->get();
    // 	$this->cbView('crudbooster::module_generator.index',$data);		
    // }	

    function hookBeforeDelete($id)
    {
        $controller = DB::table('cms_moduls')->where('id', $id)->first()->controller;
        DB::table('cms_menus')->where('path', 'like', '%'.$controller.'%')->delete();
        @unlink($this->controller_path($controller));
    }

    public function getTableColumns($table)
    {
        $columns = CRUDBooster::getTableColumns($table);

        return response()->json($columns);
    }

    public function getCheckSlug($slug)
    {
        $check = DB::table('cms_moduls')->where('path', $slug)->count();
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

    public function getStep2($id)
    {
        $this->cbLoader();

        $row = DB::table('cms_moduls')->where('id', $id)->first();

        $columns = CRUDBooster::getTableColumns($row->table_name);

        $tables = CRUDBooster::listTables();
        $table_list = [];
        foreach ($tables as $tab) {
            foreach ($tab as $key => $value) {
                $table_list[] = $value;
            }
        }

        $code = file_get_contents(controller_path($row->controller.'.php'));

        $data = [];
        $data['id'] = $id;
        $data['columns'] = $columns;
        $data['table_list'] = $table_list;
        $data['cols'] = parseScaffoldingToArray($code, 'col');


        $hooks = ['hookQueryIndex', 'hookRowIndex', 'hookBeforeAdd', 'hookAfterAdd',
            'hookBeforeEdit', 'hookAfterEdit', 'hookBeforeDelete', 'hookAfterDelete',];
        foreach($hooks as $hook){
            $data[$hook] = readMethodContent($code, $hook);
        }

        return view('crudbooster::module_generator.step2', $data);
    }

    public function postStep2(Step1Handler $handler)
    {
        $this->cbLoader();
        return $handler->handleFormSubmit();
    }

    public function postStep3()
    {
        $this->cbLoader();

        $id = Request::input('id');
        $controller = DB::table('cms_moduls')->where('id', $id)->first()->controller;
        $controller_path = $this->controller_path($controller);

        $code = file_get_contents($controller_path);
        $rawBefore = explode("# START COLUMNS DO NOT REMOVE THIS LINE", $code);
        $rawAfter = explode("# END COLUMNS DO NOT REMOVE THIS LINE", $rawBefore[1]);

        $fileResult = trim($rawBefore[0]);
        $fileResult .= "\n\n            # START COLUMNS DO NOT REMOVE THIS LINE\n";
        $fileResult .= "            \$this->col = [];\n";
        $fileResult .= implode("\n", $this->makeColumnPhpCode());
        $fileResult .= "\n            # END COLUMNS DO NOT REMOVE THIS LINE\n\n            ";
        $fileResult .= trim($rawAfter[1]);

        $fileResult = writeMethodContent($fileResult, 'hookQueryIndex', g('hookQueryIndex'));
        $fileResult = writeMethodContent($fileResult, 'hookBeforeAdd', g('hookBeforeAdd'));
        $fileResult = writeMethodContent($fileResult, 'hookAfterAdd', g('hookAfterAdd'));
        $fileResult = writeMethodContent($fileResult, 'hookBeforeEdit', g('hookBeforeEdit'));
        $fileResult = writeMethodContent($fileResult, 'hookAfterEdit', g('hookAfterEdit'));
        $fileResult = writeMethodContent($fileResult, 'hookBeforeDelete', g('hookBeforeDelete'));
        $fileResult = writeMethodContent($fileResult, 'hookAfterDelete', g('hookAfterDelete'));

        file_put_contents($controller_path, $fileResult);

        return redirect(Route("AdminModulesControllerGetStep3", ["id" => $id]));
    }

    public function getStep3($id, Step3Handler $step3)
    {
        $this->cbLoader();
        return $step3->showForm($id);
    }

    public function getTypeInfo($type = 'text')
    {
        header("Content-Type: application/json");
        echo file_get_contents($this->componentsTypePath().$type.'/info.json');
    }

    public function postStep4(Step3Handler $handler)
    {
        $this->cbLoader();
        return $handler->handleFormSubmit();
    }

    public function getStep4($id)
    {
        $this->cbLoader();

        $row = DB::table('cms_moduls')->where('id', $id)->first();

        $data = [];
        $data['id'] = $id;
        if (file_exists($this->controller_path($row->controller))) {
            $response = file_get_contents($this->controller_path($row->controller));
            $data['config'] = parseControllerConfigToArray($response);
        }

        return view('crudbooster::module_generator.step4', $data);
    }

    public function postStepFinish()
    {
        $this->cbLoader();
        $id = Request::input('id');
        $row = DB::table('cms_moduls')->where('id', $id)->first();

        $post = Request::all();

        $post['table'] = $row->table_name;

        $script_config = [];
        $exception = ['_token', 'id', 'submit'];
        $i = 0;
        foreach ($post as $key => $val) {
            if (in_array($key, $exception)) {
                continue;
            }

            if ($val != 'true' && $val != 'false') {
                $value = '"'.$val.'"';
            } else {
                $value = $val;
            }

            $script_config[$i] = "            ".'$this->'.$key.' = '.$value.';';
            $i++;
        }

        $scripts = implode("\n", $script_config);
        $raw = file_get_contents($this->controller_path($row->controller));
        $raw = explode("# START CONFIGURATION DO NOT REMOVE THIS LINE", $raw);
        $rraw = explode("# END CONFIGURATION DO NOT REMOVE THIS LINE", $raw[1]);

        $file_controller = trim($raw[0])."\n\n";
        $file_controller .= "            # START CONFIGURATION DO NOT REMOVE THIS LINE\n";
        $file_controller .= $scripts."\n";
        $file_controller .= "            # END CONFIGURATION DO NOT REMOVE THIS LINE\n\n";
        $file_controller .= "            ".trim($rraw[1]);

        file_put_contents($this->controller_path($row->controller), $file_controller);

        return redirect()->route('AdminModulesControllerGetIndex')->with([
            'message' => trans('crudbooster.alert_update_data_success'),
            'message_type' => 'success',
        ]);
    }

    public function postAddSave()
    {
        $this->cbLoader();

        $this->validation();
        $this->inputAssignment();

        //Generate Controller 
        $route_basename = basename(request('path'));
        if ($this->arr['controller'] == '') {
            $this->arr['controller'] = CRUDBooster::generateController(request('table_name'), $route_basename);
        }

        $this->arr['created_at'] = date('Y-m-d H:i:s');
        $this->arr['id'] = $this->table()->max('id') + 1;
        $this->table()->insert($this->arr);

        //Insert Menu
        if ($this->arr['controller']) {
            $this->createMenuForModule();
        }

        $id_modul = $this->arr['id'];

        $user_id_privileges = CRUDBooster::myPrivilegeId();
        DB::table('cms_privileges_roles')->insert([
            'id_cms_moduls' => $id_modul,
            'id_cms_privileges' => $user_id_privileges,
            'is_visible' => 1,
            'is_create' => 1,
            'is_read' => 1,
            'is_edit' => 1,
            'is_delete' => 1,
        ]);

        //Refresh Session Roles
        $roles = DB::table('cms_privileges_roles')->where('id_cms_privileges', CRUDBooster::myPrivilegeId())->join('cms_moduls', 'cms_moduls.id', '=', 'id_cms_moduls')->select('cms_moduls.name', 'cms_moduls.path', 'is_visible', 'is_create', 'is_read', 'is_edit', 'is_delete')->get();
        Session::put('admin_privileges_roles', $roles);

        $ref_parameter = Request::input('ref_parameter');
        if (request('return_url')) {
            CRUDBooster::redirect(request('return_url'), trans("crudbooster.alert_add_data_success"), 'success');
        } 
        if (request('submit') == trans('crudbooster.button_save_more')) {
            CRUDBooster::redirect(CRUDBooster::mainpath('add'), trans("crudbooster.alert_add_data_success"), 'success');
        }
        CRUDBooster::redirect(CRUDBooster::mainpath(), trans("crudbooster.alert_add_data_success"), 'success');
    }

    public function postEditSave($id)
    {
        $this->cbLoader();

        $row = $this->table()->where($this->primary_key, $id)->first();

        $this->validation();
        $this->inputAssignment();

        //Generate Controller 
        $route_basename = basename(request('path'));
        if ($this->arr['controller'] == '') {
            $this->arr['controller'] = CRUDBooster::generateController(request('table_name'), $route_basename);
        }

        $this->findRow($id)->update($this->arr);

        //Refresh Session Roles
        $roles = DB::table('cms_privileges_roles')->where('id_cms_privileges', CRUDBooster::myPrivilegeId())->join('cms_moduls', 'cms_moduls.id', '=', 'id_cms_moduls')->select('cms_moduls.name', 'cms_moduls.path', 'is_visible', 'is_create', 'is_read', 'is_edit', 'is_delete')->get();
        Session::put('admin_privileges_roles', $roles);

        CRUDBooster::redirect(Request::server('HTTP_REFERER'), trans('crudbooster.alert_update_data_success'), 'success');
    }

    public function getTest()
    {
        $code = file_get_contents($this->controller_path('AdminCustomersController.php'));

        $forms = parseFormToArray($code);
        echo '<pre>';
        print_r($forms);
    }

    private function makeForm()
    {
        $this->form = [];
        $this->form[] = ["label" => "Name", "name" => "name", "placeholder" => "Module name here", 'required' => true];

        $tables_list = [];
        foreach (CRUDBooster::listTables() as $table) {

            unset($table['migrations']);
            foreach ($table as $key => $label) {
                if (substr($label, 0, 4) == 'cms_' && $label != 'cms_users') {
                    continue;
                }

                $tables_list[] = $label;
            }
        }

        $this->form[] = [
            "label" => "Table Name",
            "name" => "table_name",
            "type" => "select2_dataenum",
            "options" => ['enum' => $tables_list],
            'required' => true,
        ];

        $fontawesome = FontAwesome::cssClass();
        $row = CRUDBooster::first($this->table, CRUDBooster::getCurrentId());
        $custom = view('crudbooster::components.list_icon', compact('fontawesome', 'row'))->render();
        $this->form[] = ['label' => 'Icon', 'name' => 'icon', 'type' => 'custom_html', 'options' => ['html' => $custom], 'required' => true];

        $this->form[] = ["label" => "Path", "name" => "path", "required" => true, 'placeholder' => 'Optional'];
        $this->form[] = ["label" => "Controller", "name" => "controller", "type" => "text", "placeholder" => "(Optional) Auto Generated"];

        if (in_array(CRUDBooster::getCurrentMethod(), ['getAdd', 'postAddSave'])) {
            return ;
        }

        $this->form[] = [
            "label" => "Global Privilege",
            "name" => "global_privilege",
            "type" => "radio",
            "dataenum" => ['0|No', '1|Yes'],
            'value' => 0,
            'help' => 'Global Privilege allows you to make the module to be accessible by all privileges',
            'exception' => true,
        ];

        $this->form[] = [
            "label" => "Button Action Style",
            "name" => "button_action_style",
            "type" => "radio",
            "dataenum" => ['button_icon', 'button_icon_text', 'button_text', 'dropdown'],
            'value' => 'button_icon',
            'exception' => true,
        ];
        $this->form[] = [
            "label" => "Button Table Action",
            "name" => "button_table_action",
            "type" => "radio",
            "dataenum" => ['Yes', 'No'],
            'value' => 'Yes',
            'exception' => true,
        ];
        $this->form[] = [
            "label" => "Button Add",
            "name" => "button_add",
            "type" => "radio",
            "dataenum" => ['Yes', 'No'],
            'value' => 'Yes',
            'exception' => true,
        ];
        $this->form[] = [
            "label" => "Button Delete",
            "name" => "button_delete",
            "type" => "radio",
            "dataenum" => ['Yes', 'No'],
            'value' => 'Yes',
            'exception' => true,
        ];
        $this->form[] = [
            "label" => "Button Edit",
            "name" => "button_edit",
            "type" => "radio",
            "dataenum" => ['Yes', 'No'],
            'value' => 'Yes',
            'exception' => true,
        ];
        $this->form[] = [
            "label" => "Button Detail",
            "name" => "button_detail",
            "type" => "radio",
            "dataenum" => ['Yes', 'No'],
            'value' => 'Yes',
            'exception' => true,
        ];
        $this->form[] = [
            "label" => "Button Show",
            "name" => "button_show",
            "type" => "radio",
            "dataenum" => ['Yes', 'No'],
            'value' => 'Yes',
            'exception' => true,
        ];
        $this->form[] = [
            "label" => "Button Filter",
            "name" => "button_filter",
            "type" => "radio",
            "dataenum" => ['Yes', 'No'],
            'value' => 'Yes',
            'exception' => true,
        ];
        $this->form[] = [
            "label" => "Button Export",
            "name" => "button_export",
            "type" => "radio",
            "dataenum" => ['Yes', 'No'],
            'value' => 'No',
            'exception' => true,
        ];
        $this->form[] = [
            "label" => "Button Import",
            "name" => "button_import",
            "type" => "radio",
            "dataenum" => ['Yes', 'No'],
            'value' => 'No',
            'exception' => true,
        ];

    }

    /**
     * @param $controller
     * @return mixed
     */
    private function controller_path($controller)
    {
        return app_path('Http/Controllers/'.$controller.'.php');
    }

    private function componentsTypePath()
    {
        return base_path('vendor/crocodicstudio/crudbooster/src/views/default/type_components/');
    }

    private function createMenuForModule()
    {
        $parent_menu_sort = DB::table('cms_menus')->where('parent_id', 0)->max('sorting') + 1;
        $parent_menu_id = DB::table('cms_menus')->insertGetId([
            'created_at' => date('Y-m-d H:i:s'),
            'name' => $this->arr['name'],
            'icon' => $this->arr['icon'],
            'path' => '#',
            'type' => 'URL External',
            'is_active' => 1,
            'cms_privileges' => CRUDBooster::myPrivilegeId(),
            'sorting' => $parent_menu_sort,
            'parent_id' => 0,
        ]);

        $arr = [
            'created_at' => date('Y-m-d H:i:s'),
            'type' => 'Route',
            'is_active' => 1,
            'cms_privileges' => CRUDBooster::myPrivilegeId(),
            'parent_id' => $parent_menu_id,
        ];

        DB::table('cms_menus')->insert([
            'name' => trans("crudbooster.text_default_add_new_module", ['module' => $this->arr['name']]),
            'icon' => 'fa fa-plus',
            'path' => $this->arr['controller'].'GetAdd',
            'sorting' => 1,
        ] + $arr);

        DB::table('cms_menus')->insert([
            'name' => trans("crudbooster.text_default_list_module", ['module' => $this->arr['name']]),
            'icon' => 'fa fa-bars',
            'path' => $this->arr['controller'].'GetIndex',
            'cms_privileges' => CRUDBooster::myPrivilegeId(),
            'sorting' => 2,
        ] + $arr);

    }

    /**
     * @return array
     */
    private function makeColumnPhpCode()
    {
        $labels = Request::input('column');
        $name = Request::input('name');
        $isImage = Request::input('is_image');
        $isDownload = Request::input('is_download');
        $callback = Request::input('callback');
        $width = Request::input('width');

        $columnScript = [];
        foreach ($labels as $i => $label) {

            if (! $name[$i]) {
                continue;
            }

            $colProperties = ["'label' => '$label'", "'name' => '{$name[$i]}'"];
            if ($isImage[$i]) {
                $colProperties[] = '"image" => true ';
            }
            if ($isDownload[$i]) {
                $colProperties[] = '"download" => true';
            }
            if ($callback[$i]) {
                $colProperties[] = '"callback" => function($row) {'.$callback[$i].'}';
            }
            if ($width[$i]) {
                $colProperties[] = "'width' => '$width[$i]'";
            }

            $columnScript[] = '            $this->col[] = ['.implode(", ", $colProperties).'];';
        }

        return $columnScript;
    }
}
