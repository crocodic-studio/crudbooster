<?php

namespace crocodicstudio\crudbooster\controllers\CBController;

trait CbIndexLoader
{
    //public $password_candidate = null;

    //public $date_candidate = null;

    public $columns_table;

    public $orderby = null;

    public $indexStatistic = [];

    public $indexAdditionalView = [];

    public $preIndexHtml = null;

    public $postIndexHtml = null;

    public $showNumbering = false;

    public $index_button = [];

    public $limit = 20;

    public $tableRowColor = [];

    public $buttonTableAction = true;

    public $buttonBulkAction = true;

    public $buttonFilter = true;

    public $buttonShow = true;

    public $sub_module = [];

    public $buttonActionWidth = null;

    public $addaction = [];

    public $buttonDetail = true;

    public $deleteBtn = true;

    public $col = [];

    public $button_selected = [];

    public $button_edit = true;

    public $buttonAdd = true;

    public $button_action_style = 'button_icon';

    protected function cbIndexLoader()
    {
        $this->data['button_edit'] = $this->button_edit;
        $this->columns_table = $this->col;
        $this->data['deleteBtn'] = $this->deleteBtn;
        $this->data['button_selected'] = $this->button_selected;
        $this->data['buttonExport'] = $this->buttonExport;
        $this->data['button_import'] = $this->buttonImport;
        $this->data['buttonFilter'] = $this->buttonFilter;
        $this->data['showNumbering'] = $this->showNumbering;
        $this->data['preIndexHtml'] = $this->preIndexHtml;
        $this->data['postIndexHtml'] = $this->postIndexHtml;
        $this->data['index_statistic'] = $this->indexStatistic;
        $this->data['table_row_color'] = $this->tableRowColor;
        $this->data['buttonBulkAction'] = $this->buttonBulkAction;
        $this->data['buttonTableAction'] = $this->buttonTableAction;
        $this->data['index_additional_view'] = $this->indexAdditionalView;
        $this->data['button_action_width'] = $this->buttonActionWidth;
        $this->data['buttonShow'] = $this->buttonShow;
        $this->data['addaction'] = ($this->show_addaction) ? $this->addaction : null;
        $this->data['buttonDetail'] = $this->buttonDetail;
        $this->data['buttonAdd'] = $this->buttonAdd;
    }

}