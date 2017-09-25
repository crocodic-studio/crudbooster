<?php

namespace crocodicstudio\crudbooster\controllers;

error_reporting(E_ALL ^ E_NOTICE);

use crocodicstudio\crudbooster\controllers\Controller;
use crocodicstudio\crudbooster\controllers\Helpers\IndexExport;
use crocodicstudio\crudbooster\controllers\Helpers\IndexImport;
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
use Maatwebsite\Excel\Facades\Excel;
use CRUDBooster;
use CB;
use Schema;

class CBController extends Controller
{
    public $data_inputan;

    public $columns_table;

    public $module_name;

    public $table;

    public $title_field;

    public $primary_key = 'id';

    public $arr = [];

    public $col = [];

    public $form = [];

    public $data = [];

    public $addaction = [];

    public $orderby = null;

    public $password_candidate = null;

    public $date_candidate = null;

    public $limit = 20;

    public $global_privilege = false;

    public $show_numbering = false;

    public $alert = [];

    public $index_button = [];

    public $button_filter = true;

    public $button_export = true;

    public $button_import = true;

    public $button_show = true;

    public $button_addmore = true;

    public $button_table_action = true;

    public $button_bulk_action = true;

    public $button_add = true;

    public $button_delete = true;

    public $button_cancel = true;

    public $button_save = true;

    public $button_edit = true;

    public $button_detail = true;

    public $button_action_style = 'button_icon';

    public $button_action_width = null;

    public $index_statistic = [];

    public $index_additional_view = [];

    public $pre_index_html = null;

    public $post_index_html = null;

    public $load_js = [];

    public $load_css = [];

    public $script_js = null;

    public $style_css = null;

    public $sub_module = [];

    public $show_addaction = true;

    public $table_row_color = [];

    public $button_selected = [];

    public $return_url = null;

    public $parent_field = null;

    public $parent_id = null;

    public $hide_form = [];

    public $index_return = false; //for export

    public function cbLoader()
    {
        $this->cbInit();

        $this->checkHideForm();

        $this->primary_key = CB::pk($this->table);
        $this->columns_table = $this->col;
        $this->data_inputan = $this->form;
        $this->data['pk'] = $this->primary_key;
        $this->data['forms'] = $this->data_inputan;
        $this->data['hide_form'] = $this->hide_form;
        $this->data['addaction'] = ($this->show_addaction) ? $this->addaction : null;
        $this->data['table'] = $this->table;
        $this->data['title_field'] = $this->title_field;
        $this->data['appname'] = CRUDBooster::getSetting('appname');
        $this->data['alerts'] = $this->alert;
        $this->data['index_button'] = $this->index_button;
        $this->data['show_numbering'] = $this->show_numbering;
        $this->data['button_detail'] = $this->button_detail;
        $this->data['button_edit'] = $this->button_edit;
        $this->data['button_show'] = $this->button_show;
        $this->data['button_add'] = $this->button_add;
        $this->data['button_delete'] = $this->button_delete;
        $this->data['button_filter'] = $this->button_filter;
        $this->data['button_export'] = $this->button_export;
        $this->data['button_addmore'] = $this->button_addmore;
        $this->data['button_cancel'] = $this->button_cancel;
        $this->data['button_save'] = $this->button_save;
        $this->data['button_table_action'] = $this->button_table_action;
        $this->data['button_bulk_action'] = $this->button_bulk_action;
        $this->data['button_import'] = $this->button_import;
        $this->data['button_action_width'] = $this->button_action_width;
        $this->data['button_selected'] = $this->button_selected;
        $this->data['index_statistic'] = $this->index_statistic;
        $this->data['index_additional_view'] = $this->index_additional_view;
        $this->data['table_row_color'] = $this->table_row_color;
        $this->data['pre_index_html'] = $this->pre_index_html;
        $this->data['post_index_html'] = $this->post_index_html;
        $this->data['load_js'] = $this->load_js;
        $this->data['load_css'] = $this->load_css;
        $this->data['script_js'] = $this->script_js;
        $this->data['style_css'] = $this->style_css;
        $this->data['sub_module'] = $this->sub_module;
        $this->data['parent_field'] = (g('parent_field')) ?: $this->parent_field;
        $this->data['parent_id'] = (g('parent_id')) ?: $this->parent_id;

        if (CRUDBooster::getCurrentMethod() == 'getProfile') {
            Session::put('current_row_id', CRUDBooster::myId());
            $this->data['return_url'] = Request::fullUrl();
        }

        view()->share($this->data);
    }

    public function cbView($template, $data)
    {
        $this->cbLoader();
        echo view($template, $data);
    }

    private function checkHideForm()
    {
        if (! count($this->hide_form)) {
            return null;
        }
        foreach ($this->form as $i => $f) {
            if (in_array($f['name'], $this->hide_form)) {
                unset($this->form[$i]);
            }
        }
    }

