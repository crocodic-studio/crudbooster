<?php

namespace crocodicstudio\crudbooster\controllers;

use CB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Request;
use CRUDBooster;


class FormValidator
{
    private $table;

    public function validate($id = null, $form, $table)
    {
        $this->table = $table;
        $request_all = Request::all();
        $array_input = [];
        $componentPath = implode(DIRECTORY_SEPARATOR, ["vendor", "crocodicstudio", "crudbooster", "src", "views", "default", "type_components", ""]);

        foreach ($form as $di) {
            $ai = [];
            $name = $di['name'];
            $type = $di['type'];

            if (! $name) {continue;}

            if ($di['required'] && ! Request::hasFile($name)) {
                $ai[] = 'required';
            }

            if (file_exists(base_path($componentPath.$type.DIRECTORY_SEPARATOR.'hookInputValidation.php'))) {
                require_once(base_path($componentPath.$type.DIRECTORY_SEPARATOR.'hookInputValidation.php'));
            }

            if (@$di['validation']) {
                $array_input[$name] = $this->prepareValidationRules($id, $di);
            } else {
                $array_input[$name] = implode('|', $ai);
            }
        }

        $validator = Validator::make($request_all, $array_input);

        if (! $validator->fails()) {
            return null;
        }

        $this->sendFailedValidationResponse($validator);
    }
    /**
     * @param $id
     * @param $di
     * @return array
     */
    private function prepareValidationRules($id, $di)
    {
        $exp = explode('|', $di['validation']);

        if (! count($exp)) {
            return '';
        }

        foreach ($exp as &$validationItem) {
            if (substr($validationItem, 0, 6) !== 'unique') {
                continue;
            }

            $parseUnique = explode(',', str_replace('unique:', '', $validationItem));
            $uniqueTable = ($parseUnique[0]) ?: $this->table;
            $uniqueColumn = ($parseUnique[1]) ?: $di['name'];
            $uniqueIgnoreId = ($parseUnique[2]) ?: (($id) ?: '');

            //Make sure table name
            $uniqueTable = CB::parseSqlTable($uniqueTable)['table'];

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
            if (Schema::hasColumn($uniqueTable, 'deleted_at')) {
                $uniqueRebuild[] = CB::findPrimaryKey($uniqueTable);
                $uniqueRebuild[] = 'deleted_at';
                $uniqueRebuild[] = 'NULL';
            }
            $uniqueRebuild = array_filter($uniqueRebuild);
            $validationItem = 'unique:'.implode(',', $uniqueRebuild);
        }

        return implode('|', $exp);
    }
    /**
     * @param $validator
     */
    private function sendFailedValidationResponse($validator)
    {
        $message = $validator->messages();
        $message_all = $message->all();

        if (Request::ajax()) {
            $resp = response()->json([
                'message' => trans('crudbooster.alert_validation_error', ['error' => implode(', ', $message_all)]),
                'message_type' => 'warning',
            ]);
            sendAndTerminate($resp);
        }

        sendAndTerminate(redirect()->back()->with("errors", $message)->with([
            'message' => trans('crudbooster.alert_validation_error', ['error' => implode(', ', $message_all)]),
            'message_type' => 'warning',
        ])->withInput());
    }
}