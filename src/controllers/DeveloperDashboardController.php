<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 4/25/2019
 * Time: 9:28 PM
 */

namespace crocodicstudio\crudbooster\controllers;


class DeveloperDashboardController extends Controller
{

    public function getIndex() {
        $data = [];
        $data['page_title'] = "Dashboard";
        return view('crudbooster::dev_layouts.modules.dashboard',$data);
    }
    

}