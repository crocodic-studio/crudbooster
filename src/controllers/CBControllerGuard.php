<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 4/7/2020
 * Time: 7:42 PM
 */

namespace crocodicstudio\crudbooster\controllers;


trait CBControllerGuard
{
    private function guardDetail($row) {
        if(config('crudbooster.GUARD')) {
            if (! cb()->isRead() && $this->global_privilege == false || $this->button_detail == false) {
                cb()->insertLog(trans("crudbooster.log_try_view", [
                    'name' => $row->{$this->title_field},
                    'module' => $this->module_name,
                ]));
                cb()->redirect(cb()->adminPath(), trans('crudbooster.denied_access'));
            }
        }
    }

    private function guardDelete($row) {
        if(config('crudbooster.GUARD')) {
            if (! cb()->isDelete() && $this->global_privilege == false || $this->button_delete == false) {
                cb()->insertLog(trans("crudbooster.log_try_delete", [
                    'name' => $row->{$this->title_field},
                    'module' => $this->module_name,
                ]));
                cb()->redirect(cb()->adminPath(), trans('crudbooster.denied_access'));
            }
        }

    }

    private function guardEditSave($row) {
        if(config('crudbooster.GUARD')) {
            if (! cb()->isUpdate() && $this->global_privilege == false) {
                cb()->insertLog(trans("crudbooster.log_try_add", ['name' => $row->{$this->title_field}, 'module' => $this->module_name]));
                cb()->redirect(cb()->adminPath(), trans('crudbooster.denied_access'));
            }
        }

    }

    private function guardEdit($row) {
        if(config('crudbooster.GUARD')) {
            if (! cb()->isRead() && $this->global_privilege == false || $this->button_edit == false) {
                cb()->insertLog(trans("crudbooster.log_try_edit", [
                    'name' => $row->{$this->title_field},
                    'module' => $this->module_name,
                ]));
                cb()->redirect(cb()->adminPath(), trans('crudbooster.denied_access'));
            }
        }

    }

    private function guardAddSave() {
        if(config('crudbooster.GUARD')) {
            if (! cb()->isCreate() && $this->global_privilege == false) {
                cb()->insertLog(trans('crudbooster.log_try_add_save', [
                    'name' => request()->input($this->title_field),
                    'module' => $this->module_name,
                ]));
                cb()->redirect(cb()->adminPath(), trans("crudbooster.denied_access"));
            }
        }

    }

    private function guardAdd() {
        if(config('crudbooster.GUARD')) {
            if (! cb()->isCreate() && $this->global_privilege == false || $this->button_add == false) {
                cb()->insertLog(trans('crudbooster.log_try_add', ['module' => $this->module_name]));
                cb()->redirect(cb()->adminPath(), trans("crudbooster.denied_access"));
            }
        }

    }

    private function guardIndex() {
        if(config('crudbooster.GUARD')) {
            if (! cb()->isView() && $this->global_privilege == false) {
                cb()->insertLog(trans('crudbooster.log_try_view', ['module' => $this->module_name]));
                cb()->redirect(cb()->adminPath(), trans('crudbooster.denied_access'));
            }
        }
        
    }

}