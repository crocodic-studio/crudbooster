<?php

namespace crocodicstudio\crudbooster\CBCoreModule\Index;

use crocodicstudio\crudbooster\controllers\CBController;
use crocodicstudio\crudbooster\helpers\DbInspector;

class RowContent
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
     * @param $addAction
     *
     * @return array
     */
    public function calculate($data, $number, $columnsTable, $addAction)
    {
        $tableRows = [];
        $tablePK = DbInspector::findPk($this->cb->table);
        foreach ($data['result'] as $row) {
            $rowContent = [];
            if ($this->cb->buttonBulkAction) {
                $rowContent[] = $this->addCheckBox($row->{$tablePK});
            }
            $rowContent = $this->addRowNumber($number, $rowContent);
            $rowContent = $this->addOtherColumns($columnsTable, $row, $rowContent);
            $rowContent = $this->addActionButtons($addAction, $row, $rowContent);
            $rowContent = $this->performHookOnRow($rowContent);
            $tableRows[] = $rowContent;
            $number++;
        }

        return $tableRows;
    }

    /**
     * @param $id
     * @param $htmlContent
     * @return array
     */
    private function addCheckBox($id)
    {
        return "<input type='checkbox' class='checkbox' name='checkbox[]' value='".$id."'/>";
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
     * @param $row
     * @param $rowCells
     * @return array
     */
    private function addOtherColumns($columnsTable, $row, $rowCells)
    {
        foreach ($columnsTable as $col) {
            $rowCells[] = (new ValueCalculator)->calculate($col, $row, @$row->{$this->cb->titleField});
        }

        return $rowCells;
    }



    /**
     * @param $rowCells
     * @return mixed
     */
    private function performHookOnRow($rowCells)
    {
        foreach ($rowCells as $i => $v) {
            $this->cb->hookRowIndex($i, $v);
            $rowCells[$i] = $v;
        }

        return $rowCells;
    }

    /**
     * @param $addAction
     * @param $row
     * @param $rowCells
     * @return array
     * @throws \Throwable
     */
    private function addActionButtons($addAction, $row, $rowCells)
    {
        if (!$this->cb->showButtonsOnIndexRows) {
            return $rowCells;
        }
        $buttonActionStyle = $this->cb->buttonActionStyle;
        $buttonEdit = $this->cb->buttonEdit;
        $buttonDetail = $this->cb->buttonDetail;
        $deleteBtn = $this->cb->deleteBtn;
        $id = ($row->{$this->cb->primaryKey});

        $data = compact('addAction', 'row', 'id', 'buttonActionStyle', 'parent_field', 'buttonEdit', 'deleteBtn', 'buttonDetail');
        $rowCells[] = "<div class='button_action' style='text-align:right'>".view('crudbooster::index.action', $data)->render().'</div>';

        return $rowCells;
    }

}