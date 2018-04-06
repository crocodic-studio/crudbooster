<?php

namespace crocodicstudio\crudbooster\CBCoreModule\Index;

use crocodicstudio\crudbooster\helpers\CRUDBooster;

class ViewHelpers
{
    public static function urlFilterColumn($key, $type, $value = '', $singleSorting = true)
    {
        $params = request()->all();
        $mainpath = trim(CRUDBooster::mainpath(), '/');

        if ($params['filter_column'] && $singleSorting) {
            foreach ($params['filter_column'] as $k => $filter) {
                foreach ($filter as $t => $val) {
                    if ($t == 'sorting') {
                        unset($params['filter_column'][$k]['sorting']);
                    }
                }
            }
        }

        $params['filter_column'][$key][$type] = $value;

        if (isset($params)) {
            return $mainpath.'?'.http_build_query($params);
        }
        return $mainpath.'?filter_column['.$key.']['.$type.']='.$value;

    }
}