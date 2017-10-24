<?php

namespace crocodicstudio\crudbooster\CBCoreModule;

use Illuminate\Support\Facades\DB;

class Search
{
    private $rows;

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
        $this->rows = DB::table($table);
        $this->rows->select($table.'.*');

        $this->orderRows($data, $fieldValue);

        $this->limitRows($data);

        if ($data['field_label']) {
            $this->rows->addselect($data['field_label'].' as text');
        }

        if ($fieldValue) {
            $this->rows->addselect($fieldValue.' as id');
        }

        $this->filterRow($data, $q, $id, $fieldValue);

        $items = $this->rows->get();

        return $items;
    }

    /**
     * @param $data
     * @param $fieldValue
     */
    private function orderRows($data, $fieldValue)
    {
        if ($data['sql_orderby']) {
            $this->rows->orderbyRaw($data['sql_orderby']);
        } else {
            $this->rows->orderby($fieldValue, 'desc');
        }
    }

    /**
     * @param $data
     */
    private function limitRows($data)
    {
        if ($data['limit']) {
            $this->rows->take($data['limit']);
        } else {
            $this->rows->take(10);
        }
    }

    /**
     * @param $data
     * @param $q
     * @param $id
     * @param $fieldValue
     */
    private function filterRow($data, $q, $id, $fieldValue)
    {
        if ($data['sql_where']) {
            $this->rows->whereRaw($data['sql_where']);
        }

        if ($q) {
            $this->rows->where($data['field_label'], 'like', '%'.$q.'%');
        }

        if ($id) {
            $this->rows->where($fieldValue, $id);
        }
    }
}