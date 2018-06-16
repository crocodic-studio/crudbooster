<?php

namespace crocodicstudio\crudbooster\CBCoreModule;

use crocodicstudio\crudbooster\CBCoreModule\Index\FilterIndexRows;
use crocodicstudio\crudbooster\CBCoreModule\Index\Order;
use crocodicstudio\crudbooster\CBCoreModule\Index\RowContent;
use crocodicstudio\crudbooster\controllers\CBController;
use crocodicstudio\crudbooster\helpers\CRUDBooster;
use crocodicstudio\crudbooster\helpers\DbInspector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Schema;

class Index
{
    private $cb;

    private $table;

    public function index(CBController $CbCtrl)
    {
        $this->cb = $CbCtrl;
        $table = $this->table = $CbCtrl->table;

        $data = [];
        if (request('parent_table')) {
            $data = $this->_handleParentTable();
        }

        $data['table'] = $CbCtrl->table;
        $data['table_pk'] = DbInspector::findPk($table);
        $data['page_title'] = CRUDBooster::getCurrentModule()->name;
        $data['page_description'] = cbTrans('default_module_description');
        //$data['date_candidate'] = $CbCtrl->date_candidate;
        $data['limit'] = $limit = request('limit', $CbCtrl->limit);

        $query = $CbCtrl->table()->select(DB::raw($table.".".$CbCtrl->primaryKey));

        $this->_filterForParent($query);

        $CbCtrl->hookQueryIndex($query);

        $this->_filterOutSoftDeleted($table, $query);

        $columns = $CbCtrl->columns_table;
        foreach ($columns as $index => $col) {
            $field = array_get($col, 'name');

            if (strpos($field, '.')) {
                $columns[$index] = array_merge($columns[$index], $this->addDotField($field, $query));
            } else {
                $columns[$index] = array_merge($columns[$index], $this->_addField($field, $query, $table));
            }
        }

        $this->_applyWhereAndQfilters($query, $columns, $table);

        $filter_is_orderby = false;
        if (request('filter_column')) {
            $filter_is_orderby = app(FilterIndexRows::class)->filterIndexRows($query, request('filter_column'));
        }

        if ($filter_is_orderby === true) {
            (new Order())->handle($query, $table, $this->cb->orderby, $this->cb->primaryKey);
        }

        $limit = is_string($limit) ? (int) $limit : 15;
        $data['result'] = $query->paginate($limit);

        $data['columns'] = $columns;

        if ($CbCtrl->indexReturn) {
            return $data;
        }

        //$mainpath = CRUDBooster::mainpath();
        //$orig_mainpath = $CbCtrl->data['mainpath'];
        //$titleField = $CbCtrl->titleField;
        $number = (request('page', 1) - 1) * $limit + 1;
        $columnsTable = array_filter($columns, function ($col) {
            return $col['visible'] ?? true;
        });
        $htmlContents = (new RowContent($CbCtrl))->calculate($data, $number, $columnsTable); //end foreach data[result]

        $data['html_contents'] = ['html' => $htmlContents, 'data' => $data['result']];

        return $data;
    }

    /**
     * @return array
     */
    private function _handleParentTable()
    {
        $data = [];
        $parent = (string) request('parent_table');
        $data['parent_table'] = CRUDBooster::first(request('parent_table'), request('parent_id'));
        if (request('foreign_key')) {
            $data['parent_field'] = request('foreign_key');
        } else {
            $data['parent_field'] = DbInspector::getRelatedTableName($parent);
        }

        if (! $data['parent_field']) {
            return $data;
        }

        foreach ($this->cb->columns_table as $i => $col) {
            if ($col['name'] == $data['parent_field']) {
                unset($this->cb->columns_table[$i]);
            }
        }

        return $data;
    }

    /**
     * @param $result
     * @return null
     */
    private function _filterForParent($result)
    {
        if (! request('parent_id')) {
            return null;
        }

        $tableParent = CRUDBooster::parseSqlTable($this->table)['table'];
        $result->where($tableParent.'.'.request('foreign_key'), request('parent_id'));
    }

    /**
     * @param $table
     * @param $query
     */
    private function _filterOutSoftDeleted($table, $query)
    {
        if (\Schema::hasColumn($table, 'deleted_at')) {
            $query->where($table.'.deleted_at', '=', null);
        }
    }

    /**
     * @param $result
     * @param $field
     * @return mixed
     */
    private function addDotField($field, $result)
    {
        $result->addselect($field.' as '.str_slug($field, '_'));
        $tableField = substr($field, 0, strpos($field, '.'));
        $fieldOrign = substr($field, strpos($field, '.') + 1);

        return [
            'type_data' => \Schema::getColumnType($tableField, $fieldOrign),
            'field' => str_slug($field, '_'),
            'field_raw' => $field,
            'field_with' => $tableField.'.'.$fieldOrign,
        ];
    }

    /**
     * @param $field
     * @param $table
     * @param $result
     * @return mixed
     */
    private function _addField($field, $result, $table)
    {
        $t = [
            'type_data' => 'varchar',
            'field' => $field,
            'field_raw' => $field,
            'field_with' => null,
        ];

        if (\Schema::hasColumn($table, $field)) {
            $result->addselect($table.'.'.$field);
            $t['type_data'] = \Schema::getColumnType($table, $field);
            $t['field_with'] = $table.'.'.$field;
        }

        return $t;
    }

    /**
     * @param $result
     * @param $columnsTable
     * @param $table
     * @return mixed
     */
    private function _applyWhereAndQfilters($result, $columnsTable, $table)
    {
        if (request('q')) {
            $result->where(function ($query) use ($columnsTable) {
                foreach ($columnsTable as $col) {
                    if (! $col['field_with']) {
                        continue;
                    }
                    $query->orwhere($col['field_with'], "like", "%".request("q")."%");
                }
            });
        }

        if (request('where')) {
            foreach (request('where') as $k => $v) {
                $result->where($table.'.'.$k, $v);
            }
        }
    }

}