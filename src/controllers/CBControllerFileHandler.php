<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 4/7/2020
 * Time: 5:55 PM
 */

namespace crocodicstudio\crudbooster\controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

trait CBControllerFileHandler
{

    public function getDeleteImage()
    {
        $id = request()->get('id');
        $column = request()->get('column');

        $row = DB::table($this->table)->where($this->primary_key, $id)->first();

        if (! cb()->isDelete() && $this->global_privilege == false) {
            cb()->insertLog(trans("crudbooster.log_try_delete_image", [
                'name' => $row->{$this->title_field},
                'module' => cb()->getCurrentModule()->name,
            ]));
            cb()->redirect(cb()->adminPath(), trans('crudbooster.denied_access'));
        }

        $row = DB::table($this->table)->where($this->primary_key, $id)->first();

        $file = str_replace('uploads/', '', $row->{$column});
        if (Storage::exists($file)) {
            Storage::delete($file);
        }

        DB::table($this->table)->where($this->primary_key, $id)->update([$column => null]);

        cb()->insertLog(trans("crudbooster.log_delete_image", [
            'name' => $row->{$this->title_field},
            'module' => cb()->getCurrentModule()->name,
        ]));

        cb()->redirect(request()->server('HTTP_REFERER'), trans('crudbooster.alert_delete_data_success'), 'success');
    }

    public function postUploadSummernote()
    {
        $name = 'userfile';
        if ($file = cb()->uploadFile($name, true)) {
            echo asset($file);
        }
    }

    public function postUploadFile()
    {
        $this->cbLoader();
        $name = 'userfile';
        if ($file = cb()->uploadFile($name, true)) {
            echo asset($file);
        }
    }

}