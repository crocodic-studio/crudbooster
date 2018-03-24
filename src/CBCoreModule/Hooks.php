<?php

namespace crocodicstudio\crudbooster\CBCoreModule;

trait Hooks
{
    public function hook_before_delete($id)
    {
    }

    public function hook_after_Delete($id)
    {
    }

    public function hook_before_edit(&$arr, $id)
    {
    }

    public function hook_after_edit($id)
    {
    }

    public function hook_after_add($id)
    {
    }

    public function hook_before_add(&$arr)
    {
    }

    public function hook_query_index(&$query)
    {
    }

    public function hook_row_index($index, &$value)
    {
    }
}