<?php

namespace App\Http\Controllers;

use DB;
use Session;
use Request;
use crocodicstudio\crudbooster\helpers\CRUDBooster;

class CBHook extends Controller
{
    /*
    | --------------------------------------
    | Please note that you should re-login to see the session work
    | --------------------------------------
    |
    */
    public function afterLogin()
    {

    }
}