<?php

namespace crocodicstudio\crudbooster\controllers\CBController;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Schema;
use CB;
use Illuminate\Support\Facades\Storage;

trait Deleter
{
    /**
     * @param $idsArray
     * @return mixed
     */
    private function deleteFromDB($idsArray)
    {
        $this->doDeleteWithHook($idsArray);

        $this->insertLog('log_delete', implode(',', $idsArray));

        backWithMsg(cbTrans('alert_delete_selected_success'));
    }

    /**
     * @param $idsArray
     */
    private function deleteIds($idsArray)
    {
        $query = $this->table()->whereIn($this->primary_key, $idsArray);
        if (Schema::hasColumn($this->table, 'deleted_at')) {
            $query->update(['deleted_at' => date('Y-m-d H:i:s')]);
        } else {
            $query->delete();
        }
    }

    /**
     * @param $idsArray
     */
    private function doDeleteWithHook($idsArray)
    {
        $this->hook_before_delete($idsArray);
        $this->deleteIds($idsArray);
        $this->hook_after_delete($idsArray);
    }


    public function getDelete($id)
    {
        $this->cbLoader();
        $row = $this->findRow($id)->first();

        $this->insertLog('log_delete', $row->{$this->title_field});

        $this->doDeleteWithHook([$id]);

        $url = request('return_url') ?: CB::referer();

        CB::redirect($url, cbTrans('alert_delete_data_success'), 'success');
    }

    public function postActionSelected()
    {
        $this->cbLoader();
        $selectedIds = request('checkbox');
        $button_name = request('button_name');

        if (! $selectedIds) {
            CB::redirect($_SERVER['HTTP_REFERER'], 'Please select at least one row!', 'warning');
        }

        if ($button_name == 'delete') {
            return $this->deleteFromDB($selectedIds);
        }

        return $this->_redirectBackWithMsg($button_name, $selectedIds);
    }

    /**
     * @param $button_name
     * @param $id_selected
     * @return array
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
        $this->cbLoader();
        $id = request('id');
        $column = request('column');

        $row = $this->findRow($id)->first();
        $file = $row->{$column};

        Storage::delete($file);

        $this->findRow($id)->update([$column => null]);

        $this->insertLog('log_delete_image', $row->{$this->title_field});

        CB::redirect(Request::server('HTTP_REFERER'), cbTrans('alert_delete_data_success'), 'success');
    }
}