<?php

namespace crocodicstudio\crudbooster\CBCoreModule\Index;

class Order
{
    private $ctrl;

    /**
     * Order constructor.
     *
     * @param $ctrl
     */
    public function __construct($ctrl)
    {
        $this->ctrl = $ctrl;
    }

    /**
     * @param $result
     * @param $table
     */
    public function handle($result, $table)
    {
        $orderBy = $this->ctrl->orderby;
        if (! $orderBy) {
            $result->orderby($table.'.'.$this->ctrl->primaryKey, 'desc');
            return;
        }
        if (is_string($orderBy)) {
            $orderBy = $this->normalizeOrderBy($orderBy);
        }

        $this->orderRows($result, $table, $orderBy);
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