    public function getIndex()
    {
        $this->cbLoader();
        $data = [];
        if (request('parent_table')) {
            $data = $this->_handleParentTable();
        }

        $data['table'] = $this->table;
        $data['table_pk'] = CB::pk($this->table);
        $data['page_title'] = $module->name;
        $data['page_description'] = trans('crudbooster.default_module_description');
        $data['date_candidate'] = $this->date_candidate;
        $data['limit'] = $limit = request('limit', $this->limit);

        $tablePK = $data['table_pk'];
        $table_columns = CB::getTableColumns($this->table);

        $result = $this->table()->select(DB::raw($this->table.".".$this->primary_key));

        if (request('parent_id')) {
            $this->filterForParent($result);
        }

        $this->hookQueryIndex($result);

        $this->filterSoftDeleted($table_columns, $result);

        $alias = [];
        $join_alias_count = 0;
        $join_table_temp = [];
        $table = $this->table;
        $columns_table = $this->columns_table;
        foreach ($columns_table as $index => $coltab) {

            $join = @$coltab['join'];
            $join_where = @$coltab['join_where'];
            $join_id = @$coltab['join_id'];
            $field = @$coltab['name'];

            if (strpos($field, '.')) {
                $columns_table = $this->addDotField($result, $field, $columns_table, $index);
            } else {
                $columns_table = $this->addField($columns_table, $index, $field, $table, $result);
            }
        }

        $this->_applyWhereAndQfilters($result, $columns_table, $table);

        $filter_is_orderby = false;
        if (request('filter_column')) {

            $filter_is_orderby = $this->_filterIndexRows($result);
        }

        $data = $this->_prepareResults($filter_is_orderby, $result, $limit, $data, $table);

        $data['columns'] = $columns_table;

        if ($this->index_return) {
            return $data;
        }

        //LISTING INDEX HTML
        $addaction = $this->data['addaction'];

        if ($this->sub_module) {
            $addaction = $this->_handleSubModules($addaction);
        }

        $mainpath = CRUDBooster::mainpath();
        $orig_mainpath = $this->data['mainpath'];
        $title_field = $this->title_field;
        $html_contents = [];
        $page = request('page', 1);
        $number = ($page - 1) * $limit + 1;
        foreach ($data['result'] as $row) {
            $html_content = [];

            if ($this->button_bulk_action) {

                $html_content[] = "<input type='checkbox' class='checkbox' name='checkbox[]' value='".$row->{$tablePK}."'/>";
            }

            if ($this->show_numbering) {
                $html_content[] = $number.'. ';
                $number++;
            }

            foreach ($columns_table as $col) {
                if (! $col['visible']) {
                    continue;
                }

                $value = $this->_calculateColumnValue($col, $row, $table);

                $html_content[] = $value;
            } //end foreach columns_table

            if ($this->button_table_action) {
                $button_action_style = $this->button_action_style;
                $html_content[] = "<div class='button_action' style='text-align:right'>".view('crudbooster::components.action', compact('addaction', 'row', 'button_action_style', 'parent_field'))->render()."</div>";
            }

            foreach ($html_content as $i => $v) {
                $this->hookRowIndex($i, $v);
                $html_content[$i] = $v;
            }

            $html_contents[] = $html_content;
        } //end foreach data[result]

        $data['html_contents'] = ['html' => $html_contents, 'data' => $data['result']];

        return view("crudbooster::default.index", $data);
    }

    public function getExportData()
    {

        return redirect(CRUDBooster::mainpath());
    }

    public function postExportData(IndexExport $exporter)
    {
        $this->limit = Request::input('limit');
        $this->index_return = true;
        $filename = Request::input('filename');
        $papersize = Request::input('page_size');
        $paperorientation = Request::input('page_orientation');
        $response = $this->getIndex();

        if (Request::input('default_paper_size')) {
            DB::table('cms_settings')->where('name', 'default_paper_size')->update(['content' => $papersize]);
        }

        switch (Request::input('fileformat')) {
            case "pdf":
                return $exporter->pdf($response, $papersize, $paperorientation, $filename);
                break;
            case 'xls':
                return $exporter->xls($filename, $response, $paperorientation);
                break;
            case 'csv':
                return $exporter->csv($filename, $response, $paperorientation);
                break;
        }
    }

    public function getDataQuery()
    {
        $key = g('query');
        if (! Cache::has($key)) {
            return response()->json(['items' => []]);
        }
        $query = Cache::get($key);
        $fk_name = g('fk_name');
        $fk_value = g('fk_value');

        if (strpos(strtolower($query), 'where') !== false) {
            $condition = " and ";
        }else{
            $condition = " where ";
        }

        if (strpos(strtolower($query), 'order by')) {
            $query = str_replace("ORDER BY", "order by", $query);
            $qraw = explode('order by', $query);
            $query = $qraw[0].$condition.$fk_name." = '".$fk_value."' ".$qraw[1];
        } else {
            $query .= $condition.$fk_name." = '".$fk_value."'";
        }

        $query = DB::select(DB::raw($query));

        return response()->json(['items' => $query]);
    }

    public function getDataTable()
    {
        $table = request('table');
        $label = request('label');
        $datatableWhere = urldecode(request('datatable_where'));
        $foreign_key_name = request('fk_name');
        $foreign_key_value = request('fk_value');
        if (! $table || ! $label || ! $foreign_key_name || ! $foreign_key_value) {
            return response()->json([]);
        }
        $query = DB::table($table);
        if ($datatableWhere) {
            $query->whereRaw($datatableWhere);
        }
        $query->select('id as select_value', $label.' as select_label');
        $query->where($foreign_key_name, $foreign_key_value);
        $query->orderby($label, 'asc');

        return response()->json($query->get());
    }

    public function getDataModalDatatable()
    {
        $data = request('data');
        $data = base64_decode(json_decode($data, true));

        $columns = $data['columns'];
        $columns = explode(',', $columns);

        $result = DB::table($data['table']);
        if (request('q')) {
            $result->where(function ($where) use ($columns) {
                foreach ($columns as $c => $col) {
                    if ($c == 0) {
                        $where->where($col, 'like', '%'.request('q').'%');
                    } else {
                        $where->orWhere($col, 'like', '%'.request('q').'%');
                    }
                }
            });
        }

        if ($data['sql_where']) {
            $result->whereraw($data['sql_where']);
        }

        if ($data['sql_orderby']) {
            $result->orderByRaw($data['sql_orderby']);
        } else {
            $result->orderBy($data['column_value'], 'desc');
        }
        $limit = ($data['limit']) ?: 6;

        return view('crudbooster::default.type_components.datamodal.browser', ['result' => $result->paginate($limit), 'data' => $data]);
    }

    public function getUpdateSingle()
    {
        $table = request('table');
        $column = request('column');
        $value = request('value');
        $id = request('id');
        $tablePK = CB::pk($table);
        DB::table($table)->where($tablePK, $id)->update([$column => $value]);

        return CRUDBooster::backWithMsg(trans('crudbooster.alert_delete_data_success'));
    }

    public function postFindData()
    {
        $q = request('q');
        $data = request('data');
        $data = base64_decode($data);
        $data = json_decode($data, true);
        $id = request('id');

        $fieldValue = $data['field_value'];

        $table = $data['table'];
        $rows = DB::table($table);
        $rows->select($table.'.*');

        if ($data['sql_orderby']) {
            $rows->orderbyRaw($data['sql_orderby']);
        } else {
            $rows->orderby($fieldValue, 'desc');
        }
        if ($data['limit']) {
            $rows->take($data['limit']);
        } else {
            $rows->take(10);
        }

        if ($data['field_label']) {
            $rows->addselect($data['field_label'].' as text');
        }

        if ($data['field_value']) {
            $rows->addselect($data['field_value'].' as id');
        }

        if ($data['sql_where']) {
            $rows->whereRaw($data['sql_where']);
        }

        if ($q) {
            $rows->where($data['field_label'], 'like', '%'.$q.'%');
        }

        if ($id) {
            $rows->where($data['field_value'], $id);
        }

        $result = $rows->get();

        return response()->json(['items' => $result]);
    }

