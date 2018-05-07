<?php

namespace crocodicstudio\crudbooster\controllers\CBController;

use crocodicstudio\crudbooster\CBCoreModule\DataRemover;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use crocodicstudio\crudbooster\helpers\CRUDBooster;

trait Deleter
{
    public function getDelete($id)
    {
        $this->genericLoader();
        (new DataRemover($this))->doDeleteWithHook([$id]);
        $this->insertLog('log_delete', $id);
        $url = request('return_url') ?: Request::server('HTTP_REFERER');
        CRUDBooster::redirect($url, cbTrans('alert_delete_data_success'), 'success');
    }

    public function postActionSelected()
    {
        $this->genericLoader();
        $selectedIds = request('checkbox');
        $btnName = request('button_name');
        if (! $selectedIds) {
            backWithMsg(cbTrans('at_least_one_row'), 'warning');
        }
        if ($btnName == 'delete') {
            (new DataRemover($this))->doDeleteWithHook($selectedIds);
            $this->insertLog('log_delete', implode(',', $selectedIds) . ' - '. $this->table);
            backWithMsg(cbTrans('alert_delete_selected_success'));
        }

        return $this->_redirectBackWithMsg($btnName, $selectedIds);
    }

    /**
     * @param $button_name
     * @param $id_selected
     * @return null
     */
    private function _redirectBackWithMsg($button_name, $id_selected)
    {
        $action = ucwords(str_replace(['-', '_'], ' ', $button_name));
        $type = 'success';
        $message = cbTrans('alert_action', ['action' => $action]);

        if ($this->actionButtonSelected($id_selected, $button_name) === false) {
            $message = ! empty($this->alert['message']) ? $this->alert['message'] : 'Error';
            $type = ! empty($this->alert['type']) ? $this->alert['type'] : 'danger';
        }

        backWithMsg($message, $type);
    }

    public function getDeleteImage()
    {
        $this->genericLoader();
        $id = request('id');
        $column = request('column');

        $row = $this->findRow($id)->first();
        $file = $row->{$column};

        Storage::delete($file);

        $this->findRow($id)->update([$column => null]);

        $this->insertLog('log_delete_image', $id . ' - '. $this->table);

        backWithMsg(cbTrans('alert_delete_data_success'));
    }
}