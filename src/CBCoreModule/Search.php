<?php

namespace crocodicstudio\crudbooster\CBCoreModule;

use Illuminate\Support\Facades\DB;

class Search
{
    /**
     * @param $data
     * @param $q
     * @param $id
     *
     * @return mixed
     */
    function searchData($data, $q, $id)
    {
        $data = base64_decode($data);
        $data = json_decode($data, true);
        $fieldValue = $data['field_value'];

        $table = $data['table'];
        $rows = DB::table($table);
        $rows->select($table.'.*');

        if ($data['sql_orderby']) {
            $rows->orderbyRaw($data['sql_orderby']);
        } else {
            $rows->orderby($fieldValue, 'desc');
        }
        if ($data['limit']) {
            $rows->take($data['limit']);
        } else {
            $rows->take(10);
        }

        if ($data['field_label']) {
            $rows->addselect($data['field_label'].' as text');
        }

        if ($fieldValue) {
            $rows->addselect($fieldValue.' as id');
        }

        if ($data['sql_where']) {
            $rows->whereRaw($data['sql_where']);
        }

        if ($q) {
            $rows->where($data['field_label'], 'like', '%'.$q.'%');
        }

        if ($id) {
            $rows->where($fieldValue, $id);
        }
        $items = $rows->get();

        return $items;
    }
}