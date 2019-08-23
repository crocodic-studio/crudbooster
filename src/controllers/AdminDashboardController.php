<?php namespace crocodicstudio\crudbooster\controllers;

use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminDashboardController extends BaseController {

    public function getIndex() {
        $data = [];
        $data['page_title'] = "Dashboard ".cb()->getAppName();
        return view(getThemePath('dashboard'), $data);
    }
}
