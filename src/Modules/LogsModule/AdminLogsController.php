<?php

namespace crocodicstudio\crudbooster\Modules\LogsModule;

use crocodicstudio\crudbooster\controllers\CBController;

class AdminLogsController extends CBController
{
    public function cbInit()
    {
        $this->table = 'cms_logs';
        $this->title_field = 'ipaddress';

        $this->setButtons();
        $this->makeColumns();

        $this->form = LogsForm::makeForm();
    }

    private function makeColumns()
    {
        $this->col = [];
        $this->col[] = ['label' => 'Time Access', 'name' => 'created_at'];
        $this->col[] = ['label' => 'IP Address', 'name' => 'ipaddress'];
        $this->col[] = ['label' => 'User', 'name' => 'id_cms_users', 'join' => 'cms_users,name'];
        $this->col[] = ['label' => 'Description', 'name' => 'description'];
    }

    private function setButtons()
    {
        $this->button_bulk_action = true;
        $this->button_export = false;
        $this->button_import = false;
        $this->button_add = false;
        $this->button_edit = false;
        $this->deleteBtn = true;
    }
}
