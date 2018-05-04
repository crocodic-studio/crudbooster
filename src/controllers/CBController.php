<?php

namespace crocodicstudio\crudbooster\controllers;

error_reporting(E_ALL ^ E_NOTICE);

use crocodicstudio\crudbooster\CBCoreModule\Hooks;
use crocodicstudio\crudbooster\CBCoreModule\Index;
use crocodicstudio\crudbooster\controllers\CBController\CbFormLoader;
use crocodicstudio\crudbooster\controllers\CBController\CbIndexLoader;
use crocodicstudio\crudbooster\controllers\CBController\CbLayoutLoader;
use crocodicstudio\crudbooster\controllers\CBController\Deleter;
use crocodicstudio\crudbooster\controllers\CBController\ExportData;
use crocodicstudio\crudbooster\controllers\CBController\FormSubmitHandlers;
use crocodicstudio\crudbooster\controllers\CBController\ImportData;
use crocodicstudio\crudbooster\controllers\CBController\IndexAjax;
use crocodicstudio\crudbooster\helpers\DbInspector;
use Illuminate\Support\Facades\DB;
use crocodicstudio\crudbooster\helpers\CRUDBooster;
use Schema;

abstract class CBController extends Controller
{
    abstract public function cbInit();

    use Hooks;
    use Deleter, CbFormLoader, CbIndexLoader, CbLayoutLoader, IndexAjax, ImportData, ExportData, FormSubmitHandlers;

    public $table;

    public $titleField;

    public $primaryKey = 'id';

    public $form = [];

    public $data = [];

    public $parent_field = null;

    public function cbView($template, $data)
    {
        $this->cbLayoutLoader();
        view()->share($this->data);
        return view($template, $data);
    }

    public function cbLoader()
    {
        $this->genericLoader();
        $this->cbLayoutLoader();
        $this->cbFormLoader();
        $this->cbIndexLoader();
        $this->checkHideForm();

        view()->share($this->data);
    }

    public function getIndex()
    {
        $this->genericLoader();
        $this->cbIndexLoader();
        $data = app(Index::class)->index($this);

        return $this->cbView('crudbooster::index.index', $data);
    }

    public function getUpdateSingle()
    {
        $table = request('table');
        $column = request('column');
        $value = request('value');
        $id = request('id');
        DB::table($table)->where(DbInspector::findPk($table), $id)->update([$column => $value]);

        backWithMsg(cbTrans('alert_update_data_success'));
    }

    public function getAdd()
    {
        $this->genericLoader();
        $page_title = cbTrans('add_data_page_title', ['module' => CRUDBooster::getCurrentModule()->name]);
        $command = 'add';
        return $this->cbForm(compact('page_title', 'command'));
    }

    /**
     * @param string $tableName
     * @return mixed
     */
    public function table($tableName = null)
    {
        $tableName = $tableName ?: $this->table;
        return \DB::table($tableName);
    }

    public function getEdit($id)
    {
        $this->genericLoader();
        $row = $this->findFirst($id);

        $page_title = cbTrans("edit_data_page_title", ['module' => CRUDBooster::getCurrentModule()->name, 'name' => $row->{$this->titleField}]);
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
        return $this->table()->where($this->primaryKey, $id);
    }

    public function getDetail($id)
    {
        $this->genericLoader();
        $this->cbFormLoader();
        $row = $this->findFirst($id);

        $page_title = cbTrans('detail_data_page_title', ['module' => CRUDBooster::getCurrentModule()->name, 'name' => $row->{$this->titleField}]);

        session()->put('current_row_id', $id);
        return $this->cbView('crudbooster::form.details', compact('row', 'page_title', 'id'));
    }

    public function actionButtonSelected($id_selected, $button_name)
    {
    }

    private function insertLog($msg, $name)
    {
        CRUDBooster::insertLog(trans('logging.'.$msg, ['module' => CRUDBooster::getCurrentModule()->name, 'name' => $name]));
    }

    /**
     * @param $data
     * @return mixed
     */
    private function cbForm($data)
    {
        $this->cbFormLoader();
        return $this->cbView('crudbooster::form.form', $data);
    }

    protected function genericLoader()
    {
        $this->primaryKey = DbInspector::findPk($this->table);
        $this->cbInit();
        $this->data_inputan = $this->form;
        $this->data['pk'] = $this->primaryKey;
        $this->data['hide_form'] = $this->hide_form;
        $this->data['table'] = $this->table;
        $this->data['titleField'] = $this->titleField;
        $this->data['appname'] = cbGetsetting('appname');
        $this->data['indexButton'] = $this->indexButton;

        $this->data['sub_module'] = $this->sub_module;
        $this->data['parent_field'] = (request('parent_field')) ?: $this->parent_field;
    }

    /**
     * @param $id
     * @return mixed
     */
    private function findFirst($id)
    {
        return $this->findRow($id)->first();
    }
}