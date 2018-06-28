<?php

namespace Crocodicstudio\Crudbooster\Controllers\ApiController;

trait ApiHooks
{
    public function hookBefore($data)
    {
        return $data;
    }

    public function hookAfter($data, $result)
    {
        return $result;
    }

    public function hookValidate($data)
    {
        return $data;
    }

    public function hookQuery($query)
    {
        return $query;
    }
}