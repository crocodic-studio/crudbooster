<?php namespace crocodicstudio\crudbooster\controllers;

use crocodicstudio\crudbooster\controllers\Controller;
use crocodicstudio\crudbooster\controllers\Forms\LogsForm;
use crocodicstudio\crudbooster\controllers\Forms\StatisticForm;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\PDF;
use Illuminate\Support\Facades\Excel;

class AdminLogsController extends CBController
{
    public function cbInit()
    {
        $this->table = 'cms_logs';
        $this->title_field = "ipaddress";
        $this->button_bulk_action = true;
        $this->button_export = false;
        $this->button_import = false;
        $this->button_add = false;
        $this->button_edit = false;
        $this->button_delete = true;

        $this->makeColumns();

        $this->form = LogsForm::makeForm();
    }

    private function makeColumns()
    {
        $this->col = [];
        $this->col[] = ["label" => "Time Access", "name" => "created_at"];
        $this->col[] = ["label" => "IP Address", "name" => "ipaddress"];
        $this->col[] = ["label" => "User", "name" => "id_cms_users", "join" => "cms_users,name"];
        $this->col[] = ["label" => "Description", "name" => "description"];
    }
}
