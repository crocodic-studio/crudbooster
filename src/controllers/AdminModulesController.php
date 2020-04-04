<?php namespace crocodicstudio\crudbooster\controllers;

use crocodicstudio\crudbooster\helpers\ModuleControllerGenerator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Excel;
use Illuminate\Support\Facades\PDF;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use crocodicstudio\crudbooster\fonts\Fontawesome;

class AdminModulesController extends CBController
{
    public $module_name = "Module Generator";
    public $page_icon = "fa fa-cubes";
    public $table = 'cms_moduls';
    public $primary_key = 'id';
    public $title_field = "name";
    public $limit = 20;
    public $button_add = false;
    public $button_export = false;
    public $button_import = false;
    public $button_filter = false;
    public $button_detail = false;
    public $button_bulk_action   = false;
    public $button_action_style = 'button_icon';
    public $orderby = ['id'=>'desc'];

    public function cbInit()
    {
        $this->col   = [];
        $this->col[] = ["label" => "Name", "name" => "name"];
        $this->col[] = ["label" => "Table", "name" => "table_name"];
        $this->col[] = ["label" => "Path", "name" => "path"];
        $this->col[] = ["label" => "Controller", "name" => "controller"];

        $this->form = [];
        $this->form[] = ["label" => "Name", "name" => "name", "placeholder" => "Module name here", 'required' => true];

        $fontawesome = Fontawesome::getIcons();

        $row = cb()->first($this->table, cb()->getCurrentId());
        $custom = view('crudbooster::components.list_icon', compact('fontawesome', 'row'))->render();
        $this->form[] = ['label' => 'Icon', 'name' => 'icon', 'type' => 'custom', 'html' => $custom, 'required' => true];
        $this->form[] = ["label" => "Path", "name" => "path", "required" => true, 'placeholder' => 'Optional'];

        $this->addaction[] = [
            'label' => 'Regenerate Module',
            'icon' => 'fa fa-refresh',
            'url' => cb()->mainpath('step1').'/[id]'
        ];

        $this->onIndex(function() {
            $this->index_button[] = ['label' => 'Generate New Module', 'icon' => 'fa fa-plus', 'url' => cb()->mainpath('step1'), 'color' => 'success'];
        });

    }

    function hook_query_index(&$query)
    {
        $query->where('is_protected', 0);
        $query->whereNotIn('cms_moduls.controller', ['AdminCmsUsersController']);
    }

    function hook_before_delete($id)
    {
        $modul = DB::table('cms_moduls')->where('id', $id)->first();
        $menus = DB::table('cms_menus')->where('path', 'like', '%'.$modul->controller.'%')->delete();
        @unlink(app_path('Http/Controllers/'.$modul->controller.'.php'));
    }

    public function getTableColumns($table)
    {
        $columns = cb()->getTableColumns($table);

        return response()->json($columns);
    }

    public function getCheckSlug($slug)
    {
        $check = DB::table('cms_moduls')->where('path', $slug)->count();
        $lastId = DB::table('cms_moduls')->max('id') + 1;

        return response()->json(['total' => $check, 'lastid' => $lastId]);
    }

    private function createMenu($name, $icon, $path, $id_cms_privileges = []) {

        // Clear existing menu
        DB::table('cms_menus')->where('path', $path)->delete();

        $parent_menu_sort = DB::table('cms_menus')
                ->where('parent_id', 0)
                ->max('sorting') + 1;

        $id_cms_menus = DB::table('cms_menus')->insertGetId([
            'created_at' => date('Y-m-d H:i:s'),
            'name' => $name,
            'icon' => $icon,
            'path' => $path,
            'type' => 'URL',
            'is_active' => 1,
            'id_cms_privileges' => cb()->auth()->roleId(),
            'sorting' => $parent_menu_sort,
            'parent_id' => 0,
        ]);

        if($id_cms_privileges) {
            foreach($id_cms_privileges as $id_cms_privilege) {
                DB::table('cms_menus_privileges')
                    ->insert(['id_cms_menus' => $id_cms_menus, 'id_cms_privileges' => $id_cms_privilege]);
            }
        } else {
            DB::table('cms_menus_privileges')
                ->insert(['id_cms_menus' => $id_cms_menus, 'id_cms_privileges' => cb()->myPrivilegeId()]);
        }
    }

