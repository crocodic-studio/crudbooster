<?php

namespace crocodicstudio\crudbooster\controllers;

error_reporting(E_ALL ^ E_NOTICE);

use crocodicstudio\crudbooster\CBCoreModule\RelationHandler;
use crocodicstudio\crudbooster\CBCoreModule\Hooks;
use crocodicstudio\crudbooster\CBCoreModule\Index;
use crocodicstudio\crudbooster\CBCoreModule\Search;
use crocodicstudio\crudbooster\controllers\CBController\CbFormLoader;
use crocodicstudio\crudbooster\controllers\CBController\CbIndexLoader;
use crocodicstudio\crudbooster\controllers\CBController\CbLayoutLoader;
use crocodicstudio\crudbooster\controllers\CBController\Deleter;
use crocodicstudio\crudbooster\controllers\Helpers\IndexExport;
use crocodicstudio\crudbooster\controllers\Helpers\IndexImport;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\PDF;
use Maatwebsite\Excel\Facades\Excel;
use CRUDBooster;
use CB;
use Schema;

class CBController extends Controller
{
    use Hooks;
    use Deleter,CbFormLoader, CbIndexLoader, CbLayoutLoader;

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

    public $global_privilege = false;

    public $button_delete = true;

    public $button_action_style = 'button_icon';

    public $return_url = null;

    public $parent_field = null;

    public $parent_id = null;

    public $hide_form = [];

    public $index_return = false; //for export

    public function cbView($template, $data)
    {
        $this->cbLoader();
        echo view($template, $data);
    }

