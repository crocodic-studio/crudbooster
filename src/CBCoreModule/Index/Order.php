<?php

namespace crocodicstudio\crudbooster\CBCoreModule\Index;

class Order
{
    /**
     * @param $query
     * @param $table
     * @param $orderBy
     * @param $primaryKey
     */
    public function handle($query, $table, $orderBy, $primaryKey)
    {
        if (! $orderBy) {
            $query->orderby($table.'.'.$primaryKey, 'desc');
            return;
        }
        if (is_string($orderBy)) {
            $orderBy = $this->normalizeOrderBy($orderBy);
        }

        $this->orderRows($query, $table, $orderBy);
    }

    /**
     * @param $query
     * @param $table
     * @param $orderby
     */
    private function orderRows($query, $table, $orderby)
    {
        foreach ($orderby as $key => $value) {
            if (strpos($key, '.')) {
                $table = explode(".", $key)[0];
            }
            $query->orderby($table.'.'.$key, $value);
        }
    }

    /**
     * @param $orderBy
     * @return array
     */
    private function normalizeOrderBy($orderBy)
    {
        $x = [];
        foreach (explode(";", $orderBy) as $by) {
            $by = explode(",", $by);
            $x[$by[0]] = $by[1];
        }

        return $x;
    }
}