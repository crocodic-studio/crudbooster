<?php

namespace App\Http\Controllers;

use DB;
use Session;
use Request;
use CRUDBooster;
use CB;

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