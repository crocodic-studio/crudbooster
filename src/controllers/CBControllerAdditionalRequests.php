<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 4/7/2020
 * Time: 5:57 PM
 */

namespace crocodicstudio\crudbooster\controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

trait CBControllerAdditionalRequests
{
    public function postDataQuery()
    {
        $query = request()->get('query');
        $query = DB::select(DB::raw($query));
        return response()->json($query);
    }

    public function getDataTable()
    {
        $table = request()->get('table');
        $label = request()->get('label');
        $datatableWhere = urldecode(request()->get('datatable_where'));
        $foreign_key_name = request()->get('fk_name');
        $foreign_key_value = request()->get('fk_value');
        if ($table && $label && $foreign_key_name && $foreign_key_value) {
            $query = DB::table($table);
            if ($datatableWhere) {
                $query->whereRaw($datatableWhere);
            }
            $query->select('id as select_value', $label.' as select_label');
            $query->where($foreign_key_name, $foreign_key_value);
            $query->orderby($label, 'asc');

            return response()->json($query->get());
        } else {
            return response()->json([]);
        }
    }

    public function getModalData()
    {
        $table = request()->get('table');
        $where = request()->get('where');
        $where = urldecode($where);
        $columns = request()->get('columns');
        $columns = explode(",", $columns);

        $table = cb()->parseSqlTable($table)['table'];
        $tablePK = cb()->pk($table);
        $result = DB::table($table);

        if (request()->get('q')) {
            $result->where(function ($where) use ($columns) {
                foreach ($columns as $c => $col) {
                    if ($c == 0) {
                        $where->where($col, 'like', '%'.request()->get('q').'%');
                    } else {
                        $where->orWhere($col, 'like', '%'.request()->get('q').'%');
                    }
                }
            });
        }

        if ($where) {
            $result->whereraw($where);
        }

        $result->orderby($tablePK, 'desc');

        $data['result'] = $result->paginate(6);
        $data['columns'] = $columns;

        return view('crudbooster::default.type_components.datamodal.browser', $data);
    }

    public function getUpdateSingle()
    {
        $table = request()->get('table');
        $column = request()->get('column');
        $value = request()->get('value');
        $id = request()->get('id');
        $tablePK = cb()->pk($table);
        DB::table($table)->where($tablePK, $id)->update([$column => $value]);

        return redirect()->back()->with(['type' => 'success', 'message' => trans('crudbooster.alert_delete_data_success')]);
    }

    public function getFindData()
    {
        $q = request()->get('q');
        $id = request()->get('id');
        $limit = request()->get('limit') ?: 10;
        $format = request()->get('format');

        $table1 = (request()->get('table1')) ?: $this->table;
        $table1PK = cb()->pk($table1);
        $column1 = (request()->get('column1')) ?: $this->title_field;

        @$table2 = request()->get('table2');
        @$column2 = request()->get('column2');

        @$table3 = request()->get('table3');
        @$column3 = request()->get('column3');

        $where = request()->get('where');

        $fk = request()->get('fk');
        $fk_value = request()->get('fk_value');

        if ($q || $id || $table1) {
            $rows = DB::table($table1);
            $rows->select($table1.'.*');
            $rows->take($limit);

            if (cb()->isColumnExists($table1, 'deleted_at')) {
                $rows->where($table1.'.deleted_at', null);
            }

            if ($fk && $fk_value) {
                $rows->where($table1.'.'.$fk, $fk_value);
            }

            if ($table1 && $column1) {

                $orderby_table = $table1;
                $orderby_column = $column1;
            }

            if ($table2 && $column2) {
                $table2PK = cb()->pk($table2);
                $rows->join($table2, $table2.'.'.$table2PK, '=', $table1.'.'.$column1);
                $columns = cb()->getTableColumns($table2);
                foreach ($columns as $col) {
                    $rows->addselect($table2.".".$col." as ".$table2."_".$col);
                }
                $orderby_table = $table2;
                $orderby_column = $column2;
            }

            if ($table3 && $column3) {
                $table3PK = cb()->pk($table3);
                $rows->join($table3, $table3.'.'.$table3PK, '=', $table2.'.'.$column2);
                $columns = cb()->getTableColumns($table3);
                foreach ($columns as $col) {
                    $rows->addselect($table3.".".$col." as ".$table3."_".$col);
                }
                $orderby_table = $table3;
                $orderby_column = $column3;
            }

            if ($id) {
                $rows->where($table1.".".$table1PK, $id);
            }

            if ($where) {
                $rows->whereraw($where);
            }

            if ($format) {
                $format = str_replace('&#039;', "'", $format);
                $rows->addselect(DB::raw("CONCAT($format) as text"));
                if ($q) {
                    $rows->whereraw("CONCAT($format) like '%".$q."%'");
                }
            } else {
                $rows->addselect($orderby_table.'.'.$orderby_column.' as text');
                if ($q) {
                    $rows->where($orderby_table.'.'.$orderby_column, 'like', '%'.$q.'%');
                }
                $rows->orderBy($orderby_table.'.'.$orderby_column, 'asc');
            }

            $result = [];
            $result['items'] = $rows->get();
        } else {
            $result = [];
            $result['items'] = [];
        }

        return response()->json($result);
    }

}