    public function postFindDataOld()
    {
        $q = request('q');
        $id = request('id');
        $format = request('format');

        $table1 = request('table1', $this->table);
        $table1PK = CB::pk($table1);
        $column1 = request('column1', $this->title_field);

        $table2 = request('table2');
        $column2 = request('column2');

        $table3 = request('table3');
        $column3 = request('column3');

        $where = request('where');

        $fk = request('fk');
        $fk_value = request('fk_value');

        if (!$q && !$id && !$table1) {
            $result = [];
            $result['items'] = [];
            return response()->json($result);
        }

        $rows = DB::table($table1);
        $rows->select($table1.'.*');
        $rows->take(request('limit', 10));

        if (Schema::hasColumn($table1, 'deleted_at')) {
            $rows->where($table1.'.deleted_at', null);
        }

        if ($fk && $fk_value) {
            $rows->where($table1.'.'.$fk, $fk_value);
        }

        if ($table1 && $column1) {

            $orderby_table = $table1;
            $orderby_column = $column1;
        }

        if ($table2 && $column2) {
            $table2PK = CB::pk($table2);
            $rows->join($table2, $table2.'.'.$table2PK, '=', $table1.'.'.$column1);
            $columns = CRUDBooster::getTableColumns($table2);
            foreach ($columns as $col) {
                $rows->addselect($table2.".".$col." as ".$table2."_".$col);
            }
            $orderby_table = $table2;
            $orderby_column = $column2;
        }

        if ($table3 && $column3) {
            $table3PK = CB::pk($table3);
            $rows->join($table3, $table3.'.'.$table3PK, '=', $table2.'.'.$column2);
            $columns = CRUDBooster::getTableColumns($table3);
            foreach ($columns as $col) {
                $rows->addselect($table3.".".$col." as ".$table3."_".$col);
            }
            $orderby_table = $table3;
            $orderby_column = $column3;
        }

        if ($id) {
            $rows->where($table1.".".$table1PK, $id);
        }

        if ($where) {
            $rows->whereraw($where);
        }

        if ($format) {
            $format = str_replace('&#039;', "'", $format);
            $rows->addselect(DB::raw("CONCAT($format) as text"));
            if ($q) {
                $rows->whereraw("CONCAT($format) like '%".$q."%'");
            }
        } else {
            $rows->addselect($orderby_table.'.'.$orderby_column.' as text');
            if ($q) {
                $rows->where($orderby_table.'.'.$orderby_column, 'like', '%'.$q.'%');
            }
        }

        $result = [];
        $result['items'] = $rows->get();


        return response()->json($result);
    }

    public function validation($id = null)
    {
        $request_all = Request::all();
        $array_input = [];
        $componentPath = implode(DIRECTORY_SEPARATOR, ["vendor", "crocodicstudio", "crudbooster", "src", "views", "default", "type_components", ""]);

        foreach ($this->data_inputan as $di) {
            $ai = [];
            $name = $di['name'];
            $type = $di['type'];

            if (!$name || !isset($request_all[$name])) {
                continue;
            }

            if ($di['required'] && ! Request::hasFile($name)) {
                $ai[] = 'required';
            }

            include_once base_path($componentPath.$type.DIRECTORY_SEPARATOR.'hookInputValidation.php');

            if (@$di['validation']) {
                $array_input[$name] = $this->prepareValidationRules($id, $di);
            } else {
                $array_input[$name] = implode('|', $ai);
            }
        }

        $validator = Validator::make($request_all, $array_input);

        if (!$validator->fails()) {
            return null;
        }

        $message = $validator->messages();
        $message_all = $message->all();

        if (Request::ajax()) {
            response()->json([
                'message' => trans('crudbooster.alert_validation_error', ['error' => implode(', ', $message_all)]),
                'message_type' => 'warning',
            ])->send();
            exit;
        }

        redirect()->back()->with("errors", $message)->with([
            'message' => trans('crudbooster.alert_validation_error', ['error' => implode(', ', $message_all)]),
            'message_type' => 'warning',
        ])->withInput()->send();
        \Session::driver()->save();
        exit;
    }

    public function inputAssignment($id = null)
    {

        $hide_form = (request('hide_form')) ? unserialize(request('hide_form')) : [];
        $componentPath = implode(DIRECTORY_SEPARATOR, ["vendor", "crocodicstudio", "crudbooster", "src", "views", "default", 'type_components', '']);

        foreach ($this->data_inputan as $ro) {
            $name = $ro['name'];
            $type = $ro['type'] ?: 'text';
            $inputdata = request($name);

            if (! $name) {
                continue;
            }
            if ($ro['exception']) {
                continue;
            }

            if (count($hide_form) && in_array($name, $hide_form)) {
                continue;
            }

            if (file_exists(base_path($componentPath.$type.DIRECTORY_SEPARATOR.'hookInputAssignment.php'))) {
                require_once(base_path($componentPath.$type.DIRECTORY_SEPARATOR.'hookInputAssignment.php'));
            }

            if (Request::hasFile($name)) {
                continue;
            }
            if ($inputdata != '') {
                $this->arr[$name] = $inputdata;
            } else {
                if (CB::isColumnNULL($this->table, $name)) {
                    continue;
                }

                $this->arr[$name] = "";
            }
        }
    }

    public function getAdd()
    {
        $this->cbLoader();

        $page_title = trans("crudbooster.add_data_page_title", ['module' => CRUDBooster::getCurrentModule()->name]);
        $page_menu = Route::getCurrentRoute()->getActionName();
        $command = 'add';

        return view('crudbooster::default.form', compact('page_title', 'page_menu', 'command'));
    }

