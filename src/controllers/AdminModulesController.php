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
    public $order_by = ['id'=>'desc'];

    public function __construct()
    {
        view()->share(['page_title'=>$this->module_name]);
    }

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

    private function listTable() {
        $tables = cb()->listTables();
        $table_list = [];
        foreach ($tables as $tab) {
            foreach ($tab as $key => $value) {
                $table_list[] = $value;
            }
        }
        return $table_list;
    }

    public function getStep2($id)
    {
        $row = DB::table('cms_moduls')->where('id', $id)->first();

        $data = [];
        $data['id'] = $id;
        $data['columns'] = cb()->getTableColumns($row->table_name);
        $data['table_list'] = $this->listTable();
        $data['controller_columns'] = json_decode($row->columns, true);


        return view('crudbooster::module_generator.step2', $data);
    }

    public function postStep2()
    {
        $name = request('name');
        $table_name = request('table');
        $icon = request('icon');
        $path = request('path');
        $id = request('id');
        $generate_type = \request('include_controller_doc')?"Advanced":"Simple";

        if (!request('id') && DB::table('cms_moduls')->where('path', $path)->where('deleted_at', null)->count()) {
            return redirect()->back()->with(['message' => 'Sorry the slug has already exists, please choose another !', 'type' => 'warning']);
        }

        $created_at = date_now();

        $row = cb()->first('cms_moduls', $id);

        // Generate a Module Controller
        $generate_controller = new ModuleControllerGenerator();
        $generate_controller->table = $table_name;
        $generate_controller->module_name = $name;
        $generate_controller->generate_type = \request('include_controller_doc')?"Advanced":"Simple";
        if($row) {
            $generate_controller->setProperties(json_decode($row->properties,true));
            $generate_controller->setColumns(json_decode($row->columns, true));
            $generate_controller->setForms(json_decode($row->forms, true));
        }
        $controller         = $generate_controller->generate();
        $columns            = json_encode($generate_controller->getColumns());
        $forms              = json_encode($generate_controller->getForms());
        $properties         = json_encode($generate_controller->getProperties());

        // Create new menu
        if ($controller && request('create_menu')) $this->createMenu($name, $icon, $path, \request('id_cms_privileges'));

        if(\request('id')) {

            // Update module data
            DB::table($this->table)->where("id", \request('id'))
                ->update(compact("controller", "name", "table_name", "icon", "path","columns","forms","generate_type","properties"));
        } else {

            // Create module data
            $id = DB::table($this->table)
                ->insertGetId(compact("controller", "name", "table_name", "icon", "path", "created_at","columns","forms","generate_type","properties"));
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
        $table_columns = [];
        foreach ($column as $col) {

            if (! $name[$i]) {
                $i++;
                continue;
            }

            $table_column = [];
            $table_column['label'] = $col;
            $table_column['name'] = $name[$i];

            if ($join_table[$i] && $join_field[$i]) {
                $table_column['join'] = $join_table[$i].','.$join_field[$i];
            }

            if ($is_image[$i]) {
                $table_column['image'] = true;
            }

            if ($id_download[$i]) {
                $table_column['download'] = true;
            }

            if ($width[$i]) {
                $table_column['width'] = $width[$i];
            }

            if ($callbackphp[$i]) {
                $table_column['callback_php'] = $callbackphp[$i];
            }

            $table_columns[] = $table_column;

            $i++;
        }

        DB::table($this->table)->where('id', $id)->update(['columns'=>json_encode($table_columns)]);

        // Generate a Module Controller
        $generate_controller = new ModuleControllerGenerator();
        $generate_controller->table = $row->table_name;
        $generate_controller->module_name = $row->name;
        $generate_controller->generate_type = $row->generate_type?:"Simple";
        $generate_controller->setColumns($table_columns);
        $generate_controller->setForms(json_decode($row->forms, true));
        $generate_controller->setProperties(json_decode($row->properties, true));
        $generate_controller->generate();

        return redirect(cb()->adminPath('modules/step3/'.$id));
    }

    private function listFormType() {
        $types = [];
        foreach (glob(base_path('vendor/crocodicstudio/crudbooster/src/views/default/type_components').'/*', GLOB_ONLYDIR) as $dir) {
            $types[] = basename($dir);
        }
        return $types;
    }

    public function getStep3($id)
    {

        $row = DB::table('cms_moduls')->where('id', $id)->first();

        $columns = cb()->getTableColumns($row->table_name);

        $types = $this->listFormType();

        $forms = json_decode($row->forms, true);

        return view('crudbooster::module_generator.step3', compact('columns', 'forms','types', 'id'));
    }

    public function getTypeInfo($type = 'text')
    {
        header("Content-Type: application/json");
        echo file_get_contents(base_path('vendor/crocodicstudio/crudbooster/src/views/default/type_components/'.$type.'/info.json'));
    }

    public function postStep4()
    {
        $post = \request()->all();
        $id = $post['id'];

        $label = $post['label'];
        $name = $post['name'];
        $width = $post['width'];
        $type = $post['type'];
        $option = $post['option'];
        $validation = $post['validation'];

        $row = DB::table('cms_moduls')->where('id', $id)->first();

        $i = 0;
        $forms = [];
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
                    $form[$k] = trim(trim($f, '"'));
                }

                $forms[] = $form;
            }

            $i++;
        }

        DB::table($this->table)->where("id", $id)->update(['forms'=>json_encode($forms)]);

        // Generate a Module Controller
        $generate_controller = new ModuleControllerGenerator();
        $generate_controller->table = $row->table_name;
        $generate_controller->module_name = $row->name;
        $generate_controller->generate_type = $row->generate_type?:"Simple";
        $generate_controller->setColumns(json_decode($row->columns, true));
        $generate_controller->setForms($forms);
        $generate_controller->setProperties(json_decode($row->properties, true));
        $generate_controller->generate();

        return redirect(cb()->adminPath('modules/step4/'.$id));

    }

    public function getStep4($id)
    {
        $row = DB::table('cms_moduls')->where('id', $id)->first();

        $data = [];
        $data['id'] = $id;
        $data['properties'] = json_decode($row->properties, true);

        return view('crudbooster::module_generator.step4', $data);
    }

    public function postStepFinish()
    {
        $this->cbLoader();
        $id = Request::input('id');
        $row = DB::table('cms_moduls')->where('id', $id)->first();

        $post = \request()->except(['_token','id','submit']);

        $post['table'] = $row->table_name;

        $script_config = [];
        $properties = json_decode($row->properties, true);
        $i = 0;
        foreach ($post as $key => $val) {
            if($val == 'true' || $val == 'false') {
                $value = ($val=='true')?true:false;
            } else {
                $value = $val;
            }

            $properties[$key] = $value;
            $i++;
        }

        DB::table($this->table)->where("id", $id)->update(['properties'=>json_encode($properties)]);

        // Generate a Module Controller
        $generate_controller = new ModuleControllerGenerator();
        $generate_controller->table = $row->table_name;
        $generate_controller->module_name = $row->name;
        $generate_controller->generate_type = $row->generate_type?:"Simple";
        $generate_controller->setColumns(json_decode($row->columns, true));
        $generate_controller->setForms(json_decode($row->forms, true));
        $generate_controller->setProperties($properties);
        $generate_controller->generate();

        return redirect_to(cb()->adminPath('modules'),trans('crudbooster.alert_update_data_success'),'success');
    }

    public function postEditSave($id)
    {

        DB::table($this->table)
            ->where($this->primary_key, $id)
            ->update([
                'name'=> \request('name'),
                'icon'=> \request('icon'),
                'path'=> \request('path')
            ]);

        //Refresh Session Roles
        cb()->auth()->refreshRole();

        cb()->redirect(Request::server('HTTP_REFERER'), trans('crudbooster.alert_update_data_success'), 'success');
    }
}
