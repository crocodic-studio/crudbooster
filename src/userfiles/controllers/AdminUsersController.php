<?php

namespace App\Http\Controllers;

use crocodicstudio\crudbooster\controllers\CBController;
use crocodicstudio\crudbooster\helpers\CRUDBooster, CB;
use Illuminate\Support\Facades\Request;

class AdminUsersController extends CBController
{
    public function cbInit()
    {
        $this->makeConfigs();
        $this->makeCols();
        $this->makeForm();
    }

    public function hookQueryIndex(&$query)
    {
        $query->join('cms_privileges', 'cms_privileges.id', '=', 'id_cms_privileges');
        $query->addSelect('cms_privileges.name as cms_privileges_name');
    }

    public function getProfile()
    {
        $this->genericLoader();
        $this->cbFormLoader();
        $this->button_addmore = false;
        $this->button_cancel = false;
        $this->buttonShow = false;
        $this->buttonAdd = false;
        $this->deleteBtn = false;
        $this->hide_form = ['id_cms_privileges'];

        session()->put('current_row_id', CRUDBooster::myId());
        $this->data['return_url'] = Request::fullUrl();

        $data['page_title'] = cbTrans("label_button_profile");
        $data['row'] = CRUDBooster::first('cms_users', CRUDBooster::myId());
        return $this->cbView('crudbooster::form.form', $data);
    }

    private function makeForm()
    {
        # START FORM DO NOT REMOVE THIS LINE
        $this->form = [];
        $this->form[] = ['label' => 'name', 'name' => 'name', 'required' => true, 'validation' => 'required|alpha_spaces|min:3'];
        $this->form[] = [
            'label' => "Email",
            'name' => "email",
            'required' => true,
            'type' => 'email',
            'validation' => 'required|email|unique:cms_users,email,'.CRUDBooster::getCurrentId(),
        ];
        $this->form[] = [
            'label' => "Photo",
            'name' => "photo",
            'type' => "upload",
            "help" => "Recommended resolution is 200x200px",
            'required' => true,
            'validation' => 'required|image|max:1000',
        ];
        $this->form[] = [
            'label' => "Privilege",
            'name' => "id_cms_privileges",
            'type' => "select_datatable",
            "options" => ["table" => "cms_privileges", "field_value" => "id", "field_label" => 'name'],
            'required' => true,
        ];
        $this->form[] = ['label' => "Password", 'name' => "password", 'type' => "password", "help" => "Please leave empty if not change"];
        # END FORM DO NOT REMOVE THIS LINE
    }

    private function makeCols()
    {
        # START COLUMNS DO NOT REMOVE THIS LINE
        $this->col = [];
        $this->col[] = ['label' => 'name', 'name' => 'name'];
        $this->col[] = ['label' => "Email", 'name' => "email"];
        $this->col[] = ['label' => "Privilege", 'name' => "cms_privileges_name"];
        $this->col[] = ['label' => "Photo", 'name' => "photo", "image" => 1];
        # END COLUMNS DO NOT REMOVE THIS LINE
    }

    private function makeConfigs()
    {
        # START CONFIGURATION DO NOT REMOVE THIS LINE
        $this->table = 'cms_users';
        $this->titleField = 'name';
        $this->button_action_style = 'button_icon';
        $this->button_import = false;
        $this->buttonExport = false;
        $this->button_save = true;
        # END CONFIGURATION DO NOT REMOVE THIS LINE
    }
}
