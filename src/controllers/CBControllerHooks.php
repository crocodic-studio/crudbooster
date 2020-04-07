<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 4/7/2020
 * Time: 5:49 PM
 */

namespace crocodicstudio\crudbooster\controllers;


use crocodicstudio\crudbooster\helpers\TypeComponentHelper;

trait CBControllerHooks
{
    private function afterSavingDataProcess($id) {
        //Looping Data Input Again After Insert
        foreach ($this->data_inputan as $ro) {
            if (! $ro['name']) continue;

            // Checkbox processing after edit
            TypeComponentHelper::checkbox($ro, $id, $this->table);

            // Select2 processing after edit
            TypeComponentHelper::select2($ro, $id, $this->table);

            // Child processing after edit
            TypeComponentHelper::child($ro, $id);
        }
    }

    public function postActionSelected()
    {
        $id_selected = request()->input('checkbox');
        $button_name = request()->input('button_name');

        if (! $id_selected) {
            cb()->redirect($_SERVER['HTTP_REFERER'], trans("crudbooster.alert_select_a_data"), 'warning');
        }

        if ($button_name == 'delete') {
            if (! cb()->isDelete()) {
                cb()->insertLog(trans("crudbooster.log_try_delete_selected", ['module' => cb()->getCurrentModule()->name]));
                cb()->redirect(cb()->adminPath(), trans('crudbooster.denied_access'));
            }

            $this->hook_before_delete($id_selected);
            $tablePK = cb()->pk($this->table);
            if (cb()->isColumnExists($this->table, 'deleted_at')) {

                DB::table($this->table)->whereIn($tablePK, $id_selected)->update(['deleted_at' => date('Y-m-d H:i:s')]);
            } else {
                DB::table($this->table)->whereIn($tablePK, $id_selected)->delete();
            }
            cb()->insertLog(trans("crudbooster.log_delete", ['name' => implode(',', $id_selected), 'module' => cb()->getCurrentModule()->name]));

            $this->hook_after_delete($id_selected);

            $message = trans("crudbooster.alert_delete_selected_success");

            return redirect()->back()->with(['type' => 'success', 'message' => $message]);
        }

        $action = str_replace(['-', '_'], ' ', $button_name);
        $action = ucwords($action);
        $type = 'success';
        $message = trans("crudbooster.alert_action", ['action' => $action]);

        if ($this->actionButtonSelected($id_selected, $button_name) === false) {
            $message = ! empty($this->alert['message']) ? $this->alert['message'] : 'Error';
            $type = ! empty($this->alert['type']) ? $this->alert['type'] : 'danger';
        }

        return redirect()->back()->with(['type' => $type, 'message' => $message]);
    }

    public function actionButtonSelected($id_selected, $button_name)
    {
    }

    public function hook_before_add(&$arr)
    {
    }

    public function hook_after_add($id)
    {
    }

    public function hook_query_index(&$query)
    {
    }

    public function hook_row_index($index, &$value)
    {
    }

    public function hook_before_edit(&$arr, $id)
    {
    }

    public function hook_after_edit($id)
    {
    }

    public function hook_before_delete($id)
    {
    }

    public function hook_after_delete($id)
    {
    }

    public function hookBeforeAdd(&$arr)
    {
    }

    public function hookAfterAdd($id)
    {
    }

    public function hookQueryIndex(&$query)
    {
    }

    public function hookRowIndex($index, &$value)
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

    public function onIndex(callable $callback) {
        if(cb()->getCurrentMethod() == 'getIndex') {
            call_user_func($callback);
        }
    }

    public function onAdd(callable $callback) {
        if(cb()->getCurrentMethod() == 'getAdd' || cb()->getCurrentMethod() == 'postAddSave') {
            call_user_func($callback);
        }
    }

    public function onEdit(callable $callback) {
        if(cb()->getCurrentMethod() == 'getEdit' || cb()->getCurrentMethod() == 'postEditSave') {
            call_user_func($callback);
        }
    }

    public function onDetail(callable $callback) {
        if(cb()->getCurrentMethod() == 'getDetail') {
            call_user_func($callback);
        }
    }

}