    private function createRolePermission($modules_id, $id_cms_privileges = []) {
        if($id_cms_privileges) {
            foreach($id_cms_privileges as $role_id) {
                DB::table('cms_privileges_roles')->insert([
                    'id_cms_moduls' => $id,
                    'id_cms_privileges' => $role_id,
                    'is_visible' => 1,
                    'is_create' => 1,
                    'is_read' => 1,
                    'is_edit' => 1,
                    'is_delete' => 1,
                ]);
            }
        } else {
            $user_id_privileges = cb()->myPrivilegeId();
            DB::table('cms_privileges_roles')->insert([
                'id_cms_moduls' => $id,
                'id_cms_privileges' => $user_id_privileges,
                'is_visible' => 1,
                'is_create' => 1,
                'is_read' => 1,
                'is_edit' => 1,
                'is_delete' => 1,
            ]);
        }
    }

    public function getStep1($id = 0)
    {

        $tables = cb()->listTables();
        $tables_list = [];
        foreach ($tables as $table_name) {
            foreach ($table_name as $key => $value) {
                $label = $value;

                if (substr($label, 0, 4) == 'cms_' && $label != config('crudbooster.USER_TABLE')) {
                    continue;
                }
                if ($label == 'migrations') {
                    continue;
                }

                $tables_list[] = $value;
            }
        }

        $fontawesome = Fontawesome::getIcons();

        $row = cb()->first($this->table, ['id' => $id]);

        $data = [];
        $data['page_title'] = "Module Generator";
        $data['tables_list'] = $tables_list;
        $data['fontawesome'] = $fontawesome;
        $data['row'] = $row;
        $data['id'] = $id;

        return view("crudbooster::module_generator.step1", $data);
    }

    public function getStep2($id)
    {
        $row = DB::table('cms_moduls')->where('id', $id)->first();

        $columns = cb()->getTableColumns($row->table_name);

        $tables = cb()->listTables();
        $table_list = [];
        foreach ($tables as $tab) {
            foreach ($tab as $key => $value) {
                $label = $value;
                $table_list[] = $value;
            }
        }

        if (file_exists(app_path('Http/Controllers/'.str_replace('.', '', $row->controller).'.php'))) {
            $response = file_get_contents(app_path('Http/Controllers/'.$row->controller.'.php'));
            $column_datas = extract_unit($response, "# START COLUMNS DO NOT REMOVE THIS LINE", "# END COLUMNS DO NOT REMOVE THIS LINE");
            $column_datas = str_replace('$this->', '$cb_', $column_datas);
            eval($column_datas);
        }

        $data = [];
        $data['id'] = $id;
        $data['columns'] = $columns;
        $data['table_list'] = $table_list;
        $data['cb_col'] = $cb_col;

        return view('crudbooster::module_generator.step2', $data);
    }

    public function postStep2()
    {
        $name = request('name');
        $table_name = request('table');
        $icon = request('icon');
        $path = request('path');
        $id = request('id');

        if (!request('id') && DB::table('cms_moduls')->where('path', $path)->where('deleted_at', null)->count()) {
            return redirect()->back()->with(['message' => 'Sorry the slug has already exists, please choose another !', 'type' => 'warning']);
        }

        $created_at = date_now();

        // Generate a Module Controller
        $generate_controller = new ModuleControllerGenerator();
        $generate_controller->table = $table_name;
        $generate_controller->module_name = $name;
        $generate_controller->generate_type = \request('include_controller_doc')?"Advanced":"Simple";
        $controller = $generate_controller->generate();

        if(\request('id')) {

            // Create new menu
            if ($controller && request('create_menu')) $this->createMenu($name, $icon, $path, \request('id_cms_privileges'));

            // Update module data
            DB::table($this->table)->where("id", \request('id'))
                ->update(compact("controller", "name", "table_name", "icon", "path"));
        } else {

            // Create module data
            $id = DB::table($this->table)
                ->insertGetId(compact("controller", "name", "table_name", "icon", "path", "created_at"));
        }

        // Create role permission
        $this->createRolePermission($id, \request('id_cms_privileges'));

        // Refresh Session Roles
        cb()->auth()->refreshRole();

        return redirect(cb()->adminPath('modules/step2/'.$id));
    }

