<?php

namespace Crocodicstudio\Crudbooster\CBCoreModule;

trait Hooks
{
    public function hookBeforeDelete($idsArray)
    {
        return $idsArray;
    }

    public function hookAfterDelete($id)
    {
        return $id;
    }

    public function hookBeforeEdit($arr, $id)
    {
        return $arr;
    }

    public function hookAfterEdit($id)
    {
        return $id;
    }

    public function hookAfterAdd($id)
    {
        return $id;
    }

    public function hookBeforeAdd($arr)
    {
        return $arr;
    }

    public function hookQueryIndex($query)
    {
        return $query;
    }

    public function hookRowIndex($index, $value)
    {
        return $value;
    }
}