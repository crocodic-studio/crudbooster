<?php

namespace crocodicstudio\crudbooster\Modules\ModuleGenerator\ControllerGenerator;

use crocodicstudio\crudbooster\helpers\DbInspector;
use crocodicstudio\crudbooster\Modules\ModuleGenerator\FileManipulator;
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
            $input = [
                'label' => self::getLabel($colName),
                'name' => $colName,
                'type' => '',
                'options' => '',
                'required' => true,
                'validation' => '',
                'help' => '',
                'placeholder' => '',
            ];

            if (FieldDetector::isExceptional($colName)) {
                continue;
            }

            $typeData = DbInspector::getFieldTypes($table, $colName);

            list($input['type'], $input['validation'], $input['options']) = self::parseFieldType($typeData);

            if (FieldDetector::isForeignKey($colName)) {
                list($input['type'], $input['options']) = self::handleForeignKey($colName);
            }

            if (substr($colName, 0, 3) == 'is_') {
                $input['type'] = 'radio_dataenum';
                $label = ucwords(substr($colName, 3));
                $input['options'] = [
                    'enum' => ['In '.$label, $label],
                    'value' => [0, 1],
                ];
            }

            if (FieldDetector::isPassword($colName)) {
                $input['type'] = 'password';
                $input['validation'] = 'min:5|max:32|required';
                $input['help'] = cbTrans("text_default_help_password");
            }elseif (FieldDetector::isImage($colName)) {
                $input['type'] = 'upload';
                $input['validation'] = 'required|image';
                $input['help'] = cbTrans('text_default_help_upload');
            }elseif (FieldDetector::isGeographical($colName)) {
                $input['type'] = 'hidden';
                $input['validation'] = 'required|numeric';
            }elseif (FieldDetector::isPhone($colName)) {
                $input['type'] = 'number';
                $input['validation'] = 'required|numeric';
                $input['placeholder'] = cbTrans('text_default_help_number');
            }elseif (FieldDetector::isEmail($colName)) {
                $input['type'] = 'email';
                $input['validation'] = 'require|email|unique:'.$table;
                $input['placeholder'] = cbTrans('text_default_help_email');
            }elseif ($input['type'] == 'text' && FieldDetector::isNameField($colName)) {
                $input['validation'] = 'required|string|min:3|max:70';
                $input['placeholder'] = cbTrans('text_default_help_text');
            }elseif ($input['type'] == 'text' && FieldDetector::isUrlField($colName)) {
                $input['validation'] = 'required|url';
                $input['placeholder'] = cbTrans('text_default_help_url');
            }

            $formArrayString[] = FileManipulator::stringify($input, "            ");
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
     *
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
        if (!Schema::hasTable($jointable)) {
            return ['', ''];
        }
        $options = [
            'table' => $jointable,
            'field_label' => DbInspector::colName(DbInspector::getTableCols($jointable)),
            'field_value' => DbInspector::findPk($jointable),
        ];

        return ['select2_datatable', $options];
    }
}