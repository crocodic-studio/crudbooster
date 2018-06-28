<?php

namespace Crocodicstudio\Crudbooster\Modules\EmailTemplates;

use Crocodicstudio\Crudbooster\Controllers\CBController;

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
        $this->buttonActionStyle = 'button_icon';
        $this->buttonExport = false;
        $this->buttonImport = false;
    }

    private function setColumns()
    {
        $this->col = [
            ['label' => 'Template Name', 'name' => 'name'],
            ['label' => 'Slug', 'name' => 'slug'],
        ];
    }
    //By the way, you can still create your own method in here... :)

}
