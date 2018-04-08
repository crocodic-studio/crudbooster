<?php

namespace crocodicstudio\crudbooster\Modules\ModuleGenerator\ControllerGenerator;

use crocodicstudio\crudbooster\helpers\DbInspector;
use crocodicstudio\crudbooster\Modules\ModuleGenerator\FileManipulator;
use Illuminate\Support\Facades\Schema;

class FormConfigGenerator
{
    /**
     * @param $table
     * @param $columns
     * @return array
     */
    static function generateFormConfig($table, $columns)
    {
        $formArrayString = [];
        foreach ($columns as $i => $colName) {
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

            $typeData = \Schema::getColumnType($table, $colName);

            $input = array_merge($input, self::parseFieldType($typeData));

            if (FieldDetector::isForeignKey($colName)) {
                list($input['type'], $input['options']) = self::handleForeignKey($colName);
            } elseif (substr($colName, 0, 3) == 'is_') {
                $input['type'] = 'radio_dataenum';
                $label = ucwords(substr($colName, 3));
                $input['options'] = [
                    'enum' => ['In '.$label, $label],
                    'value' => [0, 1],
                ];
            }

            $map = [
                'isPassword' => [
                    'type' => 'password',
                    'validation' => 'min:5|max:32|required',
                    'help' => cbTrans("text_default_help_password"),
                ],
                'isImage' => [
                    'type' => 'upload',
                    'validation' => 'required|image',
                    'help' => cbTrans('text_default_help_upload'),
                ],
                'isGeographical' => [
                    'type' => 'hidden',
                    'validation' => 'required|numeric',
                ],
                'isPhone' => [
                    'type' => 'number',
                    'validation' => 'required|numeric',
                    'placeholder' => cbTrans('text_default_help_number'),
                ],
                'isEmail' => [
                    'type' => 'email',
                    'validation' => 'require|email|unique:'.$table,
                    'placeholder' => cbTrans('text_default_help_email'),
                ],
                'isNameField' => [
                    'type' => 'text',
                    'validation' => 'required|string|min:3|max:70',
                    'placeholder' => cbTrans('text_default_help_text'),
                ],
                'isUrlField' => [
                    'type' => 'text',
                    'validation' => 'required|url',
                    'placeholder' => cbTrans('text_default_help_url'),
                ],
            ];

            $props = array_get($map, FieldDetector::detect($colName), null);
            unset($map);
            if($props !== null){
                $input = array_merge($input, $props);
            }

            $formArrayString[] = FileManipulator::stringify($input, str_repeat(' ', 12));
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
        // if matched a key, overrides $typeData to corresponding values
        $typeData = array_get([
            'longtext' => 'text',
            'integer' => 'int',
            'timestamp' => 'datetime',
        ], $typeData, $typeData);

        $default = ["text", "min:1|max:255", []];

        $arr = array_get([
            'string' => $default,
            'text' => ['textarea', "string|min:5", []],
            'date' => ['date', "date", ['php_format' => 'M, d Y', 'datepicker_format' => 'M, dd YYYY',]],
            'datetime' => ['datetime', "date_format:Y-m-d H:i:s", ['php_format' => 'M, d Y H:i',]],
            'time' => ['time', 'date_format:H:i:s', []],
            'double' => ['money', "integer|min:0", []],
            'int' => ['number', 'integer|min:0', []],
        ], $typeData, $default);

        return [
            'type' => $arr[0],
            'validation' => $arr[1],
            'options' => $arr[2],
        ];
    }

    /**
     * @param $field
     * @return array
     */
    private static function handleForeignKey($field)
    {
        $jointable = str_replace(['id_', '_id'], '', $field);
        if (! Schema::hasTable($jointable)) {
            return ['', ''];
        }
        $options = [
            'table' => $jointable,
            'field_label' => DbInspector::colName(\Schema::getColumnListing($jointable)),
            'field_value' => DbInspector::findPk($jointable),
        ];

        return ['select2_datatable', $options];
    }
}