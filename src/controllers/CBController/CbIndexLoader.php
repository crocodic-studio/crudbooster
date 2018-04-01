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

    public $button_detail = true;

    protected function cbIndexLoader()
    {
        $this->data['button_export'] = $this->buttonExport;
        $this->data['button_import'] = $this->buttonImport;
        $this->data['button_filter'] = $this->buttonFilter;
        $this->data['show_numbering'] = $this->showNumbering;
        $this->data['pre_index_html'] = $this->preIndexHtml;
        $this->data['post_index_html'] = $this->postIndexHtml;
        $this->data['index_statistic'] = $this->indexStatistic;
        $this->data['table_row_color'] = $this->tableRowColor;
        $this->data['button_bulk_action'] = $this->buttonBulkAction;
        $this->data['button_table_action'] = $this->buttonTableAction;
        $this->data['index_additional_view'] = $this->indexAdditionalView;
        $this->data['button_action_width'] = $this->buttonActionWidth;
        $this->data['button_show'] = $this->buttonShow;
        $this->data['addaction'] = ($this->show_addaction) ? $this->addaction : null;
        $this->data['button_detail'] = $this->button_detail;
    }

}