    public function postAddSave()
    {
        $this->cbLoader();

        $this->validation();
        $this->inputAssignment();

        if (Schema::hasColumn($this->table, 'created_at')) {
            $this->arr['created_at'] = date('Y-m-d H:i:s');
        }

        $this->hookBeforeAdd($this->arr);

        $this->arr[$this->primary_key] = $id = CRUDBooster::newId($this->table);
        $this->table()->insert($this->arr);

        //Looping Data Input Again After Insert
        foreach ($this->data_inputan as $ro) {
            $name = $ro['name'];
            if (! $name) {
                continue;
            }

            $inputdata = request($name);

            //Insert Data Checkbox if Type Datatable
            if ($ro['type'] == 'checkbox' && $ro['relationship_table']) {
                $this->_handleCheckbox($ro, $id, $inputdata);
            }

            if ($ro['type'] == 'select2' && $ro['relationship_table']) {
                $this->_updateRelations($ro, $id, $inputdata);
            }

            if ($ro['type'] == 'child') {
                $this->_updateChildTable($ro, $id);
            }
        }

        $this->hookAfterAdd($this->arr[$this->primary_key]);

        $this->return_url = ($this->return_url) ? $this->return_url : request('return_url');

        //insert log
        CRUDBooster::insertLog(trans("crudbooster.log_add", ['name' => $this->arr[$this->title_field], 'module' => CRUDBooster::getCurrentModule()->name]));

        if ($this->return_url) {
            if (request('submit') == trans('crudbooster.button_save_more')) {
                CRUDBooster::redirect(Request::server('HTTP_REFERER'), trans("crudbooster.alert_add_data_success"), 'success');
            }
            CRUDBooster::redirect($this->return_url, trans("crudbooster.alert_add_data_success"), 'success');
        }
        if (request('submit') == trans('crudbooster.button_save_more')) {
            CRUDBooster::redirect(CRUDBooster::mainpath('add'), trans("crudbooster.alert_add_data_success"), 'success');
        }
        CRUDBooster::redirect(CRUDBooster::mainpath(), trans("crudbooster.alert_add_data_success"), 'success');
    }

    public function getEdit($id)
    {
        $this->cbLoader();
        $row = $this->findRow($id)->first();

        $page_menu = Route::getCurrentRoute()->getActionName();
        $page_title = trans("crudbooster.edit_data_page_title", ['module' => CRUDBooster::getCurrentModule()->name, 'name' => $row->{$this->title_field}]);
        $command = 'edit';
        Session::put('current_row_id', $id);

        return view('crudbooster::default.form', compact('id', 'row', 'page_menu', 'page_title', 'command'));
    }

    public function postEditSave($id)
    {
        $this->cbLoader();
        $row = $this->findRow($id)->first();

        $this->validation($id);
        $this->inputAssignment($id);

        if (Schema::hasColumn($this->table, 'updated_at')) {
            $this->arr['updated_at'] = date('Y-m-d H:i:s');
        }

        $this->hookBeforeEdit($this->arr, $id);
        $this->findRow($id)->update($this->arr);

        //Looping Data Input Again After Insert
        foreach ($this->data_inputan as $ro) {
            $name = $ro['name'];
            if (! $name) {
                continue;
            }

            $inputdata = request($name);

            //Insert Data Checkbox if Type Datatable
            if ($ro['type'] == 'checkbox' && $ro['relationship_table']) {
                $datatable = explode(",", $ro['datatable'])[0];

                $foreignKey2 = CRUDBooster::getForeignKey($datatable, $ro['relationship_table']);
                $foreignKey = CRUDBooster::getForeignKey($this->table, $ro['relationship_table']);
                DB::table($ro['relationship_table'])->where($foreignKey, $id)->delete();

                if ($inputdata) {
                    foreach ($inputdata as $input_id) {
                        $relationship_table_pk = CB::pk($ro['relationship_table']);
                        DB::table($ro['relationship_table'])->insert([
                            $relationship_table_pk => CRUDBooster::newId($ro['relationship_table']),
                            $foreignKey => $id,
                            $foreignKey2 => $input_id,
                        ]);
                    }
                }
            }

            if ($ro['type'] == 'select2' && $ro['relationship_table']) {
                $datatable = explode(",", $ro['datatable'])[0];

                $foreignKey2 = CRUDBooster::getForeignKey($datatable, $ro['relationship_table']);
                $foreignKey = CRUDBooster::getForeignKey($this->table, $ro['relationship_table']);
                DB::table($ro['relationship_table'])->where($foreignKey, $id)->delete();

                if ($inputdata) {
                    foreach ($inputdata as $input_id) {
                        $relationship_table_pk = CB::pk($ro['relationship_table']);
                        DB::table($ro['relationship_table'])->insert([
                            $relationship_table_pk => CRUDBooster::newId($ro['relationship_table']),
                            $foreignKey => $id,
                            $foreignKey2 => $input_id,
                        ]);
                    }
                }
            }

            if ($ro['type'] == 'child') {
                $name = str_slug($ro['label'], '');
                $columns = $ro['columns'];
                $count_input_data = count(request($name.'-'.$columns[0]['name'])) - 1;
                $child_array = [];
                $childtable = CRUDBooster::parseSqlTable($ro['table'])['table'];
                $fk = $ro['foreign_key'];

                DB::table($childtable)->where($fk, $id)->delete();
                $lastId = CRUDBooster::newId($childtable);
                $childtablePK = CB::pk($childtable);

                for ($i = 0; $i <= $count_input_data; $i++) {

                    $column_data = [];
                    $column_data[$childtablePK] = $lastId;
                    $column_data[$fk] = $id;
                    foreach ($columns as $col) {
                        $colname = $col['name'];
                        $column_data[$colname] = request($name.'-'.$colname)[$i];
                    }
                    $child_array[] = $column_data;

                    $lastId++;
                }

                $child_array = array_reverse($child_array);

                DB::table($childtable)->insert($child_array);
            }
        }

        $this->hookAfterEdit($id);

        $this->return_url = ($this->return_url) ? $this->return_url : request('return_url');

        //insert log
        CRUDBooster::insertLog(trans("crudbooster.log_update", ['name' => $this->arr[$this->title_field], 'module' => CRUDBooster::getCurrentModule()->name]));

        if ($this->return_url) {
            CRUDBooster::redirect($this->return_url, trans("crudbooster.alert_update_data_success"), 'success');
        }

        if (request('submit') == trans('crudbooster.button_save_more')) {
            CRUDBooster::redirect(CRUDBooster::mainpath('add'), trans("crudbooster.alert_update_data_success"), 'success');
        }

        CRUDBooster::redirect(CRUDBooster::mainpath(), trans("crudbooster.alert_update_data_success"), 'success');
    }

