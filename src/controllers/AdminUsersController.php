<?php namespace crocodicstudio\crudbooster\controllers;

use crocodicstudio\crudbooster\controllers\Controller;
use crocodicstudio\crudbooster\controllers\Forms\UsersForm;
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
use CRUDBooster;
use CB;

class AdminUsersController extends CBController
{
    public function cbInit()
    {
        # START CONFIGURATION DO NOT REMOVE THIS LINE
        $this->table = 'cms_users';
        $this->title_field = "name";
        $this->button_action_style = 'button_icon';
        $this->button_import = false;
        $this->button_export = false;
        $this->button_save = true;

        $this->makeColumns();
        $this->form = UsersForm::makeForm();
    }

    public function hookQueryIndex(&$query)
    {
        $query->join('cms_privileges', 'cms_privileges.id', '=', 'id_cms_privileges');
        $query->addSelect('cms_privileges.name as cms_privileges_name');
    }

    public function getProfile()
    {

        $this->button_addmore = false;
        $this->button_cancel = false;
        $this->button_show = false;
        $this->button_add = false;
        $this->button_delete = false;
        $this->hide_form = ['id_cms_privileges'];

        $data['page_title'] = trans("crudbooster.label_button_profile");
        $data['row'] = CRUDBooster::first('cms_users', CRUDBooster::myId());
        $this->cbView('crudbooster::default.form', $data);
    }

    private function makeColumns()
    {
        $this->col = [];
        $this->col[] = ["label" => "Name", "name" => "name"];
        $this->col[] = ["label" => "Email", "name" => "email"];
        $this->col[] = ["label" => "Privilege", "name" => "cms_privileges_name"];
        $this->col[] = ["label" => "Photo", "name" => "photo", "image" => 1];
    }
}
