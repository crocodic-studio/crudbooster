<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 4/7/2020
 * Time: 5:57 PM
 */

namespace crocodicstudio\crudbooster\controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

trait CBControllerValidator
{
    private function checkHideForm()
    {
        if ($this->hide_form && count($this->hide_form)) {
            foreach ($this->form as $i => $f) {
                if (in_array($f['name'], $this->hide_form)) {
                    unset($this->form[$i]);
                }
            }
        }
    }

    public function validation($id = null)
    {
        $request_all = request()->all();
        $array_input = [];
        foreach ($this->data_inputan as $di) {
            $ai = [];
            $name = $di['name'];

            if (! isset($request_all[$name])) {
                continue;
            }

            if ($di['type'] != 'upload') {
                if (@$di['required']) {
                    $ai[] = 'required';
                }
            }

            if ($di['type'] == 'upload') {
                if ($id) {
                    $row = DB::table($this->table)->where($this->primary_key, $id)->first();
                    if ($row->{$di['name']} == '') {
                        $ai[] = 'required';
                    }
                }
            }

            if (@$di['min']) {
                $ai[] = 'min:'.$di['min'];
            }
            if (@$di['max']) {
                $ai[] = 'max:'.$di['max'];
            }
            if (@$di['image']) {
                $ai[] = 'image';
            }
            if (@$di['mimes']) {
                $ai[] = 'mimes:'.$di['mimes'];
            }
            $name = $di['name'];
            if (! $name) {
                continue;
            }

            if ($di['type'] == 'money') {
                $request_all[$name] = preg_replace('/[^\d-]+/', '', $request_all[$name]);
            }

            if ($di['type'] == 'child') {
                $slug_name = str_slug($di['label'], '');
                foreach ($di['columns'] as $child_col) {
                    if (isset($child_col['validation'])) {
                        //https://laracasts.com/discuss/channels/general-discussion/array-validation-is-not-working/
                        if (strpos($child_col['validation'], 'required') !== false) {
                            $array_input[$slug_name.'-'.$child_col['name']] = 'required';

                            str_replace('required', '', $child_col['validation']);
                        }

                        $array_input[$slug_name.'-'.$child_col['name'].'.*'] = $child_col['validation'];
                    }
                }
            }

            if (@$di['validation']) {

                $exp = explode('|', $di['validation']);
                if ($exp && count($exp)) {
                    foreach ($exp as &$validationItem) {
                        if (substr($validationItem, 0, 6) == 'unique') {
                            $parseUnique = explode(',', str_replace('unique:', '', $validationItem));
                            $uniqueTable = ($parseUnique[0]) ?: $this->table;
                            $uniqueColumn = ($parseUnique[1]) ?: $name;
                            $uniqueIgnoreId = ($parseUnique[2]) ?: (($id) ?: '');

                            //Make sure table name
                            $uniqueTable = cb()->parseSqlTable($uniqueTable)['table'];

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
                            if (cb()->isColumnExists($uniqueTable, 'deleted_at')) {
                                $uniqueRebuild[] = cb()->findPrimaryKey($uniqueTable);
                                $uniqueRebuild[] = 'deleted_at';
                                $uniqueRebuild[] = 'NULL';
                            }
                            $uniqueRebuild = array_filter($uniqueRebuild);
                            $validationItem = 'unique:'.implode(',', $uniqueRebuild);
                        }
                    }
                } else {
                    $exp = [];
                }

                $validation = implode('|', $exp);

                $array_input[$name] = $validation;
            } else {
                $array_input[$name] = implode('|', $ai);
            }
        }

        $validator = Validator::make($request_all, $array_input);

        if ($validator->fails()) {
            $message = $validator->messages();
            $message_all = $message->all();

            if (request()->ajax()) {
                $res = response()->json([
                    'message' => trans('crudbooster.alert_validation_error', ['error' => implode(', ', $message_all)]),
                    'type' => 'warning',
                ])->send();
                exit;
            } else {
                $res = redirect()->back()->with("errors", $message)->with([
                    'message' => trans('crudbooster.alert_validation_error', ['error' => implode(', ', $message_all)]),
                    'type' => 'warning',
                ])->withInput();
                \Session::driver()->save();
                $res->send();
                exit;
            }
        }
    }