    public function getDelete($id)
    {
        $this->cbLoader();
        $row = $this->findRow($id)->first();

        //insert log
        CRUDBooster::insertLog(trans("crudbooster.log_delete", ['name' => $row->{$this->title_field}, 'module' => CRUDBooster::getCurrentModule()->name]));

        $this->hookBeforeDelete($id);

        if (Schema::hasColumn($this->table, 'deleted_at')) {
            $this->findRow($id)->update(['deleted_at' => date('Y-m-d H:i:s')]);
        } else {
            $this->findRow($id)->delete();
        }

        $this->hookAfterDelete($id);

        $url = g('return_url') ?: CRUDBooster::referer();

        CRUDBooster::redirect($url, trans("crudbooster.alert_delete_data_success"), 'success');
    }

    public function getDetail($id)
    {
        $this->cbLoader();
        $row = $this->findRow($id)->first();

        $module = CRUDBooster::getCurrentModule();

        $page_menu = Route::getCurrentRoute()->getActionName();
        $page_title = trans("crudbooster.detail_data_page_title", ['module' => $module->name, 'name' => $row->{$this->title_field}]);
        $command = 'detail';

        Session::put('current_row_id', $id);

        return view('crudbooster::default.form', compact('row', 'page_menu', 'page_title', 'command', 'id'));
    }

    public function getImportData()
    {
        $this->cbLoader();
        $data['page_menu'] = Route::getCurrentRoute()->getActionName();
        $data['page_title'] = 'Import Data '.CRUDBooster::getCurrentModule()->name;

        if (! request('file') || request('import')) {
            return view('crudbooster::import', $data);
        }

        $file = base64_decode(request('file'));
        $file = 'storage'.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.$file;
        $rows = Excel::load($file, function ($reader) {
        })->get();

        Session::put('total_data_import', count($rows));

        $data_import_column = [];
        foreach ($rows as $value) {
            $a = [];
            foreach ($value as $k => $v) {
                $a[] = $k;
            }
            if (count($a)) {
                $data_import_column = $a;
            }
            break;
        }

        $table_columns = DB::getSchemaBuilder()->getColumnListing($this->table);

        $data['table_columns'] = $table_columns;
        $data['data_import_column'] = $data_import_column;

        return view('crudbooster::import', $data);
    }

    public function postDoneImport(IndexImport $importer)
    {
        $this->cbLoader();
        return  $importer->doneImport($data);
    }

    public function postDoImportChunk(IndexImport $import)
    {
        $this->cbLoader();
        $file_md5 = md5(request('file'));

        if (request('file') && request('resume') == 1) {
            return $import->handleImportProgress($file_md5);
        }

        $select_column = Session::get('select_column');
        $select_column = array_filter($select_column);
        $table_columns = DB::getSchemaBuilder()->getColumnListing($this->table);

        $file = base64_decode(request('file'));
        $file = 'storage'.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.$file;
        $rows = Excel::load($file, function ($reader) {
        })->get();

        $has_created_at = false;
        if (CRUDBooster::isColumnExists($this->table, 'created_at')) {
            $has_created_at = true;
        }

        $data_import_column = [];
        foreach ($rows as $value) {
            $a = [];
            foreach ($select_column as $sk => $s) {
                $colname = $table_columns[$sk];

                if (CRUDBooster::isForeignKey($colname)) {

                    //Skip if value is empty
                    if ($value->$s == '') {
                        continue;
                    }

                    if (intval($value->$s)) {
                        $a[$colname] = $value->$s;
                    } else {
                        $relation_table = CRUDBooster::getTableForeignKey($colname);
                        $relation_moduls = DB::table('cms_moduls')->where('table_name', $relation_table)->first();

                        $relation_class = __NAMESPACE__.'\\'.$relation_moduls->controller;
                        if (! class_exists($relation_class)) {
                            $relation_class = '\App\Http\Controllers\\'.$relation_moduls->controller;
                        }
                        $relation_class = new $relation_class;
                        $relation_class->cbLoader();

                        $title_field = $relation_class->title_field;

                        $relation_insert_data = [];
                        $relation_insert_data[$title_field] = $value->$s;

                        if (CRUDBooster::isColumnExists($relation_table, 'created_at')) {
                            $relation_insert_data['created_at'] = date('Y-m-d H:i:s');
                        }

                        try {
                            $relation_exists = DB::table($relation_table)->where($title_field, $value->$s)->first();
                            if ($relation_exists) {
                                $relation_primary_key = $relation_class->primary_key;
                                $relation_id = $relation_exists->$relation_primary_key;
                            } else {
                                $relation_id = DB::table($relation_table)->insertGetId($relation_insert_data);
                            }

                            $a[$colname] = $relation_id;
                        } catch (\Exception $e) {
                            exit($e);
                        }
                    } //END IS INT

                } else {
                    $a[$colname] = $value->$s;
                }
            }

            $has_title_field = true;
            foreach ($a as $k => $v) {
                if ($k == $this->title_field && $v == '') {
                    $has_title_field = false;
                    break;
                }
            }

            if ($has_title_field == false) {
                continue;
            }

            try {
                if ($has_created_at) {
                    $a['created_at'] = date('Y-m-d H:i:s');
                }

                $this->table()->insert($a);
                Cache::increment('success_'.$file_md5);
            } catch (\Exception $e) {
                $e = (string) $e;
                Cache::put('error_'.$file_md5, $e, 500);
            }
        }

        return response()->json(['status' => true]);
    }

    public function postDoUploadImportData(IndexImport $importer)
    {
        $this->cbLoader();
        if (! Request::hasFile('userfile')) {
            return redirect()->back();
        }
        $file = Request::file('userfile');
        $validator = $importer->validateForImport($file);
        if ($validator->fails()) {
            return CRUDBooster::backWithMsg(implode('<br/>', $validator->errors()->all()), 'warning');
        }
        $url = $importer->uploadImportData($file);

        return redirect($url);
    }

