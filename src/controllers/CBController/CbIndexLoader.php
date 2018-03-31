<?php

namespace crocodicstudio\crudbooster\controllers\CBController;

trait CbIndexLoader
{
    public $password_candidate = null;

    public $date_candidate = null;

    public $columns_table;

    public $orderby = null;

    public $index_statistic = [];

    public $index_additional_view = [];

    public $pre_index_html = null;

    public $post_index_html = null;

    public $show_numbering = false;

    public $index_button = [];

    public $limit = 20;

    public $table_row_color = [];

    public $button_table_action = true;

    public $button_bulk_action = true;

    public $button_filter = true;

    public $button_show = true;

    public $sub_module = [];

    public $button_action_width = null;

    public $addaction = [];

    protected function cbIndexLoader()
    {
        $this->data['button_export'] = $this->button_export;
        $this->data['button_import'] = $this->button_import;
        $this->data['button_filter'] = $this->button_filter;
        $this->data['show_numbering'] = $this->show_numbering;
        $this->data['pre_index_html'] = $this->pre_index_html;
        $this->data['post_index_html'] = $this->post_index_html;
        $this->data['index_statistic'] = $this->index_statistic;
        $this->data['table_row_color'] = $this->table_row_color;
        $this->data['button_bulk_action'] = $this->button_bulk_action;
        $this->data['button_table_action'] = $this->button_table_action;
        $this->data['index_additional_view'] = $this->index_additional_view;
        $this->data['button_action_width'] = $this->button_action_width;
        $this->data['button_show'] = $this->button_show;
        $this->data['addaction'] = ($this->show_addaction) ? $this->addaction : null;
    }

}