<?php

namespace crocodicstudio\crudbooster\CBCoreModule;

use crocodicstudio\crudbooster\controllers\CBController;
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
        $data['table_pk'] = CB::pk($CbCtrl->table);
        $data['page_title'] = CRUDBooster::getCurrentModule()->name;
        $data['page_description'] = trans('crudbooster.default_module_description');
        $data['date_candidate'] = $CbCtrl->date_candidate;
        $data['limit'] = $limit = request('limit', $CbCtrl->limit);

        $tablePK = $data['table_pk'];
        $table_columns = CB::getTableColumns($CbCtrl->table);

        $result = $CbCtrl->table()->select(DB::raw($CbCtrl->table.".".$CbCtrl->primary_key));


        $this->_filterForParent($result);


        $CbCtrl->hookQueryIndex($result);

        $this->_filterOutSoftDeleted($table_columns, $result);

        //$alias = [];
        //$join_alias_count = 0;
        //$join_table_temp = [];
        $table = $CbCtrl->table;
        $columns_table = $CbCtrl->columns_table;
        foreach ($columns_table as $index => $coltab) {

            //$join = @$coltab['join'];
            //$join_where = @$coltab['join_where'];
            //$join_id = @$coltab['join_id'];
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
            $filter_is_orderby = $this->_filterIndexRows($result);
        }

        $data = $this->_orderAndPaginate($filter_is_orderby, $result, $limit, $data, $table);

        $data['columns'] = $columns_table;

        if ($CbCtrl->index_return) {
            return $data;
        }

        //LISTING INDEX HTML
        $addaction = $CbCtrl->data['addaction'];

        if ($CbCtrl->sub_module) {
            $addaction = $this->_handleSubModules($addaction);
        }

        //$mainpath = CRUDBooster::mainpath();
        //$orig_mainpath = $CbCtrl->data['mainpath'];
        //$title_field = $CbCtrl->title_field;
        $html_contents = [];
        $number = (request('page', 1) - 1) * $limit + 1;
        foreach ($data['result'] as $row) {
            $html_content = [];

            if ($CbCtrl->button_bulk_action) {
                $html_content[] = "<input type='checkbox' class='checkbox' name='checkbox[]' value='".$row->{$tablePK}."'/>";
            }

            if ($CbCtrl->show_numbering) {
                $html_content[] = $number.'. ';
                $number++;
            }

            foreach ($columns_table as $col) {
                if($col['visible']===FALSE) continue;

                $html_content[] = $this->_calculateColumnValue($col, $row, $table);
            } //end foreach columns_table

            if ($CbCtrl->button_table_action) {
                $button_action_style = $CbCtrl->button_action_style;
                $html_content[] = "<div class='button_action' style='text-align:right'>".view('crudbooster::components.action', compact('addaction', 'row', 'button_action_style', 'parent_field'))->render()."</div>";
            }

            foreach ($html_content as $i => $v) {
                $CbCtrl->hookRowIndex($i, $v);
                $html_content[$i] = $v;
            }

            $html_contents[] = $html_content;
        } //end foreach data[result]

        $data['html_contents'] = ['html' => $html_contents, 'data' => $data['result']];
        return $data;
    }


    /**
     * @param $col
     * @param $row
     * @param $table
     * @return array
     */
    private function _calculateColumnValue($col, $row, $table)
    {
        $value = @$row->{$col['field']};
        $title = @$row->{$this->cb->title_field};
        $label = $col['label'];

        if (isset($col['image'])) {
            if ($value == '') {
                $value = "<a  data-lightbox='roadtrip' rel='group_{{$table}}' title='$label: $title' href='".asset('vendor/crudbooster/avatar.jpg')."'><img width='40px' height='40px' src='".asset('vendor/crudbooster/avatar.jpg')."'/></a>";
            } else {
                $pic = (strpos($value, 'http://') !== false) ? $value : asset($value);
                $value = "<a data-lightbox='roadtrip'  rel='group_{{$table}}' title='$label: $title' href='".$pic."'><img width='40px' height='40px' src='".$pic."'/></a>";
            }
        }

        if (@$col['download']) {
            $url = (strpos($value, 'http://')) ? $value : asset($value).'?download=1';
            if ($value) {
                $value = "<a class='btn btn-xs btn-primary' href='$url' target='_blank' title='Download File'><i class='fa fa-download'></i> Download</a>";
            } else {
                $value = " - ";
            }
        }

        if ($col['str_limit']) {
            $value = trim(strip_tags($value));
            $value = str_limit($value, $col['str_limit']);
        }

        if ($col['nl2br']) {
            $value = nl2br($value);
        }

        if (isset($col['callback'])) {
            $value = call_user_func($col['callback'], $row);
        }

        $datavalue = @unserialize($value);
        if ($datavalue !== false && $datavalue) {
            $prevalue = [];
            foreach ($datavalue as $d) {
                if ($d['label']) {
                    $prevalue[] = $d['label'];
                }
            }
            if (count($prevalue)) {
                $value = implode(", ", $prevalue);
            }
        }

        return $value;
    }


    /**
     * @return array
     */
    private function _handleParentTable()
    {
        $data = [];
        $data['parent_table'] = DB::table(request('parent_table'))->where(CB::pk(request('parent_table')), request('parent_id'))->first();
        if (request('foreign_key')) {
            $data['parent_field'] = request('foreign_key');
        } else {
            $data['parent_field'] = CB::getTableForeignKey(request('parent_table'), $this->table);
        }

        if (!$data['parent_field']) {
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
                'url' => CRUDBooster::adminPath($module['path']).'?parent_table='.$table_parent.'&parent_columns='.$module['parent_columns'].'&parent_columns_alias='.$module['parent_columns_alias'].'&parent_id=['.(! isset($module['custom_parent_id']) ? "id" : $module['custom_parent_id']).']&return_url='.urlencode(Request::fullUrl()).'&foreign_key='.$module['foreign_key'].'&label='.urlencode($module['label']),
                'color' => $module['button_color'],
                'showIf' => $module['showIf'],
            ];
        }

        return $addAction;
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
        $columns_table[$index]['type_data'] = CRUDBooster::getFieldType($tableField, $fieldOrign);
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

        if (CB::isColumnExists($table, $field)) {
            $result->addselect($table.'.'.$field);
            $columns_table[$index]['type_data'] = CRUDBooster::getFieldType($table, $field);
            $columns_table[$index]['field_with'] = $table.'.'.$field;
        }

        return $columns_table;
    }


    /**
     * @param $result
     */
    private function _filterForParent($result)
    {
        if (!request('parent_id')) {
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
        if (!in_array('deleted_at', $table_columns)) {
            return ;
        }
        $result->where($this->table.'.deleted_at', '=', null);
    }


    /**
     * @param $result
     * @return array
     */
    private function _filterIndexRows($result)
    {
        $filter_is_orderby = false;
        $filter_column = request('filter_column');
        $result->where(function ($query) use ($filter_column) {
            foreach ($filter_column as $key => $fc) {

                $value = @$fc['value'];
                $type = @$fc['type'];

                if ($type == 'empty') {
                    $query->whereNull($key)->orWhere($key, '');
                    continue;
                }

                if ($type == 'between') {
                    continue;
                }
                if (!$value || !$key || !$type) {
                    continue;
                }
                switch ($type) {
                    default:
                        $query->where($key, $type, $value);
                        break;
                    case 'like':
                    case 'not like':
                        $query->where($key, $type, '%'.$value.'%');
                        break;
                    case 'in':
                    case 'not in':
                        $value = explode(',', $value);
                        if ($value) {
                            $query->whereIn($key, $value);
                        }
                    break;
                }
            }
        });

        foreach ($filter_column as $key => $fc) {
            $value = @$fc['value'];
            $type = @$fc['type'];
            $sorting = @$fc['sorting'];

            if ($sorting != '' && $key) {
                $result->orderby($key, $sorting);
                $filter_is_orderby = true;
            }

            if ($type == 'between' && $key && $value) {
                $result->whereBetween($key, $value);
            }
        }

        return $filter_is_orderby;
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
     * @param $filter_is_orderby
     * @param $result
     * @param $limit
     * @param $data
     * @param $table
     * @return array
     */
    private function _orderAndPaginate($filter_is_orderby, $result, $limit, $data, $table)
    {
        $orderby = $this->cb->orderby;
        if ($filter_is_orderby !== true) {
            $data['result'] = $result->paginate($limit);

            return $data;
        }

        if (! $orderby) {
            $data['result'] = $result->orderby($this->table.'.'.$this->cb->primary_key, 'desc')->paginate($limit);

            return $data;
        }

        if (is_array($orderby)) {
            foreach ($orderby as $k => $v) {
                if (strpos($k, '.') !== false) {
                    $orderby_table = explode(".", $k)[0];
                } else {
                    $orderby_table = $table;
                }
                $result->orderby($orderby_table.'.'.$k, $v);
            }
            $data['result'] = $result->paginate($limit);

            return $data;
        }

        foreach (explode(";", $orderby) as $o) {
            $o = explode(",", $o);
            $key = $o[0];
            $value = $o[1];

            $orderby_table = $table;
            if (strpos($key, '.')) {
                $orderby_table = explode(".", $key)[0];
            }
            $result->orderby($orderby_table.'.'.$key, $value);
        }

        $data['result'] = $result->paginate($limit);

        return $data;
    }
}