<?php

namespace crocodicstudio\crudbooster\CBCoreModule\Index;

use crocodicstudio\crudbooster\controllers\CBController;

class CellContent
{
    private $cb;

    /**
     * HtmlContent constructor.
     *
     * @param $cb
     */
    public function __construct(CBController $cb)
    {
        $this->cb = $cb;
    }

    /**
     * @param $data
     * @param $tablePK
     * @param $number
     * @param $columnsTable
     * @param $table
     * @param $addAction
     *
     * @return array
     */
    public function calculate($data, $tablePK, $number, $columnsTable, $table, $addAction)
    {
        $htmlContents = [];
        foreach ($data['result'] as $row) {
            $htmlContent = [];

            $htmlContent = $this->addCheckBox($tablePK, $row, $htmlContent);
            $htmlContent = $this->addRowNumber($number, $htmlContent);
            $htmlContent = $this->addOtherColumns($columnsTable, $table, $row, $htmlContent);
            $htmlContent = $this->addActionButtons($addAction, $row, $htmlContent);
            $htmlContent = $this->performHookOnRow($htmlContent);
            $htmlContents[] = $htmlContent;
            $number++;
        }

        return $htmlContents;
    }

    /**
     * @param $tablePK
     * @param $row
     * @param $htmlContent
     * @return array
     */
    private function addCheckBox($tablePK, $row, $htmlContent)
    {
        if ($this->cb->buttonBulkAction) {
            $htmlContent[] = "<input type='checkbox' class='checkbox' name='checkbox[]' value='".$row->{$tablePK}."'/>";
        }

        return $htmlContent;
    }

    /**
     * @param $number
     * @param $htmlContent
     * @return array
     */
    private function addRowNumber($number, $htmlContent)
    {
        if ($this->cb->showNumbering) {
            $htmlContent[] = $number.'. ';
        }

        return $htmlContent;
    }
    /**
     * @param $columnsTable
     * @param $table
     * @param $row
     * @param $htmlContent
     * @return array
     */
    private function addOtherColumns($columnsTable, $table, $row, $htmlContent)
    {
        foreach ($columnsTable as $col) {
            if ($col['visible'] === false) {
                continue;
            }
            $htmlContent[] = (new ValueCalculator)->calculate($col, $row, $table, @$row->{$this->cb->titleField});
        }

        return $htmlContent;
    }



    /**
     * @param $htmlContent
     * @return mixed
     */
    private function performHookOnRow($htmlContent)
    {
        foreach ($htmlContent as $i => $v) {
            $this->cb->hookRowIndex($i, $v);
            $htmlContent[$i] = $v;
        }

        return $htmlContent;
    }

    /**
     * @param $addAction
     * @param $row
     * @param $htmlContent
     * @return array
     * @throws \Throwable
     */
    private function addActionButtons($addAction, $row, $htmlContent)
    {
        if (!$this->cb->buttonTableAction) {
            return $htmlContent;
        }
        $button_action_style = $this->cb->button_action_style;
        $button_edit = $this->cb->button_edit;
        $buttonDetail = $this->cb->buttonDetail;
        $deleteBtn = $this->cb->deleteBtn;
        $id = ($row->{$this->cb->primaryKey});

        $data = compact('addAction', 'row', 'id', 'button_action_style', 'parent_field', 'button_edit', 'deleteBtn', 'buttonDetail');
        $htmlContent[] = "<div class='button_action' style='text-align:right'>".view('crudbooster::index.action', $data)->render().'</div>';

        return $htmlContent;
    }

}