    public function postActionSelected()
    {
        $this->cbLoader();
        $id_selected = Request::input('checkbox');
        $button_name = Request::input('button_name');

        if (! $id_selected) {
            CRUDBooster::redirect($_SERVER['HTTP_REFERER'], 'Please select at least one data!', 'warning');
        }

        if ($button_name == 'delete') {
            return $this->deleteFromDB($id_selected);
        }

        list($type, $message) = $this->getMessageAndType($button_name, $id_selected);

        return CRUDBooster::backWithMsg($message, $type);
    }

    public function getDeleteImage()
    {
        $this->cbLoader();
        $id = request('id');
        $column = request('column');

        $row = $this->findRow($id)->first();
        $file = $row->{$column};

        Storage::delete($file);

        $this->findRow($id)->update([$column => null]);

        CRUDBooster::insertLog(trans("crudbooster.log_delete_image", [
            'name' => $row->{$this->title_field},
            'module' => CRUDBooster::getCurrentModule()->name,
        ]));

        CRUDBooster::redirect(Request::server('HTTP_REFERER'), trans('crudbooster.alert_delete_data_success'), 'success');
    }

    public function postUploadSummernote()
    {
        $this->cbLoader();
        $name = 'userfile';
        $uploadTypes = explode(',', cbConfig('UPLOAD_TYPES'));
        $uploadMaxSize = cbConfig('UPLOAD_MAX_SIZE') ?: 5000;

        if (! Request::hasFile($name)) {
            return null;
        }

        $file = Request::file($name);
        $ext = $file->getClientOriginalExtension();
        $filesize = $file->getClientSize() / 1024;

        if ($filesize > $uploadMaxSize) {
            echo "The filesize is too large!";
            exit;
        }

        if (! in_array($ext, $uploadTypes)) {
            echo "The filetype is not allowed!";
            exit;
        }

        //Create Directory Monthly
        Storage::makeDirectory(date('Y-m'));
        //Move file to storage
        $filename = md5(str_random(5)).'.'.$ext;
        $file_path = 'uploads'.DIRECTORY_SEPARATOR.date('Y-m');
        Storage::putFileAs($file_path, $file, $filename);
        echo asset('uploads/'.date('Y-m').'/'.$filename);
    }

    public function postUploadFile()
    {
        $this->cbLoader();
        $name = 'userfile';
        if (! Request::hasFile($name)) {
            return null;
        }
        $file = Request::file($name);
        $ext = $file->getClientOriginalExtension();
        $filesize = $file->getClientSize() / 1024;
        $uploadMaxSize = cbConfig('UPLOAD_MAX_SIZE', 5000);

        if ($filesize > $uploadMaxSize) {
            echo "The filesize is too large!";
            exit;
        }
        $uploadTypes = explode(',', cbConfig('UPLOAD_TYPES'));

        if (! in_array($ext, $uploadTypes)) {
            echo "The filetype is not allowed!";
            exit;
        }
        //Create Directory Monthly
        Storage::makeDirectory(date('Y-m'));

        //Move file to storage
        $filename = md5(str_random(5)).'.'.$ext;
        $file_path = 'uploads'.DIRECTORY_SEPARATOR.date('Y-m');
        Storage::putFileAs($file_path, $file, $filename);
        echo 'uploads/'.date('Y-m').'/'.$filename;
    }

    public function actionButtonSelected($id_selected, $button_name)
    {
    }

    public function hookQueryIndex(&$query)
    {
    }

    public function hookRowIndex($index, &$value)
    {
    }

    public function hookBeforeAdd(&$arr)
    {
    }

    public function hookAfterAdd($id)
    {
    }

    public function hookBeforeEdit(&$arr, $id)
    {
    }

    public function hookAfterEdit($id)
    {
    }

    public function hookBeforeDelete($id)
    {
    }

    public function hookAfterDelete($id)
    {
    }

    /**
     * @param $row
     * @param $id
     * @param $inputData
     * @param $row
     * @return array
     */
    private function _updateRelations($row, $id, $inputData)
    {
        $dataTable = explode(",", $row['datatable'])[0];
        $foreignKey2 = CRUDBooster::getForeignKey($dataTable, $row['relationship_table']);
        $foreignKey = CRUDBooster::getForeignKey($this->table, $row['relationship_table']);
        DB::table($row['relationship_table'])->where($foreignKey, $id)->delete();

        if ($inputData) {
            foreach ($inputData as $input_id) {
                $relationship_table_pk = CB::pk($row['relationship_table']);
                DB::table($row['relationship_table'])->insert([
                    $relationship_table_pk => CRUDBooster::newId($row['relationship_table']),
                    $foreignKey => $id,
                    $foreignKey2 => $input_id,
                ]);
            }
        }
    }

    /**
     * @param $ro
     * @param $id
     * @return string
     */
    private function _updateChildTable($ro, $id)
    {
        $name = str_slug($ro['label'], '');
        $columns = $ro['columns'];
        $count_input_data = count(request($name.'-'.$columns[0]['name'])) - 1;
        $child_array = [];

        for ($i = 0; $i <= $count_input_data; $i++) {
            $fk = $ro['foreign_key'];
            $column_data = [];
            $column_data[$fk] = $id;
            foreach ($columns as $col) {
                $colName = $col['name'];
                $column_data[$colName] = request($name.'-'.$colName)[$i];
            }
            $child_array[] = $column_data;
        }

        $childTable = CRUDBooster::parseSqlTable($ro['table'])['table'];
        DB::table($childTable)->insert($child_array);

        return $name;
    }

    /**
     * @param $ro
     * @param $id
     * @param $inputdata
     */
    private function _handleCheckbox($ro, $id, $inputdata)
    {
        $datatable = explode(",", $ro['datatable'])[0];
        $foreignKey2 = CRUDBooster::getForeignKey($datatable, $ro['relationship_table']);
        $foreignKey = CRUDBooster::getForeignKey($this->table, $ro['relationship_table']);
        DB::table($ro['relationship_table'])->where($foreignKey, $id)->delete();

        if ($inputdata) {
            $relationship_table_pk = CB::pk($ro['relationship_table']);
            foreach ($inputdata as $input_id) {
                DB::table($ro['relationship_table'])->insert([
                    $relationship_table_pk => CRUDBooster::newId($ro['relationship_table']),
                    $foreignKey => $id,
                    $foreignKey2 => $input_id,
                ]);
            }
        }
    }

