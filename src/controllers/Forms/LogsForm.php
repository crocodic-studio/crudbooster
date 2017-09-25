<?php

namespace crocodicstudio\crudbooster\controllers\Forms;

class LogsForm
{
    public static function makeForm()
    {
        $form = [];
        $form[] = ["label" => "Time Access", "name" => "created_at", "readonly" => true];
        $form[] = ["label" => "IP Address", "name" => "ipaddress", "readonly" => true];
        $form[] = ["label" => "User Agent", "name" => "useragent", "readonly" => true];
        $form[] = ["label" => "URL", "name" => "url", "readonly" => true];
        $form[] = ["label" => "User", "name" => "id_cms_users", "type" => "select", "datatable" => "cms_users,name", "readonly" => true];
        $form[] = ["label" => "Description", "name" => "description", "readonly" => true];

        return $form;
    }
}