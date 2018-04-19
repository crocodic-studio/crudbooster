<?php

namespace crocodicstudio\crudbooster\Modules\EmailTemplates;

use crocodicstudio\crudbooster\controllers\CBController;
use crocodicstudio\crudbooster\helpers\CRUDBooster;

class AdminEmailTemplatesController extends CBController
{
    public function cbInit()
    {
        $this->table = 'cms_email_templates';
        $this->titleField = 'name';
        $this->limit = 20;
        $this->orderby = ['id' => 'desc'];
        $this->global_privilege = false;

        $this->setButtons();

        $this->setColumns();

        $this->form = EmailTemplateForm::makeForm();
    }

    private function setButtons()
    {
        $this->buttonTableAction = true;
        $this->button_action_style = 'button_icon';
        $this->buttonAdd = true;
        $this->deleteBtn = true;
        $this->button_edit = true;
        $this->buttonDetail = true;
        $this->buttonShow = true;
        $this->button_filter = true;
        $this->buttonExport = false;
        $this->button_import = false;
    }

    private function setColumns()
    {
        $this->col = [];
        $this->col[] = ['label' => 'Template Name', 'name' => 'name'];
        $this->col[] = ['label' => 'Slug', 'name' => 'slug'];
    }
    //By the way, you can still create your own method in here... :)

}
