<?php

namespace crocodicstudio\crudbooster\controllers\ApiController;

trait ApiHooks
{
    public function hookBefore(&$postdata)
    {
    }

    public function hookAfter($postdata, &$result)
    {
    }

    public function hookValidate(&$postdata)
    {
    }

    public function hookQuery(&$query)
    {
    }
}