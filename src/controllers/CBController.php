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
use crocodicstudio\crudbooster\controllers\CBController\ImportData;
use crocodicstudio\crudbooster\controllers\CBController\IndexAjax;
use crocodicstudio\crudbooster\controllers\Helpers\IndexExport;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\PDF;
use CRUDBooster;
use CB;
use Schema;

class CBController extends Controller
{
    use Hooks;
    use Deleter, CbFormLoader, CbIndexLoader, CbLayoutLoader, IndexAjax, ImportData;

    public $data_inputan;

    public $module_name;

    public $table;

    public $title_field;

    public $primary_key = 'id';

    public $arr = [];

    public $col = [];

    public $form = [];

    public $data = [];

    public $addaction = [];

    //public $global_privilege = false;

    public $button_delete = true;

    public $button_action_style = 'button_icon';

    public $return_url = null;

    public $parent_field = null;

    public $parent_id = null;

    public $hide_form = [];

    public $index_return = false; //for export

    public function cbView($template, $data)
    {
        $this->cbLayoutLoader();
        view()->share($this->data);
        return view($template, $data);
    }

    public function cbLoader()
    {
        $this->cbInit();

        $this->cbLayoutLoader();
        $this->cbFormLoader();
        $this->cbIndexLoader();
        $this->checkHideForm();
        $this->genericLoader();

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

        return $this->cbView('crudbooster::default.index', $data);
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

    public function getAdd()
    {
        $page_title = cbTrans('add_data_page_title', ['module' => CB::getCurrentModule()->name]);
        $command = 'add';
        return $this->cbForm(compact('page_title', 'command'));
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
        $row = $this->findRow($id)->first();

        $page_title = cbTrans("edit_data_page_title", ['module' => CB::getCurrentModule()->name, 'name' => $row->{$this->title_field}]);
        $command = 'edit';
        session()->put('current_row_id', $id);

        return $this->cbForm(compact('id', 'row', 'page_title', 'command'));
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
        $row = $this->findRow($id)->first();


        $page_title = cbTrans('detail_data_page_title', ['module' => CB::getCurrentModule()->name, 'name' => $row->{$this->title_field}]);
        $command = 'detail';

        session()->put('current_row_id', $id);

        return $this->cbForm(compact('row', 'page_title', 'command', 'id'));
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

    /**
     * @param $data
     * @return mixed
     */
    private function cbForm($data)
    {
        $this->genericLoader();
        $this->cbFormLoader();
        return $this->cbView('crudbooster::default.form', $data);
    }

    private function genericLoader()
    {
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
    }
}