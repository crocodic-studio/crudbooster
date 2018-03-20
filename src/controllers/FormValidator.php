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
        $rules = $this->getRules($id, $form);

        $validator = Validator::make(Request::all(), $rules);

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
    private function parseValidationRules($id, $di)
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
        $msg = [
            'message' => cbTrans('alert_validation_error', ['error' => implode(', ', $message->all())]),
            'message_type' => 'warning',
        ];

        if (Request::ajax()) {
        $resp = response()->json($msg);
            sendAndTerminate($resp);
        }
        $resp = redirect()->back()->with("errors", $message)->with($msg)->withnput();
        sendAndTerminate($resp);
    }

    /**
     * @param $id
     * @param $form
     * @return mixed
     */
    private function getRules($id, $form)
    {
        $componentPath = implode(DIRECTORY_SEPARATOR, ["vendor", "crocodicstudio", "crudbooster", "src", "views", "default", "type_components", ""]);
        $rules = [];
        foreach ($form as $formInput) {
            $name = $formInput['name'];
            if (! $name) {
                continue;
            }

            $ai = [];
            if ($formInput['required'] && ! Request::hasFile($name)) {
                $ai[] = 'required';
            }

            $hookValidationPath = base_path($componentPath.$formInput['type'].DIRECTORY_SEPARATOR.'hookInputValidation.php');
            if (file_exists($hookValidationPath)) {
                require_once($hookValidationPath);
            }
            unset($hookValidationPath);

            if (@$formInput['validation']) {
                $rules[$name] = $this->parseValidationRules($id, $formInput);
            } else {
                $rules[$name] = implode('|', $ai);
            }
        }

        return $rules;
    }
}