<?php

namespace crocodicstudio\crudbooster\CBCoreModule;

trait Hooks
{
    public function hookBeforeDelete($id)
    {
    }

    public function hookAfterDelete($id)
    {
    }
    public function hookBeforeEdit(&$arr, $id)
    {
    }

    public function hookAfterEdit($id)
    {
    }

    public function hookAfterAdd($id)
    {
    }

    public function hookBeforeAdd(&$arr)
    {
    }

    public function hookQueryIndex(&$query)
    {
    }

    public function hookRowIndex($index, &$value)
    {
    }
}