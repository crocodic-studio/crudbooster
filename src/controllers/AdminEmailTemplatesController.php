<?php

namespace crocodicstudio\crudbooster\controllers;

use crocodicstudio\crudbooster\controllers\Forms\EmailTemplateForm;
use CRUDBooster;

class AdminEmailTemplatesController extends CBController
{
    public function cbInit()
    {
        $this->table = "cms_email_templates";
        $this->title_field = "name";
        $this->limit = 20;
        $this->orderby = ["id" => "desc"];
        $this->global_privilege = false;

        $this->setButtons();

        $this->col = [];
        $this->col[] = ["label" => "Template Name", "name" => "name"];
        $this->col[] = ["label" => "Slug", "name" => "slug"];

        $this->form = EmailTemplateForm::makeForm();
    }

    private function setButtons()
    {
        $this->button_table_action = true;
        $this->button_action_style = "button_icon";
        $this->button_add = true;
        $this->button_delete = true;
        $this->button_edit = true;
        $this->button_detail = true;
        $this->button_show = true;
        $this->button_filter = true;
        $this->button_export = false;
        $this->button_import = false;
    }
    //By the way, you can still create your own method in here... :)

}
