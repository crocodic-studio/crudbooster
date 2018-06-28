<?php

namespace Crocodicstudio\Crudbooster\Controllers;

class DashboardController extends CBController
{

    function index()
    {
        // todo: translate dashboard
        return $this->cbView('crudbooster::dashboard', ['page_title' => '<strong>'.cbTrans('text_dashboard').'</strong>']);
    }

    public function cbInit()
    {
        // TODO: Implement cbInit() method.
    }
}