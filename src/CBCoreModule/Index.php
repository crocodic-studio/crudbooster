<?php

namespace crocodicstudio\crudbooster\CBCoreModule;

use crocodicstudio\crudbooster\CBCoreModule\Index\FilterIndexRows;
use crocodicstudio\crudbooster\CBCoreModule\Index\Order;
use crocodicstudio\crudbooster\CBCoreModule\Index\ValueCalculator;
use crocodicstudio\crudbooster\controllers\CBController;
use crocodicstudio\crudbooster\helpers\DbInspector;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;
use CRUDBooster;
use CB;
use Schema;

class Index
{
    private $cb;

    private $table;

    public function index(CBController $CbCtrl)
    {
        $this->cb = $CbCtrl;
        $this->table = $CbCtrl->table;

        $data = [];
        if (request('parent_table')) {
            $data = $this->_handleParentTable();
        }

        $data['table'] = $CbCtrl->table;
        $data['table_pk'] = DbInspector::findPk($CbCtrl->table);
        $data['page_title'] = CRUDBooster::getCurrentModule()->name;
        $data['page_description'] = cbTrans('default_module_description');
        $data['date_candidate'] = $CbCtrl->date_candidate;
        $data['limit'] = $limit = request('limit', $CbCtrl->limit);

        $tablePK = $data['table_pk'];

        $result = $CbCtrl->table()->select(DB::raw($CbCtrl->table.".".$CbCtrl->primary_key));

        $this->_filterForParent($result);

        $CbCtrl->hookQueryIndex($result);

        $tableCols = DbInspector::getTableCols($CbCtrl->table);
        $this->_filterOutSoftDeleted($tableCols, $result);
        unset($tableCols);

        $table = $CbCtrl->table;
        $columns_table = $CbCtrl->columns_table;
        foreach ($columns_table as $index => $coltab) {
            $field = @$coltab['name'];

            if (strpos($field, '.')) {
                $columns_table = $this->addDotField($columns_table, $index, $field, $result);
            } else {
                $columns_table = $this->_addField($columns_table, $index, $field, $result, $table);
            }
        }

        $this->_applyWhereAndQfilters($result, $columns_table, $table);

        $filter_is_orderby = false;
        if (request('filter_column')) {
            $filter_is_orderby = app(FilterIndexRows::class)->filterIndexRows($result, request('filter_column'));
        }

        if ($filter_is_orderby === true) {
            (new Order($this->cb))->handle($result, $table);
        }
        $data['result'] = $result->paginate($limit);

        $data['columns'] = $columns_table;

        if ($CbCtrl->index_return) {
            return $data;
        }

        //LISTING INDEX HTML
        $addAction = $CbCtrl->data['addaction'];

        if ($CbCtrl->sub_module) {
            $addAction = $this->_handleSubModules($addAction);
        }

        //$mainpath = CRUDBooster::mainpath();
        //$orig_mainpath = $CbCtrl->data['mainpath'];
        //$title_field = $CbCtrl->title_field;
        $number = (request('page', 1) - 1) * $limit + 1;
        $html_contents = $this->htmlContents($CbCtrl, $data, $tablePK, $number, $columns_table, $table, $addAction); //end foreach data[result]

        $data['html_contents'] = ['html' => $html_contents, 'data' => $data['result']];

        return $data;
    }
    /**
     * @return array
     */
    private function _handleParentTable()
    {
        $data = [];
        $data['parent_table'] = DB::table(request('parent_table'))->where(DbInspector::findPk(request('parent_table')), request('parent_id'))->first();
        if (request('foreign_key')) {
            $data['parent_field'] = request('foreign_key');
        } else {
            $data['parent_field'] = CB::getTableForeignKey(request('parent_table'), $this->table);
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

        $table_parent = CRUDBooster::parseSqlTable($this->table)['table'];
        $result->where($table_parent.'.'.request('foreign_key'), request('parent_id'));
    }

    /**
     * @param $table_columns
     * @param $result
     */
    private function _filterOutSoftDeleted($table_columns, $result)
    {
        if (! in_array('deleted_at', $table_columns)) {
            return;
        }
        $result->where($this->table.'.deleted_at', '=', null);
    }

    /**
     * @param $result
     * @param $field
     * @param $columns_table
     * @param $index
     * @return mixed
     */
    private function addDotField($columns_table, $index, $field, $result)
    {
        $result->addselect($field.' as '.str_slug($field, '_'));
        $tableField = substr($field, 0, strpos($field, '.'));
        $fieldOrign = substr($field, strpos($field, '.') + 1);
        $columns_table[$index]['type_data'] = DbInspector::getFieldTypes($tableField, $fieldOrign);
        $columns_table[$index]['field'] = str_slug($field, '_');
        $columns_table[$index]['field_raw'] = $field;
        $columns_table[$index]['field_with'] = $tableField.'.'.$fieldOrign;

        return $columns_table;
    }

    /**
     * @param $columns_table
     * @param $index
     * @param $field
     * @param $table
     * @param $result
     * @return mixed
     */
    private function _addField($columns_table, $index, $field, $result, $table)
    {
        $columns_table[$index]['type_data'] = 'varchar';
        $columns_table[$index]['field_with'] = null;
        $columns_table[$index]['field'] = $field;
        $columns_table[$index]['field_raw'] = $field;

        if (\Schema::hasColumn($table, $field)) {
            $result->addselect($table.'.'.$field);
            $columns_table[$index]['type_data'] = DbInspector::getFieldTypes($table, $field);
            $columns_table[$index]['field_with'] = $table.'.'.$field;
        }

        return $columns_table;
    }

    /**
     * @param $result
     * @param $columns_table
     * @param $table
     * @return mixed
     */
    private function _applyWhereAndQfilters($result, $columns_table, $table)
    {
        if (request('q')) {
            $result->where(function ($query) use ($columns_table) {
                foreach ($columns_table as $col) {
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
            $table_parent = CRUDBooster::parseSqlTable($this->table)['table'];
            $addAction[] = [
                'label' => $module['label'],
                'icon' => $module['button_icon'],
                'url' => $this->subModuleUrl($module, $table_parent),
                'color' => $module['button_color'],
                'showIf' => $module['showIf'],
            ];
        }

        return $addAction;
    }

    /**
     * @param \crocodicstudio\crudbooster\controllers\CBController $CbCtrl
     * @param $data
     * @param $tablePK
     * @param $number
     * @param $columnsTable
     * @param $table
     * @param $addaction
     * @return array
     */
    private function htmlContents(CBController $CbCtrl, $data, $tablePK, $number, $columnsTable, $table, $addaction)
    {
        $htmlContents = [];
        foreach ($data['result'] as $row) {
            $htmlContent = [];

            if ($CbCtrl->button_bulk_action) {
                $htmlContent[] = "<input type='checkbox' class='checkbox' name='checkbox[]' value='".$row->{$tablePK}."'/>";
            }

            if ($CbCtrl->show_numbering) {
                $htmlContent[] = $number.'. ';
                $number++;
            }

            foreach ($columnsTable as $col) {
                if ($col['visible'] === false) {
                    continue;
                }
                $htmlContent[] = (new ValueCalculator)->calculate($col, $row, $table, @$row->{$this->cb->title_field});
            }

            if ($CbCtrl->button_table_action) {
                $button_action_style = $CbCtrl->button_action_style;
                $htmlContent[] = "<div class='button_action' style='text-align:right'>".view('crudbooster::components.action', compact('addaction', 'row', 'button_action_style', 'parent_field'))->render()."</div>";
            }

            foreach ($htmlContent as $i => $v) {
                $CbCtrl->hookRowIndex($i, $v);
                $htmlContent[$i] = $v;
            }

            $htmlContents[] = $htmlContent;
        }

        return $htmlContents;
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