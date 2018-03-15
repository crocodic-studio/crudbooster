<?php

namespace crocodicstudio\crudbooster\controllers\ApiController;

trait ApiHooks
{
    public function hook_before(&$postdata)
    {
    }

    public function hook_after($postdata, &$result)
    {
    }

    public function hook_validate(&$postdata)
    {
    }

    public function hook_query(&$query)
    {
    }
}