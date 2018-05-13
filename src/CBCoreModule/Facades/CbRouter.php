<?php

namespace crocodicstudio\crudbooster\CBCoreModule\Facades;

use Illuminate\Support\Facades\Facade;

class CbRouter extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \crocodicstudio\crudbooster\CBCoreModule\CbRouter::class;
    }
}




