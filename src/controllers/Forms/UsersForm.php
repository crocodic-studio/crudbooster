<?php

namespace crocodicstudio\crudbooster\controllers\Forms;

class UsersForm
{
    public static function makeForm()
    {
        $form = [];
        $form[] = ["label" => "Name", "name" => "name", 'required' => true, 'validation' => 'required|alpha_spaces|min:3'];
        $form[] = [
            "label" => "Email",
            "name" => "email",
            'required' => true,
            'type' => 'email',
            'validation' => 'required|email|unique:cms_users,email,'.\CRUDBooster::getCurrentId(),
        ];
        $form[] = [
            "label" => "Photo",
            "name" => "photo",
            "type" => "upload",
            "help" => "Recommended resolution is 200x200px",
            'required' => true,
            'validation' => 'required|image|max:1000',
        ];
        $form[] = [
            "label" => "Privilege",
            "name" => "id_cms_privileges",
            "type" => "select_datatable",
            "options" => ["table" => "cms_privileges", "field_value" => "id", "field_label" => "name"],
            'required' => true,
        ];
        $form[] = ["label" => "Password", "name" => "password", "type" => "password", "help" => "Please leave empty if not change"];

        return $form;
    }
}