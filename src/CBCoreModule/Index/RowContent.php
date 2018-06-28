<?php

namespace Crocodicstudio\Crudbooster\CBCoreModule\Index;

use Crocodicstudio\Crudbooster\controllers\CBController;
use Crocodicstudio\Crudbooster\Helpers\CRUDBooster;
use Crocodicstudio\Crudbooster\Helpers\DbInspector;

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
     * @param $number
     * @param $columnsTable
     *
     * @return array
     */
    public function calculate($data, $number, $columnsTable)
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
            $rowContent = $this->addActionButtons($row, $rowContent);
            $rowContent = $this->performHookOnRow($rowContent);
            $tableRows[] = $rowContent;
            $number++;
        }

        return $tableRows;
    }

    /**
     * @param $id
     * @return string
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
            $rowCells[$i] = $this->cb->hookRowIndex($i, $v);
        }

        return $rowCells;
    }

    /**
     * @param $row
     * @param $rowCells
     * @return array
     * @throws \Throwable
     */
    private function addActionButtons($row, $rowCells)
    {
        //LISTING INDEX HTML
        $addAction = $this->cb->data['addAction'];

        if (! empty($this->cb->sub_module)) {
            $addAction = $this->_handleSubModules($addAction);
        }

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

    /**
     * @param $addAction
     * @return array
     */
    private function _handleSubModules($addAction)
    {
        foreach ($this->cb->sub_module as $module) {
            $addAction[] = [
                'label' => $module['label'],
                'icon' => $module['button_icon'],
                'url' => $this->subModuleUrl($module, CRUDBooster::parseSqlTable($this->table)['table']),
                'color' => $module['button_color'],
                'showIf' => $module['showIf'],
            ];
        }

        return $addAction;
    }

    /**
     * @param $module
     * @param $parentTable
     * @return string
     */
    private function subModuleUrl($module, $parentTable)
    {
        return CRUDBooster::adminPath($module['path']).'?parent_table='.$parentTable.'&parent_columns='
            .$module['parent_columns'].'&parent_columns_alias='
            .$module['parent_columns_alias'].'&parent_id=['
            .(! isset($module['custom_parent_id']) ? "id" : $module['custom_parent_id'])
            .']&return_url='.urlencode(request()->fullUrl()).'&foreign_key='
            .$module['foreign_key'].'&label='.urlencode($module['label']);
    }

}