    public function inputAssignment($id = null)
    {

        $hide_form = (request()->get('hide_form')) ? unserialize(request()->get('hide_form')) : [];

        foreach ($this->data_inputan as $ro) {
            $name = $ro['name'];

            if (! $name) {
                continue;
            }

            if ($ro['exception']) {
                continue;
            }

            if ($name == 'hide_form') {
                continue;
            }

            if ($hide_form && count($hide_form)) {
                if (in_array($name, $hide_form)) {
                    continue;
                }
            }

            if ($ro['type'] == 'checkbox' && $ro['relationship_table']) {
                continue;
            }

            if ($ro['type'] == 'select2' && $ro['relationship_table']) {
                continue;
            }

            $inputdata = request()->get($name);

            if ($ro['type'] == 'money') {
                $inputdata = preg_replace('/[^\d-]+/', '', $inputdata);
            }

            if ($ro['type'] == 'child') {
                continue;
            }

            if ($name) {
                if ($inputdata != '') {
                    $this->arr[$name] = $inputdata;
                } else {
                    if (cb()->isColumnNULL($this->table, $name) && $ro['type'] != 'upload') {
                        continue;
                    } else {
                        $this->arr[$name] = "";
                    }
                }
            }

            $password_candidate = explode(',', config('crudbooster.PASSWORD_FIELDS_CANDIDATE'));
            if (in_array($name, $password_candidate)) {
                if (! empty($this->arr[$name])) {
                    $this->arr[$name] = Hash::make($this->arr[$name]);
                } else {
                    unset($this->arr[$name]);
                }
            }

            if ($ro['type'] == 'checkbox') {

                if (is_array($inputdata)) {
                    if ($ro['datatable'] != '') {
                        $table_checkbox = explode(',', $ro['datatable'])[0];
                        $field_checkbox = explode(',', $ro['datatable'])[1];
                        $table_checkbox_pk = cb()->pk($table_checkbox);
                        $data_checkbox = DB::table($table_checkbox)->whereIn($table_checkbox_pk, $inputdata)->pluck($field_checkbox)->toArray();
                        $this->arr[$name] = implode(";", $data_checkbox);
                    } else {
                        $this->arr[$name] = implode(";", $inputdata);
                    }
                }
            }

            //multitext colomn
            if ($ro['type'] == 'multitext') {
                $name = $ro['name'];
                $multitext = "";
                $maxI = ($this->arr[$name])?count($this->arr[$name]):0;
                for ($i = 0; $i <= $maxI - 1; $i++) {
                    $multitext .= $this->arr[$name][$i]."|";
                }
                $multitext = substr($multitext, 0, strlen($multitext) - 1);
                $this->arr[$name] = $multitext;
            }

            if ($ro['type'] == 'googlemaps') {
                if ($ro['latitude'] && $ro['longitude']) {
                    $latitude_name = $ro['latitude'];
                    $longitude_name = $ro['longitude'];
                    $this->arr[$latitude_name] = request()->get('input-latitude-'.$name);
                    $this->arr[$longitude_name] = request()->get('input-longitude-'.$name);
                }
            }

            if ($ro['type'] == 'select' || $ro['type'] == 'select2') {
                if ($ro['datatable']) {
                    if ($inputdata == '') {
                        $this->arr[$name] = 0;
                    }
                }
            }

            if (@$ro['type'] == 'upload') {

                $this->arr[$name] = cb()->uploadFile($name, $ro['encrypt'] || $ro['upload_encrypt'], $ro['resize_width'], $ro['resize_height'], cb()->myId());

                if (! $this->arr[$name]) {
                    $this->arr[$name] = request()->get('_'.$name);
                }
            }

            if (@$ro['type'] == 'filemanager') {
                $filename = str_replace('/'.config('lfm.prefix').'/'.config('lfm.files_folder_name').'/', '', $this->arr[$name]);
                $url = 'uploads/'.$filename;
                $this->arr[$name] = $url;
            }
        }
    }

}