<?php namespace crocodicstudio\crudbooster\controllers;

use Illuminate\Routing\Controller as BaseController;

class AdminDashboardController extends BaseController {

    public function getIndex() {
        return view('crudbooster::dashboard');
    }
}
