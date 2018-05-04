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

        $this->setButtons();

        $this->setColumns();

        $this->form = EmailTemplateForm::makeForm();
    }

    private function setButtons()
    {
        $this->buttonTableAction = true;
        $this->buttonActionStyle = 'button_icon';
        $this->buttonAdd = true;
        $this->deleteBtn = true;
        $this->buttonEdit = true;
        $this->buttonDetail = true;
        $this->buttonShow = true;
        $this->buttonFilter = true;
        $this->buttonExport = false;
        $this->buttonImport = false;
    }

    private function setColumns()
    {
        $this->col = [];
        $this->col[] = ['label' => 'Template Name', 'name' => 'name'];
        $this->col[] = ['label' => 'Slug', 'name' => 'slug'];
    }
    //By the way, you can still create your own method in here... :)

}
