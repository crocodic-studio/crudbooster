<?php

namespace crocodicstudio\crudbooster\CBCoreModule;

use crocodicstudio\crudbooster\controllers\CBController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\PDF;
use CRUDBooster;
use CB;
use Schema;

class DataSaver
{
    private $Cb;

    public function insert(CBController $cbCtrl)
    {
        $this->Cb = $cbCtrl;
        $this->Cb->arr[$cbCtrl->primary_key] = $id = $this->Cb->table()->insertGetId($this->Cb->arr);

        //Looping Data Input Again After Insert
        foreach ($this->Cb->data_inputan as $row) {
            $name = $row['name'];
            if (! $name) {
                continue;
            }

            $inputdata = request($name);

            //Insert Data Checkbox if Type Datatable
            if ($row['type'] == 'checkbox' && $row['relationship_table']) {
                $this->_handleCheckbox($row, $id, $inputdata);
            }

            if ($row['type'] == 'select2' && $row['relationship_table']) {
                $this->_updateRelations($row, $id, $inputdata);
            }

            if ($row['type'] == 'child') {
                $this->_updateChildTable($row, $id);
            }
        }
    }

    public function update($id, CBController $cbCtrl)
    {
        $this->Cb = $cbCtrl;
        $cbCtrl->findRow($id)->update($cbCtrl->arr);
        //Looping Data Input Again After Insert
        $this->insertIntoRelatedTables($id);
    }

    /**
     * @param $id
     */
    private function insertIntoRelatedTables($id)
    {
        foreach ($this->Cb->data_inputan as $row) {
            $name = $row['name'];
            if (! $name) {
                continue;
            }

            $inputData = request($name);

            //Insert Data Checkbox if Type Datatable
            if ($row['type'] == 'checkbox' && $row['relationship_table']) {
                $this->_handleCheckbox($row, $id, $inputData);
            }

            if ($row['type'] == 'select2' && $row['relationship_table']) {
                $this->_updateRelations($row, $id, $inputData);
            }

            if ($row['type'] == 'child') {
                $this->insertChildTable($id, $row);
            }
        }
    }

    /**
     * @param $row
     * @param $id
     * @return string
     */
    private function _updateChildTable($row, $id)
    {
        $name = str_slug($row['label'], '');
        $columns = $row['columns'];

        $fk = $row['foreign_key'];
        $countInput = count(request($name.'-'.$columns[0]['name'])) - 1;
        $childArray = [];
        for ($i = 0; $i <= $countInput; $i++) {
            $column_data = [];
            $column_data[$fk] = $id;
            foreach ($columns as $col) {
                $colName = $col['name'];
                $column_data[$colName] = request($name.'-'.$colName)[$i];
            }
            $childArray[] = $column_data;
        }

        $childTable = CRUDBooster::parseSqlTable($row['table'])['table'];
        DB::table($childTable)->insert($childArray);

        return $name;
    }

    /**
     * @param $row
     * @param $id
     * @param $inputData
     * @return array
     */
    private function _updateRelations($row, $id, $inputData)
    {
        $dataTable = explode(",", $row['datatable'])[0];
        $pivotTable = $row['relationship_table'];

        $foreignKey2 = CRUDBooster::getForeignKey($dataTable, $pivotTable);
        $foreignKey = CRUDBooster::getForeignKey($this->Cb->table, $pivotTable);
        DB::table($pivotTable)->where($foreignKey, $id)->delete();

        if (!$inputData) {
            return null;
        }

        foreach ($inputData as $inputId) {
            DB::table($pivotTable)->insert([
                $foreignKey => $id,
                $foreignKey2 => $inputId,
            ]);
        }
    }

    /**
     * @param $row
     * @param $id
     * @param $inputdata
     * @return null
     */
    private function _handleCheckbox($row, $id, $inputdata)
    {
        $pivotTable = $row['relationship_table'];
        $dataTable = explode(",", $row['datatable'])[0];

        $foreignKey2 = CRUDBooster::getForeignKey($dataTable, $pivotTable);
        $foreignKey = CRUDBooster::getForeignKey($this->Cb->table, $pivotTable);
        DB::table($pivotTable)->where($foreignKey, $id)->delete();

        if (! $inputdata) {
            return null;
        }
        foreach ($inputdata as $inputId) {
            DB::table($pivotTable)->insert([
                $foreignKey => $id,
                $foreignKey2 => $inputId,
            ]);
        }
    }

    /**
     * @param $id
     * @param $row
     * @return string
     */
    private function insertChildTable($id, $row)
    {
        $name = str_slug($row['label'], '');
        $columns = $row['columns'];
        $child_array = [];

        $childtable = CRUDBooster::parseSqlTable($row['table'])['table'];
        $fk = $row['foreign_key'];

        DB::table($childtable)->where($fk, $id)->delete();

        $countInput = count(request($name.'-'.$columns[0]['name'])) - 1;
        for ($i = 0; $i <= $countInput; $i++) {
            $column_data = [];
            $column_data[$fk] = $id;
            foreach ($columns as $col) {
                $colname = $col['name'];
                $column_data[$colname] = request($name.'-'.$colname)[$i];
            }
            $child_array[] = $column_data;
        }

        DB::table($childtable)->insert(array_reverse($child_array));
    }
}