    /**
     * @param $filter_is_orderby
     * @param $result
     * @param $limit
     * @param $data
     * @param $table
     * @return array
     */
    private function _prepareResults($filter_is_orderby, $result, $limit, $data, $table)
    {
        if ($filter_is_orderby !== true) {
            $data['result'] = $result->paginate($limit);

            return $data;
        }

        if (! $this->orderby) {
            $data['result'] = $result->orderby($this->table.'.'.$this->primary_key, 'desc')->paginate($limit);

            return $data;
        }

        if (is_array($this->orderby)) {
            foreach ($this->orderby as $k => $v) {
                if (strpos($k, '.') !== false) {
                    $orderby_table = explode(".", $k)[0];
                } else {
                    $orderby_table = $table;
                }
                $result->orderby($orderby_table.'.'.$k, $v);
            }
            $data['result'] = $result->paginate($limit);

            return $data;
        }

        $this->orderby = explode(";", $this->orderby);
        foreach ($this->orderby as $o) {
            $o = explode(",", $o);
            $k = $o[0];
            $v = $o[1];
            if (strpos($k, '.') !== false) {
                $orderby_table = explode(".", $k)[0];
            } else {
                $orderby_table = $table;
            }
            $result->orderby($orderby_table.'.'.$k, $v);
        }

        $data['result'] = $result->paginate($limit);

        return $data;
    }

    /**
     * @param $result
     * @return array
     */
    private function _filterIndexRows($result)
    {
        $filter_is_orderby = false;
        $filter_column = request('filter_column');
        $result->where(function ($query) use ($filter_column) {
            foreach ($filter_column as $key => $fc) {

                $value = @$fc['value'];
                $type = @$fc['type'];

                if ($type == 'empty') {
                    $query->whereNull($key)->orWhere($key, '');
                    continue;
                }

                if ($value == '' || $type == '') {
                    continue;
                }

                if ($type == 'between') {
                    continue;
                }

                switch ($type) {
                    default:
                        if ($key && $type && $value) {
                            $query->where($key, $type, $value);
                        }
                        break;
                    case 'like':
                    case 'not like':
                        $value = '%'.$value.'%';
                        if ($key && $type && $value) {
                            $query->where($key, $type, $value);
                        }
                        break;
                    case 'in':
                    case 'not in':
                        if ($value) {
                            $value = explode(',', $value);
                            if ($key && $value) {
                                $query->whereIn($key, $value);
                            }
                        }
                        break;
                }
            }
        });

        foreach ($filter_column as $key => $fc) {
            $value = @$fc['value'];
            $type = @$fc['type'];
            $sorting = @$fc['sorting'];

            if ($sorting != '' && $key) {
                $result->orderby($key, $sorting);
                $filter_is_orderby = true;
            }

            if ($type == 'between') {
                if ($key && $value) {
                    $result->whereBetween($key, $value);
                }
            }
        }

        return $filter_is_orderby;
    }

    /**
     * @param $data
     * @return array
     */
    private function _handleParentTable()
    {
        $data = [];
        $parentTablePK = CB::pk(g('parent_table'));
        $data['parent_table'] = DB::table(request('parent_table'))->where($parentTablePK, request('parent_id'))->first();
        if (request('foreign_key')) {
            $data['parent_field'] = request('foreign_key');
        } else {
            $data['parent_field'] = CB::getTableForeignKey(g('parent_table'), $this->table);
        }

        if ($data['parent_field']) {
            foreach ($this->columns_table as $i => $col) {
                if ($col['name'] == $data['parent_field']) {
                    unset($this->columns_table[$i]);
                }
            }
        }

        return $data;
    }

    /**
     * @param $col
     * @param $row
     * @param $table
     * @return array
     */
    private function _calculateColumnValue($col, $row, $table)
    {
        $value = @$row->{$col['field']};
        $title = @$row->{$this->title_field};
        $label = $col['label'];

        if (isset($col['image'])) {
            if ($value == '') {
                $value = "<a  data-lightbox='roadtrip' rel='group_{{$table}}' title='$label: $title' href='".asset('vendor/crudbooster/avatar.jpg')."'><img width='40px' height='40px' src='".asset('vendor/crudbooster/avatar.jpg')."'/></a>";
            } else {
                $pic = (strpos($value, 'http://') !== false) ? $value : asset($value);
                $value = "<a data-lightbox='roadtrip'  rel='group_{{$table}}' title='$label: $title' href='".$pic."'><img width='40px' height='40px' src='".$pic."'/></a>";
            }
        }

        if (@$col['download']) {
            $url = (strpos($value, 'http://') !== false) ? $value : asset($value).'?download=1';
            if ($value) {
                $value = "<a class='btn btn-xs btn-primary' href='$url' target='_blank' title='Download File'><i class='fa fa-download'></i> Download</a>";
            } else {
                $value = " - ";
            }
        }

        if ($col['str_limit']) {
            $value = trim(strip_tags($value));
            $value = str_limit($value, $col['str_limit']);
        }

        if ($col['nl2br']) {
            $value = nl2br($value);
        }

        if (isset($col['callback'])) {
            $value = call_user_func($col['callback'], $row);
        }

        $datavalue = @unserialize($value);
        if ($datavalue !== false && $datavalue) {
            $prevalue = [];
            foreach ($datavalue as $d) {
                if ($d['label']) {
                    $prevalue[] = $d['label'];
                }
            }
            if (count($prevalue)) {
                $value = implode(", ", $prevalue);
            }
        }

        return $value;
    }

    /**
     * @param $result
     * @param $columns_table
     * @param $table
     * @return mixed
     */
    private function _applyWhereAndQfilters($result, $columns_table, $table)
    {
        if (request('q')) {
            $result->where(function ($query) use ($columns_table) {
                foreach ($columns_table as $col) {
                    if (! $col['field_with']) {
                        continue;
                    }
                    $query->orwhere($col['field_with'], "like", "%".request("q")."%");
                }
            });
        }

        if (request('where')) {
            foreach (request('where') as $k => $v) {
                $result->where($table.'.'.$k, $v);
            }
        }
    }

