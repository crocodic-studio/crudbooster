<?php namespace App\Http\Controllers;

use Session;
use Request;
use DB;
use Illuminate\Routing\Controller as BaseController;

class AdminDashboardController extends BaseController {

    public function getIndex()
    {
        return view('crudbooster::home');
    }
}