    public function postStep3()
    {
        $column = Request::input('column');
        $name = Request::input('name');
        $join_table = Request::input('join_table');
        $join_field = Request::input('join_field');
        $is_image = Request::input('is_image');
        $is_download = Request::input('is_download');
        $callbackphp = Request::input('callbackphp');
        $id = Request::input('id');
        $width = Request::input('width');

        $row = DB::table('cms_moduls')->where('id', $id)->first();

        $i = 0;
        $script_cols = [];
        foreach ($column as $col) {

            if (! $name[$i]) {
                $i++;
                continue;
            }

            $script_cols[$i] = "\t\t\t".'$this->col[] = ["label"=>"'.$col.'","name"=>"'.$name[$i].'"';

            if ($join_table[$i] && $join_field[$i]) {
                $script_cols[$i] .= ',"join"=>"'.$join_table[$i].','.$join_field[$i].'"';
            }

            if ($is_image[$i]) {
                $script_cols[$i] .= ',"image"=>true';
            }

            if ($id_download[$i]) {
                $script_cols[$i] .= ',"download"=>true';
            }

            if ($width[$i]) {
                $script_cols[$i] .= ',"width"=>"'.$width[$i].'"';
            }

            if ($callbackphp[$i]) {
                $script_cols[$i] .= ',"callback_php"=>\''.$callbackphp[$i].'\'';
            }

            $script_cols[$i] .= "];";

            $i++;
        }

        $scripts = implode("\n", $script_cols);
        $raw = file_get_contents(app_path('Http/Controllers/'.$row->controller.'.php'));
        $raw = explode("# START COLUMNS DO NOT REMOVE THIS LINE", $raw);
        $rraw = explode("# END COLUMNS DO NOT REMOVE THIS LINE", $raw[1]);

        $file_controller = trim($raw[0])."\n\n";
        $file_controller .= "\t\t\t# START COLUMNS DO NOT REMOVE THIS LINE\n";
        $file_controller .= "\t\t\t".'$this->col = [];'."\n";
        $file_controller .= $scripts."\n";
        $file_controller .= "\t\t\t# END COLUMNS DO NOT REMOVE THIS LINE\n\n";
        $file_controller .= "\t\t\t".trim($rraw[1]);

        file_put_contents(app_path('Http/Controllers/'.$row->controller.'.php'), $file_controller);

        return redirect(cb()->adminPath('modules/step3/'.$id));
    }