    /**
     * @param $addaction
     * @return array
     */
    private function _handleSubModules($addaction)
    {
        foreach ($this->sub_module as $s) {
            $table_parent = CRUDBooster::parseSqlTable($this->table)['table'];
            $addaction[] = [
                'label' => $s['label'],
                'icon' => $s['button_icon'],
                'url' => CRUDBooster::adminPath($s['path']).'?parent_table='.$table_parent.'&parent_columns='.$s['parent_columns'].'&parent_columns_alias='.$s['parent_columns_alias'].'&parent_id=['.(! isset($s['custom_parent_id']) ? "id" : $s['custom_parent_id']).']&return_url='.urlencode(Request::fullUrl()).'&foreign_key='.$s['foreign_key'].'&label='.urlencode($s['label']),
                'color' => $s['button_color'],
                'showIf' => $s['showIf'],
            ];
        }

        return $addaction;
    }

    /**
     * @param $result
     * @param $field
     * @param $columns_table
     * @param $index
     * @return mixed
     */
    private function addDotField($result, $field, $columns_table, $index)
    {
        $result->addselect($field.' as '.str_slug($field, '_'));
        $tableField = substr($field, 0, strpos($field, '.'));
        $fieldOrign = substr($field, strpos($field, '.') + 1);
        $columns_table[$index]['type_data'] = CRUDBooster::getFieldType($tableField, $fieldOrign);
        $columns_table[$index]['field'] = str_slug($field, '_');
        $columns_table[$index]['field_raw'] = $field;
        $columns_table[$index]['field_with'] = $tableField.'.'.$fieldOrign;

        return $columns_table;
    }

    /**
     * @param $columns_table
     * @param $index
     * @param $field
     * @param $table
     * @param $result
     * @return mixed
     */
    private function addField($columns_table, $index, $field, $table, $result)
    {
        $columns_table[$index]['type_data'] = 'varchar';
        $columns_table[$index]['field_with'] = null;
        $columns_table[$index]['field'] = $field;
        $columns_table[$index]['field_raw'] = $field;

        if (CB::isColumnExists($table, $field)) {
            $result->addselect($table.'.'.$field);
            $columns_table[$index]['type_data'] = CRUDBooster::getFieldType($table, $field);
            $columns_table[$index]['field_with'] = $table.'.'.$field;
        }

        return $columns_table;
    }

    /**
     * @param $result
     */
    private function filterForParent($result)
    {
        $table_parent = $this->table;
        $table_parent = CRUDBooster::parseSqlTable($table_parent)['table'];
        $result->where($table_parent.'.'.request('foreign_key'), request('parent_id'));
    }

    /**
     * @param $table_columns
     * @param $result
     */
    private function filterSoftDeleted($table_columns, $result)
    {
        if (in_array('deleted_at', $table_columns)) {
            $result->where($this->table.'.deleted_at', '=', null);
        }
    }

    /**
     * @param $button_name
     * @param $id_selected
     * @return array
     */
    private function getMessageAndType($button_name, $id_selected)
    {
        $action = str_replace(['-', '_'], ' ', $button_name);
        $action = ucwords($action);
        $type = 'success';
        $message = trans("crudbooster.alert_action", ['action' => $action]);

        if ($this->actionButtonSelected($id_selected, $button_name) === false) {
            $message = ! empty($this->alert['message']) ? $this->alert['message'] : 'Error';
            $type = ! empty($this->alert['type']) ? $this->alert['type'] : 'danger';
        }

        return [$type, $message];
    }

    /**
     * @param $id_selected
     * @return mixed
     */
    private function deleteFromDB($id_selected)
    {
        $this->hookBeforeDelete($id_selected);
        $tablePK = CB::pk($this->table);
        if (Schema::hasColumn($this->table, 'deleted_at')) {

            $this->table()->whereIn($tablePK, $id_selected)->update(['deleted_at' => date('Y-m-d H:i:s')]);
        } else {
            $this->table()->whereIn($tablePK, $id_selected)->delete();
        }
        CRUDBooster::insertLog(trans("crudbooster.log_delete", ['name' => implode(',', $id_selected), 'module' => CRUDBooster::getCurrentModule()->name]));

        $this->hookAfterDelete($id_selected);

        return CRUDBooster::backWithMsg(trans("crudbooster.alert_delete_selected_success"));
    }

    /**
 * @return mixed
 */
    protected function table()
    {
        return \DB::table($this->table);
    }

    /**
     * @param $id
     * @return mixed
     */
    protected function findRow($id)
    {
        return $this->table()->where($this->primary_key, $id);
    }

    /**
     * @param $id
     * @param $di
     * @return array
     */
    private function prepareValidationRules($id, &$di)
    {
        $name = $di['name'];
        $exp = explode('|', $di['validation']);

        if (!count($exp)) {
            return '';
        }

        foreach ($exp as &$validationItem) {
            if (substr($validationItem, 0, 6) !== 'unique') {
                continue;
            }

            $parseUnique = explode(',', str_replace('unique:', '', $validationItem));
            $uniqueTable = ($parseUnique[0]) ?: $this->table;
            $uniqueColumn = ($parseUnique[1]) ?: $name;
            $uniqueIgnoreId = ($parseUnique[2]) ?: (($id) ?: '');

            //Make sure table name
            $uniqueTable = CB::parseSqlTable($uniqueTable)['table'];

            //Rebuild unique rule
            $uniqueRebuild = [];
            $uniqueRebuild[] = $uniqueTable;
            $uniqueRebuild[] = $uniqueColumn;

            if ($uniqueIgnoreId) {
                $uniqueRebuild[] = $uniqueIgnoreId;
            } else {
                $uniqueRebuild[] = 'NULL';
            }

            //Check whether deleted_at exists or not
            if (Schema::hasColumn($uniqueTable, 'deleted_at')) {
                $uniqueRebuild[] = CB::findPrimaryKey($uniqueTable);
                $uniqueRebuild[] = 'deleted_at';
                $uniqueRebuild[] = 'NULL';
            }
            $uniqueRebuild = array_filter($uniqueRebuild);
            $validationItem = 'unique:'.implode(',', $uniqueRebuild);
        }


        return implode('|', $exp);
    }
}
