<?php

namespace Crocodicstudio\Crudbooster\Controllers\CBController;

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

    public $indexButton = [];

    public $limit = 20;

    public $tableRowColor = [];

    public $showButtonsOnIndexRows = true;

    public $buttonBulkAction = true;

    public $buttonFilter = true;

    public $buttonShow = true;

    public $sub_module = [];

    public $buttonActionWidth = null;

    public $addAction = [];

    public $showAddAction = true;

    public $buttonDetail = true;

    public $deleteBtn = true;

    public $col = [];

    public $buttonSelected = [];

    public $buttonEdit = true;

    public $buttonAdd = true;

    public $buttonActionStyle = 'button_icon';

    public $buttonExport = true;

    public $buttonImport = true;

    public $indexReturn = false; //for export

    protected function cbIndexLoader()
    {
        $this->data['buttonEdit'] = $this->buttonEdit;
        $this->columns_table = $this->col;
        $this->data['deleteBtn'] = $this->deleteBtn;
        $this->data['buttonSelected'] = $this->buttonSelected;
        $this->data['buttonExport'] = $this->buttonExport;
        $this->data['buttonImport'] = $this->buttonImport;
        $this->data['buttonFilter'] = $this->buttonFilter;
        $this->data['showNumbering'] = $this->showNumbering;
        $this->data['preIndexHtml'] = $this->preIndexHtml;
        $this->data['postIndexHtml'] = $this->postIndexHtml;
        $this->data['index_statistic'] = $this->indexStatistic;
        $this->data['table_row_color'] = $this->tableRowColor;
        $this->data['buttonBulkAction'] = $this->buttonBulkAction;
        $this->data['showButtonsOnIndexRows'] = $this->showButtonsOnIndexRows;
        $this->data['index_additional_view'] = $this->indexAdditionalView;
        $this->data['button_action_width'] = $this->buttonActionWidth;
        $this->data['buttonShow'] = $this->buttonShow;
        $this->data['addAction'] = ($this->showAddAction) ? $this->addAction : null;
        $this->data['buttonDetail'] = $this->buttonDetail;
        $this->data['buttonAdd'] = $this->buttonAdd;
    }

}