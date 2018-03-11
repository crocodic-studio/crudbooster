<?php

namespace crocodicstudio\crudbooster\CBCoreModule\Index;

class OrderAndPaginate
{
    /**
     * @param $filterIsOrderby
     * @param $result
     * @param $limit
     * @param $data
     * @param $table
     * @param $index
     * @return array
     */
    function handle($filterIsOrderby, $result, $limit, $data, $table, $index)
    {
        $orderby = $this->cb->orderby;
        if ($filterIsOrderby !== true) {
            return $this->paginate($result, $limit, $data);
        }

        if (! $orderby) {
            $data['result'] = $result->orderby($index->table.'.'.$index->cb->primary_key, 'desc')->paginate($limit);

            return $data;
        }

        return $this->orderAndPaginate($result, $limit, $data, $table, $orderby);
    }

    /**
     * @param $result
     * @param $limit
     * @param $data
     * @param $table
     * @param $orderby
     * @return mixed
     */
    private function orderAndPaginate($result, $limit, $data, $table, $orderby)
    {
        $x = [];
        if (is_array($orderby)) {
            $x = $orderby;
        } elseif(is_string($orderby)) {
            foreach (explode(";", $orderby) as $by) {
                $by = explode(",", $by);
                $x[$by[0]] = $by[1];
            }
        }

        foreach ($x as $key => $value) {
            $this->orderRows($result, $table, $key, $value);
        }

        return $this->paginate($result, $limit, $data);
    }
    /**
     * @param $result
     * @param $table
     * @param $key
     * @param $value
     */
    private function orderRows($result, $table, $key, $value)
    {
        if (strpos($key, '.')) {
            $table = explode(".", $key)[0];
        }
        $result->orderby($table.'.'.$key, $value);
    }

    /**
     * @param $result
     * @param $limit
     * @param $data
     * @return mixed
     */
    private function paginate($result, $limit, $data)
    {
        $data['result'] = $result->paginate($limit);
        return $data;
    }
}