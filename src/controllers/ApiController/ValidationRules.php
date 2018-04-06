<?php

namespace crocodicstudio\crudbooster\controllers\ApiController;

use crocodicstudio\crudbooster\helpers\CRUDBooster;

class ValidationRules
{
    /**
     * @param $param
     * @param $typeExcept
     * @param $table
     * @return array
     */
    function make($param, $typeExcept, $table)
    {
        $type = $param['type'];
        $config = $param['config'];

        $format_validation = [];

        if ($param['required']) {
            $format_validation[] = 'required';
        }

        if (in_array($type, ['unique', 'exists'])) {
            $format_validation[] = $this->existOrUnique($config, $type);
        } elseif (in_array($type, ['date_format','digits_between', 'in', 'mimes', 'min', 'max', 'not_in'])) {
            $format_validation[] = $type.':'.$config;
        } elseif (! in_array($type, $typeExcept)) {
            $format_validation[] = $type;
        }

        if ($param['name'] == 'id') {
            $format_validation[] = 'exists:'.CRUDBooster::parseSqlTable($table)['table'].',id';
        }

        return implode('|', $format_validation);
    }

    /**
     * @param $config
     * @param $type
     * @return string
     */
    private function existOrUnique($config, $type)
    {
        $config = explode(',', $config);
        $table_exist = $config[0];
        $table_exist = CRUDBooster::parseSqlTable($table_exist)['table'];
        $field_exist = $config[1];
        $config = ($field_exist) ? $table_exist.','.$field_exist : $table_exist;
        return $type.':'.$config;
    }
}