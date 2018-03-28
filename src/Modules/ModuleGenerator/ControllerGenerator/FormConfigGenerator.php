<?php

namespace crocodicstudio\crudbooster\Modules\ModuleGenerator\ControllerGenerator;

use crocodicstudio\crudbooster\helpers\DbInspector;
use Illuminate\Support\Facades\Schema;
use CB;

class FormConfigGenerator
{
    /**
     * @param $table
     * @param $coloms
     * @return array
     */
    static function generateFormConfig($table, $coloms)
    {
        $formArrayString = [];
        foreach ($coloms as $i => $colName) {
            //$attribute = [];
            $placeholder = '';
            $help = '';

            $label = self::getLabel($colName);

            if (FieldDetector::isExceptional($colName)) {
                continue;
            }

            $typeData = DbInspector::getFieldTypes($table, $colName);

            $validation = [];
            $validation[] = 'required';
            list($type, $rule, $options) = self::parseFieldType($typeData);
            $validation[] = $rule;

            if (FieldDetector::isForeignKey($colName)) {
                list($type, $options) = self::handleForeignKey($colName);
            }

            if (substr($colName, 0, 3) == 'is_') {
                $type = 'radio_dataenum';
                $label = ucwords(substr($colName, 3));
                $validation = ['required|integer'];
                $options = [
                    'enum' => ['In '.$label, $label],
                    'value' => [0, 1],
                ];
            }

            if (FieldDetector::isPassword($colName)) {
                $type = 'password';
                $validation = ['min:3', 'max:32'];
                $help = cbTrans("text_default_help_password");
            }

            if (FieldDetector::isImage($colName)) {
                $type = 'upload';
                $validation = ['required|image'];
                $help = cbTrans('text_default_help_upload');
            }

            if (FieldDetector::isGeographical($colName)) {
                $type = 'hidden';
            }

            if (FieldDetector::isPhone($colName)) {
                $type = 'number';
                $validation = ['required', 'numeric'];
                $placeholder = cbTrans('text_default_help_number');
            }

            if (FieldDetector::isEmail($colName)) {
                $type = 'email';
                $validation[] = 'email|unique:'.$table;
                $placeholder = cbTrans('text_default_help_email');
            }

            if ($type == 'text' && FieldDetector::isNameField($colName)) {
                $validation = ['required', 'string', 'min:3', 'max:70'];
                $placeholder = cbTrans('text_default_help_text');
            }

            if ($type == 'text' && FieldDetector::isUrlField($colName)) {
                $validation = ['required', 'url'];
                $placeholder = cbTrans('text_default_help_url');
            }

            $validation = implode('|', $validation);

            $formArray = [];
            $formArray['label'] = $label;
            $formArray['name'] = $colName;
            $formArray['type'] = $type;
            $formArray['options'] = $options;
            $formArray['required'] = true;
            $formArray['validation'] = $validation;
            $formArray['help'] = $help;
            $formArray['placeholder'] = $placeholder;
            $formArrayString[] = min_var_export($formArray, "            ");
        }

        return $formArrayString;
    }

    /**
     * @param $c
     * @return mixed|string
     */
    private static function getLabel($c)
    {
        $label = str_replace("id_", "", $c);
        $label = ucwords(str_replace("_", " ", $label));

        return $label;
    }

    /**
     * @param $typeData
     * @param $validation
     * @return array
     */
    private static function parseFieldType($typeData)
    {
        $typeData = array_get([
            'longtext' => 'text',
            'integer' => 'int',
            'timestamp' => 'datetime',
        ], $typeData, $typeData);


        $default = ["text", "min:1|max:255", []];
        return array_get([
            'text' => ['textarea', "string|min:5", []],
            'date' => ['date', "date", ['php_format' => 'M, d Y', 'datepicker_format' => 'M, dd YYYY',]],
            'datetime' => ['datetime', "date_format:Y-m-d H:i:s", ['php_format' => 'M, d Y H:i',]],
            'time' => ['time', 'date_format:H:i:s', []],
            'double' => ['money', "integer|min:0", []],
            'int' => ['number', 'integer|min:0', []],
        ], $typeData, $default);
    }

    /**
     * @param $field
     * @return array
     */
    private static function handleForeignKey($field)
    {
        $jointable = str_replace(['id_', '_id'], '', $field);
        if (Schema::hasTable($jointable)) {
            $joincols = CB::getTableColumns($jointable);
            $joinname = CB::getNameTable($joincols);
            $jointablePK = CB::pk($jointable);
            $type = 'select2_datatable';
            $options = [
                'table' => $jointable,
                'field_label' => $joinname,
                'field_value' => $jointablePK,
            ];
        }

        return [$type, $options];
    }
}