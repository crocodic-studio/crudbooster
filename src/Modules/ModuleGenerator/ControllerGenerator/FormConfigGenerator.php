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
    public static function generateFormConfig($table, $columns)
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

            $props = array_get(DefaultFormConfigs::defaultConfigForFields($table), FieldDetector::detect($colName), null);

            if($props !== null){
                $input = array_merge($input, $props);
            }

            $indent = str_repeat(' ', 8);
            $formArrayString[] = FileManipulator::stringify($input, $indent);
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