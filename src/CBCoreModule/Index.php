<?php

namespace crocodicstudio\crudbooster\CBCoreModule;

use crocodicstudio\crudbooster\CBCoreModule\Index\FilterIndexRows;
use crocodicstudio\crudbooster\CBCoreModule\Index\RowContent;
use crocodicstudio\crudbooster\CBCoreModule\Index\Order;
use crocodicstudio\crudbooster\controllers\CBController;
use crocodicstudio\crudbooster\helpers\DbInspector;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;
use crocodicstudio\crudbooster\helpers\CRUDBooster;
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

        if (\Schema::hasColumn($table, 'deleted_at')) {
            $this->_filterOutSoftDeleted($query);
        }

        $columns_table = $CbCtrl->columns_table;
        foreach ($columns_table as $index => $coltab) {
            $field = @$coltab['name'];

            if (strpos($field, '.')) {
                $columns_table = $this->addDotField($columns_table, $index, $field, $query);
            } else {
                $columns_table = $this->_addField($columns_table, $index, $field, $query, $table);
            }
        }

        $this->_applyWhereAndQfilters($query, $columns_table, $table);

        $filter_is_orderby = false;
        if (request('filter_column')) {
            $filter_is_orderby = app(FilterIndexRows::class)->filterIndexRows($query, request('filter_column'));
        }

        if ($filter_is_orderby === true) {
            (new Order($this->cb))->handle($query, $table);
        }
        $limit = is_string($limit) ? (int)$limit : 15;
        $data['result'] = $query->paginate($limit);

        $data['columns'] = $columns_table;

        if ($CbCtrl->indexReturn) {
            return $data;
        }

        //LISTING INDEX HTML
        $addAction = $CbCtrl->data['addAction'];

        if (! empty($CbCtrl->sub_module)) {
            $addAction = $this->_handleSubModules($addAction);
        }

        //$mainpath = CRUDBooster::mainpath();
        //$orig_mainpath = $CbCtrl->data['mainpath'];
        //$titleField = $CbCtrl->titleField;
        $number = (request('page', 1) - 1) * $limit + 1;
        $columnsTable = array_filter($columns_table, function ($col) {
            return $col['visible'] !== false;
        });
        $htmlContents = (new RowContent($CbCtrl))->calculate($data, $number, $columnsTable, $addAction); //end foreach data[result]

        $data['html_contents'] = ['html' => $htmlContents, 'data' => $data['result']];

        return $data;
    }

    /**
     * @return array
     */
    private function _handleParentTable()
    {
        $data = [];
        $parent = (string)request('parent_table');
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
     * @param $result
     */
    private function _filterOutSoftDeleted($result)
    {
        $result->where($this->table.'.deleted_at', '=', null);
    }

    /**
     * @param $result
     * @param $field
     * @param $columnsTable
     * @param $index
     * @return mixed
     */
    private function addDotField($columnsTable, $index, $field, $result)
    {
        $result->addselect($field.' as '.str_slug($field, '_'));
        $tableField = substr($field, 0, strpos($field, '.'));
        $fieldOrign = substr($field, strpos($field, '.') + 1);
        $columnsTable[$index]['type_data'] = \Schema::getColumnType($tableField, $fieldOrign);
        $columnsTable[$index]['field'] = str_slug($field, '_');
        $columnsTable[$index]['field_raw'] = $field;
        $columnsTable[$index]['field_with'] = $tableField.'.'.$fieldOrign;

        return $columnsTable;
    }

    /**
     * @param $columnsTable
     * @param $index
     * @param $field
     * @param $table
     * @param $result
     * @return mixed
     */
    private function _addField($columnsTable, $index, $field, $result, $table)
    {
        $columnsTable[$index]['type_data'] = 'varchar';
        $columnsTable[$index]['field_with'] = null;
        $columnsTable[$index]['field'] = $field;
        $columnsTable[$index]['field_raw'] = $field;

        if (\Schema::hasColumn($table, $field)) {
            $result->addselect($table.'.'.$field);
            $columnsTable[$index]['type_data'] = \Schema::getColumnType($table, $field);
            $columnsTable[$index]['field_with'] = $table.'.'.$field;
        }

        return $columnsTable;
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
     * @param $table_parent
     * @return string
     */
    private function subModuleUrl($module, $table_parent)
    {
        return CRUDBooster::adminPath($module['path']).'?parent_table='.$table_parent.'&parent_columns='
            .$module['parent_columns'].'&parent_columns_alias='
            .$module['parent_columns_alias'].'&parent_id=['
            .(! isset($module['custom_parent_id']) ? "id" : $module['custom_parent_id'])
            .']&return_url='.urlencode(Request::fullUrl()).'&foreign_key='
            .$module['foreign_key'].'&label='.urlencode($module['label']);
    }

}