    public function cbLoader()
    {
        $this->cbInit();

        $this->checkHideForm();
        $this->cbLayoutLoader();
        $this->primary_key = CB::pk($this->table);
        $this->columns_table = $this->col;
        $this->data_inputan = $this->form;
        $this->data['pk'] = $this->primary_key;
        $this->data['forms'] = $this->data_inputan;
        $this->data['hide_form'] = $this->hide_form;
        $this->data['table'] = $this->table;
        $this->data['title_field'] = $this->title_field;
        $this->data['appname'] = cbGetsetting('appname');
        $this->data['index_button'] = $this->index_button;
        $this->data['button_delete'] = $this->button_delete;
        $this->data['sub_module'] = $this->sub_module;
        $this->data['parent_field'] = (request('parent_field')) ?: $this->parent_field;
        $this->data['parent_id'] = (request('parent_id')) ?: $this->parent_id;

        if (CB::getCurrentMethod() == 'getProfile') {
            session()->put('current_row_id', CB::myId());
            $this->data['return_url'] = Request::fullUrl();
        }

        view()->share($this->data);
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

    public function getExportData()
    {
        return redirect(CB::mainpath());
    }

    public function postExportData()
    {
        $this->limit = request('limit');
        $this->index_return = true;
        $filename = request('filename');
        $papersize = request('page_size');
        $paperorientation = request('page_orientation');
        $indexContent = $this->getIndex();

        if (request('default_paper_size')) {
            DB::table('cms_settings')->where('name', 'default_paper_size')->update(['content' => $papersize]);
        }
        $format = request('fileformat');
        if(in_array($format, ['pdf', 'xls', 'csv']))
        {
            return app(IndexExport::class)->{$format}($filename, $indexContent, $paperorientation, $papersize);
        }
    }

    public function getIndex()
    {
        $index = app(Index::class);
        $this->cbIndexLoader();
        $this->cbLoader();
        $data = $index->index($this);

        return view('crudbooster::default.index', $data);
    }

    public function getDataQuery()
    {
        $key = request('query');
        if (! Cache::has($key)) {
            return response()->json(['items' => []]);
        }
        $query = Cache::get($key);

        $fk_name = request('fk_name');
        $fk_value = request('fk_value');

        $condition = ' where ';
        if (strpos(strtolower($query), 'where') !== false) {
            $condition = ' and ';
        }

        if (strpos(strtolower($query), 'order by')) {
            $query = str_replace('ORDER BY', 'order by', $query);
            $qraw = explode('order by', $query);
            $query = $qraw[0].$condition.$fk_name." = '$fk_value' $qraw[1]";
        } else {
            $query .= $condition.$fk_name." = '$fk_value'";
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
        $data = base64_decode(json_decode(request('data'), true));

        $columns = explode(',', $data['columns']);

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
        DB::table($table)->where(CB::pk($table), $id)->update([$column => $value]);

        backWithMsg(cbTrans('alert_update_data_success'));
    }

    public function postFindData()
    {
        $items = app(Search::class)->searchData(request('data'), request('q'), request('id'));

        return response()->json(['items' => $items]);
    }

    public function getAdd()
    {
        $this->cbFormLoader();
        $this->cbLoader();

        $page_title = cbTrans('add_data_page_title', ['module' => CB::getCurrentModule()->name]);

        $command = 'add';

        return view('crudbooster::default.form', compact('page_title', 'command'));
    }

    public function postAddSave()
    {
        $this->cbLoader();

        app(FormValidator::class)->validate(null, $this->form, $this->table);
        $this->inputAssignment();

        $this->setTimeStamps('created_at');

        $this->hookBeforeAdd($this->arr);

        $this->arr[$this->primary_key] = $id = $this->table()->insertGetId($this->arr);
        app(RelationHandler::class)->save($this->table, $id, $this->data_inputan);

        $this->hookAfterAdd($this->arr[$this->primary_key]);

        $this->insertLog('log_add', $this->arr[$this->title_field]);

        $this->sendResponseForSave('alert_add_data_success');
    }

    public function inputAssignment($id = null)
    {
        $hide_form = (request('hide_form')) ? unserialize(request('hide_form')) : [];

        foreach ($this->form as $ro) {
            $name = $ro['name'];
            $type = $ro['type'] ?: 'text';
            $inputdata = request($name);

            if (! $name || $ro['exception']) {
                continue;
            }

            if (count($hide_form) && in_array($name, $hide_form)) {
                continue;
            }

            $hookPath = \CB::componentsPath($type).DIRECTORY_SEPARATOR.'hookInputAssignment.php';
            if (file_exists($hookPath)) {
                require_once($hookPath);
            }
            unset($hookPath);

            if (Request::hasFile($name)) {
                continue;
            }

            if ($inputdata == '' && CB::isColumnNULL($this->table, $name)) {
                continue;
            }

            $this->arr[$name] = '';

            if ($inputdata != '') {
                $this->arr[$name] = $inputdata;
            }
        }
    }

    /**
     * @param null $tableName
     * @return mixed
     */
    public function table($tableName = null)
    {
        $table = $tableName ?: $this->table;
        return \DB::table($table);
    }

    public function getEdit($id)
    {
        $this->cbFormLoader();
        $this->cbLoader();
        $row = $this->findRow($id)->first();

        $page_title = cbTrans("edit_data_page_title", ['module' => CB::getCurrentModule()->name, 'name' => $row->{$this->title_field}]);
        $command = 'edit';
        session()->put('current_row_id', $id);

        return view('crudbooster::default.form', compact('id', 'row', 'page_title', 'command'));
    }

    /**
     * @param $id
     * @return mixed
     */
    public function findRow($id)
    {
        return $this->table()->where($this->primary_key, $id);
    }

    public function postEditSave($id)
    {
        $this->cbLoader();

        app(FormValidator::class)->validate($id, $this->form, $this->table);
        $this->inputAssignment($id);

        $this->setTimeStamps('updated_at');

        $this->hookBeforeEdit($this->arr, $id);
        $this->findRow($id)->update($this->arr);
        app(RelationHandler::class)->save($this->table, $id, $this->data_inputan);

        $this->hookAfterEdit($id);

        $this->insertLog('log_update', $this->arr[$this->title_field]);

        $this->sendResponseForSave('alert_update_data_success');
    }

    public function getDetail($id)
    {
        $this->cbFormLoader();
        $this->cbLoader();
        $row = $this->findRow($id)->first();


        $page_title = cbTrans('detail_data_page_title', ['module' => CB::getCurrentModule()->name, 'name' => $row->{$this->title_field}]);
        $command = 'detail';

        session()->put('current_row_id', $id);

        return view('crudbooster::default.form', compact('row', 'page_title', 'command', 'id'));
    }

    public function getImportData()
    {
        $this->cbLoader();

        $data = [];
        $data['page_menu'] = Route::getCurrentRoute()->getActionName();
        $data['page_title'] = 'Import Data '.CB::getCurrentModule()->name;

        if (! request('file') || request('import')) {
            return view('crudbooster::import', $data);
        }

        $file = 'storage'.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.base64_decode(request('file'));
        $rows = Excel::load($file, function ($reader) {
        })->get();

        session()->put('total_data_import', count($rows));

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

        $data['table_columns'] = DB::getSchemaBuilder()->getColumnListing($this->table);
        $data['data_import_column'] = $data_import_column;

        return view('crudbooster::import', $data);
    }

    public function postDoImportChunk()
    {
        $import = app(IndexImport::class);
        $this->cbLoader();
        $fileMD5 = md5(request('file'));

        if (request('file') && request('resume') == 1) {
            return $import->handleImportProgress($fileMD5);
        }

        $import->InsertToDB($fileMD5, $this->table, $this->title_field);

        return response()->json(['status' => true]);
    }

    public function actionButtonSelected($id_selected, $button_name)
    {
    }

    private function sendResponseForSave($msg)
    {
        $this->return_url = $this->return_url ?: request('return_url');
        if ($this->return_url) {
            if (request('submit') == cbTrans('button_save_more')) {
                CB::redirect(Request::server('HTTP_REFERER'), cbTrans($msg), 'success');
            }
            CB::redirect($this->return_url, cbTrans($msg), 'success');
        }
        if (request('submit') == cbTrans('button_save_more')) {
            CB::redirect(CB::mainpath('add'), cbTrans($msg), 'success');
        }
        CB::redirect(CB::mainpath(), cbTrans($msg), 'success');
    }

    private function insertLog($msg, $name)
    {
        CB::insertLog(trans('logging.'.$msg, ['module' => CB::getCurrentModule()->name, 'name' => $name]));
    }

    private function setTimeStamps($col)
    {
        if (Schema::hasColumn($this->table, $col)) {
            $this->arr[$col] = date('Y-m-d H:i:s');
        }
    }
}