<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 4/7/2020
 * Time: 7:21 PM
 */

namespace crocodicstudio\crudbooster\helpers;


class TypeComponentHelper
{
    /**
     * @param $ro from afterSavingDataProcess looping
     */
    public static function checkbox($ro, $id, $table) {
        $inputdata = request()->get($ro['name']);

        if ($ro['type'] == 'checkbox') {
            if ($ro['relationship_table']) {
                $datatable = explode(",", $ro['datatable'])[0];
                $foreignKey2 = cb()->getForeignKey($datatable, $ro['relationship_table']);
                $foreignKey = cb()->getForeignKey($table, $ro['relationship_table']);
                DB::table($ro['relationship_table'])->where($foreignKey, $id)->delete();
                if ($inputdata) {
                    foreach ($inputdata as $input_id) {
                        $relationship_table_pk = cb()->pk($ro['relationship_table']);
                        DB::table($ro['relationship_table'])->insert([
                            $foreignKey => $id,
                            $foreignKey2 => $input_id,
                        ]);
                    }
                }
            }
        }
    }

    /**
     * @param $ro from afterSavingDataProcess looping
     */
    public static function select2($ro, $id, $table) {
        $inputdata = request()->get($ro['name']);

        if ($ro['type'] == 'select2') {
            if ($ro['relationship_table'] && $ro["datatable_orig"] == "") {
                $datatable = explode(",", $ro['datatable'])[0];

                $foreignKey2 = cb()->getForeignKey($datatable, $ro['relationship_table']);
                $foreignKey = cb()->getForeignKey($table, $ro['relationship_table']);
                DB::table($ro['relationship_table'])->where($foreignKey, $id)->delete();

                if ($inputdata) {
                    foreach ($inputdata as $input_id) {
                        $relationship_table_pk = cb()->pk($ro['relationship_table']);
                        DB::table($ro['relationship_table'])->insert([
                            $foreignKey => $id,
                            $foreignKey2 => $input_id,
                        ]);
                    }
                }
            }
            if ($ro['relationship_table'] && $ro["datatable_orig"] != "") {
                $params = explode("|", $ro['datatable_orig']);
                if(!isset($params[2])) $params[2] = "id";
                DB::table($params[0])->where($params[2], $id)->update([$params[1] => implode(",",$inputdata)]);
            }
        }
    }

    /**
     * @param $ro from afterSavingDataProcess looping
     */
    public static function child($ro, $id) {
        $inputdata = request()->get($ro['name']);

        if ($ro['type'] == 'child') {
            $name = str_slug($ro['label'], '');
            $columns = $ro['columns'];
            $getColName = request()->get($name.'-'.$columns[0]['name']);
            $count_input_data = ($getColName)?(count($getColName) - 1):0;
            $child_array = [];
            $childtable = cb()->parseSqlTable($ro['table'])['table'];
            $fk = $ro['foreign_key'];

            DB::table($childtable)->where($fk, $id)->delete();
            $lastId = cb()->newId($childtable);
            $childtablePK = cb()->pk($childtable);

            for ($i = 0; $i <= $count_input_data; $i++) {
                $column_data = [];
                foreach ($columns as $col) {
                    $colname = $col['name'];
                    $colvalue = request()->get($name.'-'.$colname)[$i];
                    if(isset($colvalue) === TRUE) {
                        $column_data[$colname] = $colvalue;
                    }
                }
                if(isset($column_data) === TRUE){
                    $column_data[$childtablePK] = $lastId;
                    $column_data[$fk] = $id;
                    $child_array[] = $column_data;
                    $lastId++;
                }
            }
            $child_array = array_reverse($child_array);
            DB::table($childtable)->insert($child_array);
        }
    }

}