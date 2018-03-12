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
            if (in_array($row['type'], ['checkbox', 'select2']) && $row['relationship_table']) {
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
            if (in_array($row['type'] , ['select2', 'checkbox']) && $row['relationship_table']) {
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
        list($pivotTable, $foreignKey2, $foreignKey) = $this->deleteFromPivot($row, $id);

        if (!$inputData) {
            return null;
        }

        $this->insertIntoPivot($id, $inputData, $pivotTable, $foreignKey, $foreignKey2);
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
        $childArray = [];

        $childTable = CRUDBooster::parseSqlTable($row['table'])['table'];
        $fk = $row['foreign_key'];

        DB::table($childTable)->where($fk, $id)->delete();

        $countInput = count(request($name.'-'.$columns[0]['name'])) - 1;
        for ($i = 0; $i <= $countInput; $i++) {
            $columnData = [];
            $columnData[$fk] = $id;
            foreach ($columns as $col) {
                $colname = $col['name'];
                $columnData[$colname] = request($name.'-'.$colname)[$i];
            }
            $childArray[] = $columnData;
        }

        DB::table($childTable)->insert(array_reverse($childArray));
    }

    /**
     * @param $id
     * @param $inputData
     * @param $pivotTable
     * @param $foreignKey
     * @param $foreignKey2
     */
    private function insertIntoPivot($id, $inputData, $pivotTable, $foreignKey, $foreignKey2)
    {
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
     * @return array
     */
    private function deleteFromPivot($row, $id)
    {
        $pivotTable = $row['relationship_table'];
        $dataTable = explode(",", $row['datatable'])[0];

        $foreignKey2 = CRUDBooster::getForeignKey($dataTable, $pivotTable);
        $foreignKey = CRUDBooster::getForeignKey($this->Cb->table, $pivotTable);
        DB::table($pivotTable)->where($foreignKey, $id)->delete();

        return [$pivotTable, $foreignKey2, $foreignKey];
    }
}