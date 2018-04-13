<?php namespace crocodicstudio\crudbooster\controllers;

use CRUDBooster;
use Illuminate\Support\Facades\Excel;
use Illuminate\Support\Facades\PDF;

class EmailTemplatesController extends \crocodicstudio\crudbooster\controllers\CBController
{
    public function cbInit()
    {
        $this->table = "cms_email_templates";
        $this->primary_key = "id";
        $this->title_field = "name";
        $this->limit = 20;
        $this->orderby = ["id" => "desc"];
        $this->global_privilege = false;

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

        $this->col = [];
        $this->col[] = ["label" => "Template Name", "name" => "name"];
        $this->col[] = ["label" => "Slug", "name" => "slug"];

        $this->form = [];
        $this->form[] = [
            "label" => "Template Name",
            "name" => "name",
            "type" => "text",
            "required" => true,
            "validation" => "required|min:3|max:255|alpha_spaces",
            "placeholder" => "You can only enter the letter only",
        ];
        $this->form[] = ["label" => "Slug", "type" => "text", "name" => "slug", "required" => true, 'validation' => 'required|unique:cms_email_templates,slug'];
        $this->form[] = ["label" => "Subject", "name" => "subject", "type" => "text", "required" => true, "validation" => "required|min:3|max:255"];
        $this->form[] = ["label" => "Content", "name" => "content", "type" => "wysiwyg", "required" => true, "validation" => "required"];
        $this->form[] = ["label" => "Description", "name" => "description", "type" => "text", "required" => true, "validation" => "required|min:3|max:255"];

        $this->form[] = [
            "label" => "From Name",
            "name" => "from_name",
            "type" => "text",
            "required" => false,
            "width" => "col-sm-6",
            'placeholder' => 'Optional',
        ];
        $this->form[] = [
            "label" => "From Email",
            "name" => "from_email",
            "type" => "email",
            "required" => false,
            "validation" => "email",
            "width" => "col-sm-6",
            'placeholder' => 'Optional',
        ];

        $this->form[] = [
            "label" => "Cc Email",
            "name" => "cc_email",
            "type" => "email",
            "required" => false,
            "validation" => "email",
            'placeholder' => 'Optional',
        ];
    }
    //By the way, you can still create your own method in here... :)

}
