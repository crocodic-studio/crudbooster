<?php

namespace crocodicstudio\crudbooster\controllers\CBController;

use CB;
use crocodicstudio\crudbooster\CBCoreModule\RelationHandler;
use crocodicstudio\crudbooster\controllers\FormValidator;
use crocodicstudio\crudbooster\helpers\DbInspector;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Schema;

trait FormSubmitHandlers
{
    public function inputAssignment($id = null)
    {
        $hide_form = (request('hide_form')) ? unserialize(request('hide_form')) : [];

        foreach ($this->form as $form) {
            $name = $form['name'];
            $type = $form['type'] ?: 'text';
            $inputdata = request($name);

            if (! $name || in_array($name, $hide_form) || $form['exception']) {
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

            if ($inputdata == '' && DbInspector::isColNull($this->table, $name)) {
                continue;
            }

            $this->arr[$name] = '';

            if ($inputdata != '') {
                $this->arr[$name] = $inputdata;
            }
        }
    }

    public function postAddSave()
    {
        $this->genericLoader();

        app(FormValidator::class)->validate(null, $this->form, $this->table);
        $this->inputAssignment();

        $this->setTimeStamps('created_at');

        $this->hookBeforeAdd($this->arr);
        $id = $this->table()->insertGetId($this->arr);
        app(RelationHandler::class)->save($this->table, $id, $this->data_inputan);
        $this->hookAfterAdd($id);

        $this->insertLog('log_add', $id. ' on ' . $this->table);

        $this->sendResponseForSave('alert_add_data_success');
    }

    public function postEditSave($id)
    {
        $this->genericLoader();

        app(FormValidator::class)->validate($id, $this->form, $this->table);
        $this->inputAssignment($id);

        $this->setTimeStamps('updated_at');

        $this->hookBeforeEdit($this->arr, $id);
        $this->findRow($id)->update($this->arr);
        app(RelationHandler::class)->save($this->table, $id, $this->data_inputan);
        $this->hookAfterEdit($id);

        $this->insertLog('log_update', $id. ' on ' . $this->table);

        $this->sendResponseForSave('alert_update_data_success');
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

    private function setTimeStamps($col)
    {
        if (Schema::hasColumn($this->table, $col)) {
            $this->arr[$col] = date('Y-m-d H:i:s');
        }
    }
}