    public function getStep3($id)
    {
        $this->cbLoader();

        $module = cb()->getCurrentModule();

        if (! cb()->isView() && $this->global_privilege == false) {
            cb()->insertLog(trans('crudbooster.log_try_view', ['module' => $module->name]));
            cb()->redirect(cb()->adminPath(), trans('crudbooster.denied_access'));
        }

        $row = DB::table('cms_moduls')->where('id', $id)->first();

        $columns = cb()->getTableColumns($row->table_name);

        if (file_exists(app_path('Http/Controllers/'.$row->controller.'.php'))) {
            $response = file_get_contents(app_path('Http/Controllers/'.$row->controller.'.php'));
            $column_datas = extract_unit($response, "# START FORM DO NOT REMOVE THIS LINE", "# END FORM DO NOT REMOVE THIS LINE");
            $column_datas = str_replace('$this->', '$cb_', $column_datas);
            eval($column_datas);
        }

        $types = [];
        foreach (glob(base_path('vendor/crocodicstudio/crudbooster/src/views/default/type_components').'/*', GLOB_ONLYDIR) as $dir) {
            $types[] = basename($dir);
        }

        return view('crudbooster::module_generator.step3', compact('columns', 'cb_form', 'types', 'id'));
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

        $label = $post['label'];
        $name = $post['name'];
        $width = $post['width'];
        $type = $post['type'];
        $option = $post['option'];
        $validation = $post['validation'];

        $row = DB::table('cms_moduls')->where('id', $id)->first();

        $i = 0;
        $script_form = [];
        foreach ($label as $l) {

            if ($l != '') {

                $form = [];
                $form['label'] = $l;
                $form['name'] = $name[$i];
                $form['type'] = $type[$i];
                $form['validation'] = $validation[$i];
                $form['width'] = $width[$i];
                if ($option[$i]) {
                    $form = array_merge($form, $option[$i]);
                }

                foreach ($form as $k => $f) {
                    if ($f == '') {
                        unset($form[$k]);
                    }
                }

                $script_form[$i] = "\t\t\t".'$this->form[] = '.min_var_export($form).";";
            }

            $i++;
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

        return redirect(cb()->adminPath('modules/step4/'.$id));

    }

    public function getStep4($id)
    {
        $this->cbLoader();

        $module = cb()->getCurrentModule();

        if (! cb()->isView() && $this->global_privilege == false) {
            cb()->insertLog(trans('crudbooster.log_try_view', ['module' => $module->name]));
            cb()->redirect(cb()->adminPath(), trans('crudbooster.denied_access'));
        }

        $row = DB::table('cms_moduls')->where('id', $id)->first();

        $data = [];
        $data['id'] = $id;
        if (file_exists(app_path('Http/Controllers/'.$row->controller.'.php'))) {
            $response = file_get_contents(app_path('Http/Controllers/'.$row->controller.'.php'));
            $column_datas = extract_unit($response, "# START CONFIGURATION DO NOT REMOVE THIS LINE", "# END CONFIGURATION DO NOT REMOVE THIS LINE");
            $column_datas = str_replace('$this->', '$data[\'cb_', $column_datas);
            $column_datas = str_replace(' = ', '\'] = ', $column_datas);
            $column_datas = str_replace([' ', "\t"], '', $column_datas);
            eval($column_datas);
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

        return redirect_to(cb()->adminPath('modules'),trans('crudbooster.alert_update_data_success'),'success');
    }

    public function postEditSave($id)
    {
        $this->cbLoader();

        $row = DB::table($this->table)->where($this->primary_key, $id)->first();

        if (! cb()->isUpdate() && $this->global_privilege == false) {
            cb()->insertLog(trans("crudbooster.log_try_add", ['name' => $row->{$this->title_field}, 'module' => cb()->getCurrentModule()->name]));
            cb()->redirect(cb()->adminPath(), trans('crudbooster.denied_access'));
        }

        $this->validation();
        $this->input_assignment();

        //Generate Controller
        $route_basename = basename(Request::get('path'));
        if ($this->arr['controller'] == '') {
            $this->arr['controller'] = cb()->generateController(Request::get('table_name'), $route_basename);
        }

        DB::table($this->table)->where($this->primary_key, $id)->update($this->arr);

        //Refresh Session Roles
        $roles = DB::table('cms_privileges_roles')->where('id_cms_privileges', cb()->myPrivilegeId())->join('cms_moduls', 'cms_moduls.id', '=', 'id_cms_moduls')->select('cms_moduls.name', 'cms_moduls.path', 'is_visible', 'is_create', 'is_read', 'is_edit', 'is_delete')->get();
        Session::put('admin_privileges_roles', $roles);

        cb()->redirect(Request::server('HTTP_REFERER'), trans('crudbooster.alert_update_data_success'), 'success');
    }
}
