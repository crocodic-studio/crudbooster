<?php

namespace crocodicstudio\crudbooster\CBCoreModule;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\PDF;
use CRUDBooster;
use CB;
use Schema;

class RelationHandler
{
    private $Cb;

    public function save($table, $id, $data)
    {
        $this->table = $table;
        //Looping Data Input Again After Insert
        foreach ($data as $row) {
            $name = $row['name'];
            if (! $name) {
                continue;
            }

            $inputData = request($name);

            //Insert Data Checkbox if Type Datatable
            if ($this->isRelationship($row)) {
                $this->updateRelations($row, $id, $inputData);
            }

            if ($row['type'] == 'child') {
                $this->insertChildTable($id, $row);
            }
        }
    }

    /**
     * @param $row
     * @return bool
     */
    private function isRelationship($row)
    {
        return in_array($row['type'], ['checkbox', 'select2']) && $row['relationship_table'];
    }

    /**
     * @param $row
     * @param $id
     * @param $inputData
     * @return array
     */
    private function updateRelations($row, $id, $inputData)
    {
        list($pivotTable, $foreignKey2, $foreignKey) = $this->deleteFromPivot($row, $id);

        if (! $inputData) {
            return null;
        }

        $this->insertIntoPivot($id, $inputData, $pivotTable, $foreignKey, $foreignKey2);
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
            DB::table($pivotTable)->insert([$foreignKey => $id, $foreignKey2 => $inputId]);
        }
    }

    /**
     * @param $id
     * @param $row
     * @return string
     */
    private function insertChildTable($id, $row)
    {
        $fk = $row['foreign_key'];
        $columns = $row['columns'];
        $name = str_slug($row['label'], '');
        $childArray = $this->childArray($id, $name, $columns, $fk);

        $childTable = CRUDBooster::parseSqlTable($row['table'])['table'];
        DB::table($childTable)->where($fk, $id)->delete();
        DB::table($childTable)->insert($childArray);
    }

    /**
     * @param $id
     * @param $name
     * @param $columns
     * @param $fk
     * @return array
     */
    private function childArray($id, $name, $columns, $fk)
    {
        $countInput = count(request($name.'-'.$columns[0]['name'])) - 1;
        $childArray = [];
        for ($i = 0; $i <= $countInput; $i++) {
            $columnData = [];
            $columnData[$fk] = $id;
            foreach ($columns as $col) {
                $colName = $col['name'];
                $columnData[$colName] = request($name.'-'.$colName)[$i];
            }
            $childArray[] = $columnData;
        }

        return $childArray;
    }
}