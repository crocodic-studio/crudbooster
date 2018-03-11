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
            $result = $result->orderby($index->table.'.'.$index->cb->primary_key, 'desc');
            return $this->paginate($result, $limit, $data);
        }

        $orderby = $this->normalizeOrderBy($orderby);
        $this->orderRows($result, $table, $orderby);
        return $this->paginate($result, $limit, $data);
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

    /**
     * @param $orderby
     * @return array
     */
    private function normalizeOrderBy($orderby)
    {
        $x = [];
        if (is_array($orderby)) {
            $x = $orderby;
        } elseif (is_string($orderby)) {
            foreach (explode(";", $orderby) as $by) {
                $by = explode(",", $by);
                $x[$by[0]] = $by[1];
            }
        }

        return $x;
    }
}