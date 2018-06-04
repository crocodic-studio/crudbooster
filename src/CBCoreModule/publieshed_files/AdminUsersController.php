<?php

namespace App\Http\Controllers;

use crocodicstudio\crudbooster\controllers\CBController;
use crocodicstudio\crudbooster\helpers\CRUDBooster;

class AdminUsersController extends CBController
{
    public function cbInit()
    {
        $this->makeConfigs();
        $this->makeCols();
        $this->makeForm();
    }

    public function hookQueryIndex($query)
    {
        $query->join('cms_roles', 'cms_roles.id', '=', 'cms_roles_id');
        $query->addSelect('cms_roles.name as cms_roles_name');
    }

    public function getProfile()
    {
        $this->genericLoader();
        $this->cbFormLoader();
        $this->button_addmore = false;
        $this->buttonCancel = false;
        $this->buttonShow = false;
        $this->buttonAdd = false;
        $this->deleteBtn = false;
        $this->hide_form = ['cms_roles_id'];

        session()->put('current_row_id', auth('cbAdmin')->id());

        $this->data['return_url'] = request()->fullUrl();

        $data = [
            'page_title' => cbTrans("label_button_profile"),
            'row' => cbUser(),
        ];
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
            'name' => "cms_roles_id",
            'type' => "select_datatable",
            "options" => ["table" => "cms_roles", "field_value" => "id", "field_label" => 'name'],
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
        $this->col[] = ['label' => "Privilege", 'name' => "cms_roles_name"];
        $this->col[] = ['label' => "Photo", 'name' => "photo", "image" => 1];
        # END COLUMNS DO NOT REMOVE THIS LINE
    }

    private function makeConfigs()
    {
        # START CONFIGURATION DO NOT REMOVE THIS LINE
        $this->table = 'cms_users';
        $this->titleField = 'name';
        $this->buttonActionStyle = 'button_icon';
        $this->buttonImport = false;
        $this->buttonExport = false;
        $this->button_save = true;
        # END CONFIGURATION DO NOT REMOVE THIS LINE
    }
}
