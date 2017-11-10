<?php

namespace App\Http\Controllers;

use crocodicstudio\crudbooster\controllers\CBController;
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
        # END CONFIGURATION DO NOT REMOVE THIS LINE

        # START COLUMNS DO NOT REMOVE THIS LINE
        $this->col = [];
        $this->col[] = ["label" => "Name", "name" => "name"];
        $this->col[] = ["label" => "Email", "name" => "email"];
        $this->col[] = ["label" => "Privilege", "name" => "cms_privileges_name"];
        $this->col[] = ["label" => "Photo", "name" => "photo", "image" => 1];
        # END COLUMNS DO NOT REMOVE THIS LINE

        # START FORM DO NOT REMOVE THIS LINE
        $this->form = [];
        $this->form[] = ["label" => "Name", "name" => "name", 'required' => true, 'validation' => 'required|alpha_spaces|min:3'];
        $this->form[] = [
            "label" => "Email",
            "name" => "email",
            'required' => true,
            'type' => 'email',
            'validation' => 'required|email|unique:cms_users,email,'.CRUDBooster::getCurrentId(),
        ];
        $this->form[] = [
            "label" => "Photo",
            "name" => "photo",
            "type" => "upload",
            "help" => "Recommended resolution is 200x200px",
            'required' => true,
            'validation' => 'required|image|max:1000',
        ];
        $this->form[] = [
            "label" => "Privilege",
            "name" => "id_cms_privileges",
            "type" => "select_datatable",
            "options" => ["table" => "cms_privileges", "field_value" => "id", "field_label" => "name"],
            'required' => true,
        ];
        $this->form[] = ["label" => "Password", "name" => "password", "type" => "password", "help" => "Please leave empty if not change"];
        # END FORM DO NOT REMOVE THIS LINE

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
}
