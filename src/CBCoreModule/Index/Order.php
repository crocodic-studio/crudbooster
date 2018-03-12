<?php

namespace crocodicstudio\crudbooster\CBCoreModule\Index;

class Order
{
    /**
     * @param $result
     * @param $table
     * @param $index
     */
    function handle($result, $table, $index)
    {
        $orderby = $this->cb->orderby;
        if (! $orderby) {
            $result->orderby($index->table.'.'.$index->cb->primary_key, 'desc');
            return;
        }
        if (is_string($orderby)) {
            $orderby = $this->normalizeOrderBy($orderby);
        }

        $this->orderRows($result, $table, $orderby);
    }

    /**
     * @param $result
     * @param $table
     * @param $orderby
     */
    private function orderRows($result, $table, $orderby)
    {
        foreach ($orderby as $key => $value) {
            if (strpos($key, '.')) {
                $table = explode(".", $key)[0];
            }
            $result->orderby($table.'.'.$key, $value);
        }
    }

    /**
     * @param $orderby
     * @return array
     */
    private function normalizeOrderBy($orderby)
    {
        $x = [];
        foreach (explode(";", $orderby) as $by) {
            $by = explode(",", $by);
            $x[$by[0]] = $by[1];
        }

        return $x;
    }
}