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
                $name = str_slug($row['label'], '');
                $columns = $row['columns'];
                $count_input_data = count(request($name.'-'.$columns[0]['name'])) - 1;
                $child_array = [];

                $childtable = CRUDBooster::parseSqlTable($row['table'])['table'];
                $fk = $row['foreign_key'];

                DB::table($childtable)->where($fk, $id)->delete();
                $lastId = CRUDBooster::newId($childtable);
                $childtablePK = CB::pk($childtable);

                for ($i = 0; $i <= $count_input_data; $i++) {

                    $column_data = [];
                    $column_data[$childtablePK] = $lastId;
                    $column_data[$fk] = $id;
                    foreach ($columns as $col) {
                        $colname = $col['name'];
                        $column_data[$colname] = request($name.'-'.$colname)[$i];
                    }
                    $child_array[] = $column_data;

                    $lastId++;
                }

                $child_array = array_reverse($child_array);

                DB::table($childtable)->insert($child_array);
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
        $count_input_data = count(request($name.'-'.$columns[0]['name'])) - 1;
        $child_array = [];

        $fk = $row['foreign_key'];
        for ($i = 0; $i <= $count_input_data; $i++) {
            $column_data = [];
            $column_data[$fk] = $id;
            foreach ($columns as $col) {
                $colName = $col['name'];
                $column_data[$colName] = request($name.'-'.$colName)[$i];
            }
            $child_array[] = $column_data;
        }

        $childTable = CRUDBooster::parseSqlTable($row['table'])['table'];
        DB::table($childTable)->insert($child_array);

        return $name;
    }

    /**
     * @param $row
     * @param $id
     * @param $inputData
     * @param $row
     * @return array
     */
    private function _updateRelations($row, $id, $inputData)
    {
        $dataTable = explode(",", $row['datatable'])[0];
        $foreignKey2 = CRUDBooster::getForeignKey($dataTable, $row['relationship_table']);
        $foreignKey = CRUDBooster::getForeignKey($this->Cb->table, $row['relationship_table']);
        DB::table($row['relationship_table'])->where($foreignKey, $id)->delete();

        if (!$inputData) {
            return null;
        }

        foreach ($inputData as $input_id) {
            $relationship_table_pk = CB::pk($row['relationship_table']);
            DB::table($row['relationship_table'])->insert([
                $relationship_table_pk => CRUDBooster::newId($row['relationship_table']),
                $foreignKey => $id,
                $foreignKey2 => $input_id,
            ]);
        }
    }

    /**
     * @param $ro
     * @param $id
     * @param $inputdata
     * @return null
     */
    private function _handleCheckbox($ro, $id, $inputdata)
    {
        $datatable = explode(",", $ro['datatable'])[0];
        $foreignKey2 = CRUDBooster::getForeignKey($datatable, $ro['relationship_table']);
        $foreignKey = CRUDBooster::getForeignKey($this->Cb->table, $ro['relationship_table']);
        DB::table($ro['relationship_table'])->where($foreignKey, $id)->delete();

        if (! $inputdata) {
            return null;
        }
        $relationship_table_pk = CB::pk($ro['relationship_table']);
        foreach ($inputdata as $input_id) {
            DB::table($ro['relationship_table'])->insert([
                $relationship_table_pk => CRUDBooster::newId($ro['relationship_table']),
                $foreignKey => $id,
                $foreignKey2 => $input_id,
            ]);
        }
    }
}