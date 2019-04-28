<?php namespace crocodicstudio\crudbooster\controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminDashboardController extends BaseController {

    public function getIndex() {
        return view('crudbooster::dashboard');
    }
}
