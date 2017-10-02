<?php namespace crocodicstudio\crudbooster\controllers;

use crocodicstudio\crudbooster\controllers\Controller;
use crocodicstudio\crudbooster\controllers\Helpers\FontAwesome;
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
        $modul = DB::table('cms_moduls')->where('id', $id)->first();
        $menus = DB::table('cms_menus')->where('path', 'like', '%'.$modul->controller.'%')->delete();
        @unlink(app_path('Http/Controllers/'.$modul->controller.'.php'));
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

    public function getStep1($id = 0)
    {
        $this->cbLoader();

        $tables = CRUDBooster::listTables();
        $tables_list = [];
        foreach ($tables as $tab) {
            foreach ($tab as $key => $value) {
                $label = $value;

                if (substr($label, 0, 4) == 'cms_' && $label != 'cms_users') {
                    continue;
                }
                if ($label == 'migrations') {
                    continue;
                }

                $tables_list[] = $value;
            }
        }
        $row = CRUDBooster::first($this->table, ['id' => $id]);

        return view("crudbooster::module_generator.step1", compact("tables_list", "fontawesome", "row", "id"));
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
                $label = $value;
                $table_list[] = $value;
            }
        }

        $code = file_get_contents(base_path('app/Http/Controllers/'.$row->controller.'.php'));

        $data = [];
        $data['id'] = $id;
        $data['columns'] = $columns;
        $data['table_list'] = $table_list;
        $data['cols'] = parseScaffoldingToArray($code, 'col');

        $data['hookQueryIndex'] = readMethodContent($code, 'hookQueryIndex');
        $data['hookRowIndex'] = readMethodContent($code, 'hookRowIndex');
        $data['hookBeforeAdd'] = readMethodContent($code, 'hookBeforeAdd');
        $data['hookAfterAdd'] = readMethodContent($code, 'hookAfterAdd');
        $data['hookBeforeEdit'] = readMethodContent($code, 'hookBeforeEdit');
        $data['hookAfterEdit'] = readMethodContent($code, 'hookAfterEdit');
        $data['hookBeforeDelete'] = readMethodContent($code, 'hookBeforeDelete');
        $data['hookAfterDelete'] = readMethodContent($code, 'hookAfterDelete');

        return view('crudbooster::module_generator.step2', $data);
    }

    public function postStep2()
    {
        $this->cbLoader();

        $name = Request::get('name');
        $table_name = Request::get('table');
        $icon = Request::get('icon');
        $path = Request::get('path');

        if (! Request::get('id')) {

            if (DB::table('cms_moduls')->where('path', $path)->where('deleted_at', null)->count()) {
                return CRUDBooster::backWithMsg('Sorry the slug has already exists, please choose another !', 'warning');
            }

            $created_at = now();

            $controller = CRUDBooster::generateController($table_name, $path);
            $id = DB::table($this->table)->insertGetId(compact("controller", "name", "table_name", "icon", "path", "created_at"));

            //Insert Menu
            if ($controller && Request::get('create_menu')) {
                $parent_menu_sort = DB::table('cms_menus')->where('parent_id', 0)->max('sorting') + 1;

                DB::table('cms_menus')->insert([
                    'created_at' => date('Y-m-d H:i:s'),
                    'name' => $name,
                    'icon' => $icon,
                    'path' => $controller.'GetIndex',
                    'type' => 'Route',
                    'is_active' => 1,
                    'cms_privileges' => '["'.CRUDBooster::myPrivilegeName().'"]',
                    'sorting' => $parent_menu_sort,
                    'parent_id' => 0,
                ]);
            }

            $user_id_privileges = CRUDBooster::myPrivilegeId();
            DB::table('cms_privileges_roles')->insert([
                'id' => DB::table('cms_privileges_roles')->max('id') + 1,
                'id_cms_moduls' => $id,
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

            return redirect(Route("AdminModulesControllerGetStep2", ["id" => $id]));
        }

        $id = Request::get('id');
        DB::table($this->table)->where('id', $id)->update(compact("name", "table_name", "icon", "path"));

        $row = DB::table('cms_moduls')->where('id', $id)->first();

        $response = file_get_contents(__DIR__.'/'.str_replace('.', '', $row->controller).'.php');
        if (file_exists(app_path('Http/Controllers/'.$row->controller.'.php'))) {
            $response = file_get_contents(app_path('Http/Controllers/'.str_replace('.', '', $row->controller).'.php'));
        }

        if (strpos($response, "# START COLUMNS") !== true) {
            // return redirect()->back()->with(['message'=>'Sorry, is not possible to edit the module with Module Generator Tool. Prefix and or Suffix tag is missing !','message_type'=>'warning']);
        }

        return redirect(Route("AdminModulesControllerGetStep2", ["id" => $id]));
    }

    public function postStep3()
    {
        $this->cbLoader();

        $label = Request::input('column');
        $name = Request::input('name');
        $isImage = Request::input('is_image');
        $isDownload = Request::input('is_download');
        $callback = Request::input('callback');
        $id = Request::input('id');
        $width = Request::input('width');

        $row = DB::table('cms_moduls')->where('id', $id)->first();
        $columnScript = [];
        foreach ($label as $i => $lab) {

            if (! $name[$i]) {
                continue;
            }

            $colKey = [];
            $colKey[] = '"label"=>"'.$lab.'"';
            $colKey[] = '"name"=>"'.$name[$i].'"';
            if ($isImage[$i]) {
                $colKey[] = '"image"=>true';
            }
            if ($isDownload[$i]) {
                $colKey[] = '"download"=>true';
            }
            if ($callback[$i]) {
                $colKey[] = '"callback"=>function($row) {'.$callback[$i].'}';
            }
            if ($width[$i]) {
                $colKey[] = '"width"=>"'.$width[$i].'"';
            }

            $columnScript[] = "\t\t\t".'$this->col[] = ['.implode(",", $colKey).'];';
        }

        $code = file_get_contents(app_path('Http/Controllers/'.$row->controller.'.php'));
        $rawBefore = explode("# START COLUMNS DO NOT REMOVE THIS LINE", $code);
        $rawAfter = explode("# END COLUMNS DO NOT REMOVE THIS LINE", $rawBefore[1]);

        $fileResult = trim($rawBefore[0]);
        $fileResult .= "\n";
        $fileResult .= "\n\t\t\t# START COLUMNS DO NOT REMOVE THIS LINE\n";
        $fileResult .= "\t\t\t".'$this->col = [];'."\n";
        $fileResult .= implode("\n", $columnScript);
        $fileResult .= "\n\t\t\t# END COLUMNS DO NOT REMOVE THIS LINE\n";
        $fileResult .= "\n\t\t\t";
        $fileResult .= trim($rawAfter[1]);

        $fileResult = writeMethodContent($fileResult, 'hookQueryIndex', g('hookQueryIndex'));
        $fileResult = writeMethodContent($fileResult, 'hookBeforeAdd', g('hookBeforeAdd'));
        $fileResult = writeMethodContent($fileResult, 'hookAfterAdd', g('hookAfterAdd'));
        $fileResult = writeMethodContent($fileResult, 'hookBeforeEdit', g('hookBeforeEdit'));
        $fileResult = writeMethodContent($fileResult, 'hookAfterEdit', g('hookAfterEdit'));
        $fileResult = writeMethodContent($fileResult, 'hookBeforeDelete', g('hookBeforeDelete'));
        $fileResult = writeMethodContent($fileResult, 'hookAfterDelete', g('hookAfterDelete'));

        file_put_contents(app_path('Http/Controllers/'.$row->controller.'.php'), $fileResult);

        return redirect(Route("AdminModulesControllerGetStep3", ["id" => $id]));
    }

    public function getStep3($id)
    {
        $this->cbLoader();

        $row = DB::table('cms_moduls')->where('id', $id)->first();

        $columns = CRUDBooster::getTableColumns($row->table_name);

        $code = file_get_contents(base_path('app/Http/Controllers/'.$row->controller.'.php'));

        $forms = parseScaffoldingToArray($code, 'form');

        $types = [];
        foreach (glob(base_path('vendor/crocodicstudio/crudbooster/src/views/default/type_components').'/*', GLOB_ONLYDIR) as $dir) {
            $types[] = basename($dir);
        }

        return view('crudbooster::module_generator.step3', compact('columns', 'forms', 'types', 'id'));
    }

    public function getTypeInfo($type = 'text')
    {
        header("Content-Type: application/json");
        echo file_get_contents(base_path('vendor/crocodicstudio/crudbooster/src/views/default/type_components/'.$type.'/info.json'));
    }

    public function postStep4()
    {
        $this->cbLoader();

        $post = Request::all();

        $id = $post['id'];

        $labels = $post['label'];
        $name = $post['name'];
        $width = $post['width'];
        $type = $post['type'];
        $help = $post['help'];
        $placeholder = $post['placeholder'];
        $style = $post['style'];
        $validation = $post['validation'];

        $row = DB::table('cms_moduls')->where('id', $id)->first();

        $i = 0;
        $script_form = [];
        foreach ($labels as $i => $label) {
            $currentName = $name[$i];
            $form = [];
            $form['label'] = $label;
            $form['name'] = $name[$i];
            $form['type'] = $type[$i];
            $form['validation'] = $validation[$i];
            $form['width'] = $width[$i];
            $form['placeholder'] = $placeholder[$i];
            $form['help'] = $help[$i];
            $form['style'] = $style[$i];

            if ($label != '') {

                $info = file_get_contents(base_path('vendor/crocodicstudio/crudbooster/src/views/default/type_components/'.$type[$i].'/info.json'));
                $info = json_decode($info, true);
                if (count($info['options'])) {
                    $options = [];
                    foreach ($info['options'] as $i => $opt) {
                        $optionName = $opt['name'];
                        $optionValue = $post[$optionName][$currentName];
                        if ($opt['type'] == 'array') {
                            $optionValue = ($optionValue) ? explode(";", $optionValue) : [];
                        } elseif ($opt['type'] == 'boolean') {
                            $optionValue = ($optionValue == 1) ? true : false;
                        }
                        $options[$optionName] = $optionValue;
                    }
                    $form['options'] = $options;
                }

                $script_form[] = "\t\t\t".'$this->form[] = '.min_var_export($form, "\t\t\t").";";
            }
        }

        $scripts = implode("\n", $script_form);
        $raw = file_get_contents(app_path('Http/Controllers/'.$row->controller.'.php'));
        $raw = explode("# START FORM DO NOT REMOVE THIS LINE", $raw);
        $rraw = explode("# END FORM DO NOT REMOVE THIS LINE", $raw[1]);

        $top_script = trim($raw[0]);
        $current_scaffolding_form = trim($rraw[0]);
        $bottom_script = trim($rraw[1]);

        //IF FOUND OLD, THEN CLEAR IT
        if (strpos($bottom_script, '# OLD START FORM') !== false) {
            $line_end_count = strlen('# OLD END FORM');
            $line_start_old = strpos($bottom_script, '# OLD START FORM');
            $line_end_old = strpos($bottom_script, '# OLD END FORM') + $line_end_count;
            $get_string = substr($bottom_script, $line_start_old, $line_end_old);
            $bottom_script = str_replace($get_string, '', $bottom_script);
        }

        //ARRANGE THE FULL SCRIPT
        $file_controller = $top_script."\n\n";
        $file_controller .= "\t\t\t# START FORM DO NOT REMOVE THIS LINE\n";
        $file_controller .= "\t\t\t".'$this->form = [];'."\n";
        $file_controller .= $scripts."\n";
        $file_controller .= "\t\t\t# END FORM DO NOT REMOVE THIS LINE\n\n";

        //CREATE A BACKUP SCAFFOLDING TO OLD TAG
        if ($current_scaffolding_form) {
            $current_scaffolding_form = preg_split("/\\r\\n|\\r|\\n/", $current_scaffolding_form);
            foreach ($current_scaffolding_form as &$c) {
                $c = "\t\t\t//".trim($c);
            }
            $current_scaffolding_form = implode("\n", $current_scaffolding_form);

            $file_controller .= "\t\t\t# OLD START FORM\n";
            $file_controller .= $current_scaffolding_form."\n";
            $file_controller .= "\t\t\t# OLD END FORM\n\n";
        }

        $file_controller .= "\t\t\t".trim($bottom_script);

        //CREATE FILE CONTROLLER
        file_put_contents(app_path('Http/Controllers/'.$row->controller.'.php'), $file_controller);

        return redirect(Route("AdminModulesControllerGetStep4", ["id" => $id]));
    }

    public function getStep4($id)
    {
        $this->cbLoader();

        $row = DB::table('cms_moduls')->where('id', $id)->first();

        $data = [];
        $data['id'] = $id;
        if (file_exists(app_path('Http/Controllers/'.$row->controller.'.php'))) {
            $response = file_get_contents(app_path('Http/Controllers/'.$row->controller.'.php'));
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

            $script_config[$i] = "\t\t\t".'$this->'.$key.' = '.$value.';';
            $i++;
        }

        $scripts = implode("\n", $script_config);
        $raw = file_get_contents(app_path('Http/Controllers/'.$row->controller.'.php'));
        $raw = explode("# START CONFIGURATION DO NOT REMOVE THIS LINE", $raw);
        $rraw = explode("# END CONFIGURATION DO NOT REMOVE THIS LINE", $raw[1]);

        $file_controller = trim($raw[0])."\n\n";
        $file_controller .= "\t\t\t# START CONFIGURATION DO NOT REMOVE THIS LINE\n";
        $file_controller .= $scripts."\n";
        $file_controller .= "\t\t\t# END CONFIGURATION DO NOT REMOVE THIS LINE\n\n";
        $file_controller .= "\t\t\t".trim($rraw[1]);

        file_put_contents(app_path('Http/Controllers/'.$row->controller.'.php'), $file_controller);

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
        $route_basename = basename(Request::get('path'));
        if ($this->arr['controller'] == '') {
            $this->arr['controller'] = CRUDBooster::generateController(Request::get('table_name'), $route_basename);
        }

        $this->arr['created_at'] = date('Y-m-d H:i:s');
        $this->arr['id'] = DB::table($this->table)->max('id') + 1;
        DB::table($this->table)->insert($this->arr);

        //Insert Menu
        if ($this->arr['controller']) {
            $parent_menu_sort = DB::table('cms_menus')->where('parent_id', 0)->max('sorting') + 1;
            $parent_menu_id = DB::table('cms_menus')->max('id') + 1;
            DB::table('cms_menus')->insert([
                'id' => $parent_menu_id,
                'created_at' => date('Y-m-d H:i:s'),
                'name' => $this->arr['name'],
                'icon' => $this->arr['icon'],
                'path' => '#',
                'type' => 'URL External',
                'is_active' => 1,
                'id_cms_privileges' => CRUDBooster::myPrivilegeId(),
                'sorting' => $parent_menu_sort,
                'parent_id' => 0,
            ]);
            DB::table('cms_menus')->insert([
                'id' => DB::table('cms_menus')->max('id') + 1,
                'created_at' => date('Y-m-d H:i:s'),
                'name' => trans("crudbooster.text_default_add_new_module", ['module' => $this->arr['name']]),
                'icon' => 'fa fa-plus',
                'path' => $this->arr['controller'].'GetAdd',
                'type' => 'Route',
                'is_active' => 1,
                'id_cms_privileges' => CRUDBooster::myPrivilegeId(),
                'sorting' => 1,
                'parent_id' => $parent_menu_id,
            ]);
            DB::table('cms_menus')->insert([
                'id' => DB::table('cms_menus')->max('id') + 1,
                'created_at' => date('Y-m-d H:i:s'),
                'name' => trans("crudbooster.text_default_list_module", ['module' => $this->arr['name']]),
                'icon' => 'fa fa-bars',
                'path' => $this->arr['controller'].'GetIndex',
                'type' => 'Route',
                'is_active' => 1,
                'id_cms_privileges' => CRUDBooster::myPrivilegeId(),
                'sorting' => 2,
                'parent_id' => $parent_menu_id,
            ]);
        }

        $id_modul = $this->arr['id'];

        $user_id_privileges = CRUDBooster::myPrivilegeId();
        DB::table('cms_privileges_roles')->insert([
            'id' => DB::table('cms_privileges_roles')->max('id') + 1,
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
        if (Request::get('return_url')) {
            CRUDBooster::redirect(Request::get('return_url'), trans("crudbooster.alert_add_data_success"), 'success');
        } else {
            if (Request::get('submit') == trans('crudbooster.button_save_more')) {
                CRUDBooster::redirect(CRUDBooster::mainpath('add'), trans("crudbooster.alert_add_data_success"), 'success');
            } else {
                CRUDBooster::redirect(CRUDBooster::mainpath(), trans("crudbooster.alert_add_data_success"), 'success');
            }
        }
    }

    public function postEditSave($id)
    {
        $this->cbLoader();

        $row = DB::table($this->table)->where($this->primary_key, $id)->first();

        $this->validation();
        $this->inputAssignment();

        //Generate Controller 
        $route_basename = basename(Request::get('path'));
        if ($this->arr['controller'] == '') {
            $this->arr['controller'] = CRUDBooster::generateController(Request::get('table_name'), $route_basename);
        }

        DB::table($this->table)->where($this->primary_key, $id)->update($this->arr);

        //Refresh Session Roles
        $roles = DB::table('cms_privileges_roles')->where('id_cms_privileges', CRUDBooster::myPrivilegeId())->join('cms_moduls', 'cms_moduls.id', '=', 'id_cms_moduls')->select('cms_moduls.name', 'cms_moduls.path', 'is_visible', 'is_create', 'is_read', 'is_edit', 'is_delete')->get();
        Session::put('admin_privileges_roles', $roles);

        CRUDBooster::redirect(Request::server('HTTP_REFERER'), trans('crudbooster.alert_update_data_success'), 'success');
    }

    public function getTest()
    {
        $code = file_get_contents(base_path('app/Http/Controllers/AdminCustomersController.php'));

        $forms = parseFormToArray($code);
        echo '<pre>';
        print_r($forms);
    }

    private function makeForm()
    {
        $this->form = [];
        $this->form[] = ["label" => "Name", "name" => "name", "placeholder" => "Module name here", 'required' => true];

        $tables_list = [];
        foreach (CRUDBooster::listTables() as $tab) {
            foreach ($tab as $key => $label) {
                if (substr($label, 0, 4) == 'cms_' && $label != 'cms_users') {
                    continue;
                }
                if ($label == 'migrations') {
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
}
