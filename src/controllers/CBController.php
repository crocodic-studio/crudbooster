<?php namespace crocodicstudio\crudbooster\controllers;

error_reporting(E_ALL ^ E_NOTICE);

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\PDF;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Schema;

abstract class CBController extends Controller
{
    use CBControllerExportImport, CBControllerFileHandler,
        CBControllerValidator, CBControllerHelper, CBControllerHooks,
        CBControllerAdditionalRequests, CBControllerGuard;

    public $data_inputan;
    public $columns_table;
    public $module_name;
    public $page_icon;
    public $table;
    public $title_field;
    public $primary_key = 'id';
    public $arr = [];
    public $col = [];
    public $form = [];
    public $data = [];
    public $addaction = [];
    public $order_by = null;
    public $password_candidate = null;
    public $date_candidate = null;
    public $limit = 20;
    public $global_privilege = false;
    public $show_numbering = false;
    public $alert = [];
    public $index_button = [];
    public $button_filter = true;
    public $button_export = true;
    public $button_import = true;
    public $button_show = true;
    public $button_addmore = true;
    public $button_table_action = true;
    public $button_bulk_action = true;
    public $button_add = true;
    public $button_delete = true;
    public $button_cancel = true;
    public $button_save = true;
    public $button_edit = true;
    public $button_detail = true;
    public $button_action_style = 'button_icon';
    public $button_action_width = null;
    public $index_statistic = [];
    public $index_additional_view = [];
    public $pre_index_html = null;
    public $post_index_html = null;
    public $load_js = [];
    public $load_css = [];
    public $script_js = null;
    public $style_css = null;
    public $sub_module = [];
    public $show_addaction = true;
    public $table_row_color = [];
    public $button_selected = [];
    public $return_url = null;
    public $parent_field = null;
    public $parent_id = null;
    public $hide_form = [];
    public $index_return = false; //for export
    public $sidebar_mode = 'normal';

    public function __construct()
    {
        $this->cbInit();
        $this->checkHideForm();

        $this->primary_key = cb()->pk($this->table);
        $this->columns_table = $this->col;
        $this->data_inputan = $this->form;
        $this->data['module'] = cb()->getCurrentModule();
        $this->data['pk'] = $this->primary_key;
        $this->data['forms'] = $this->data_inputan;
        $this->data['hide_form'] = $this->hide_form;
        $this->data['addaction'] = ($this->show_addaction) ? $this->addaction : null;
        $this->data['table'] = $this->table;
        $this->data['title_field'] = $this->title_field;
        $this->data['appname'] = cb()->getSetting('appname');
        $this->data['alerts'] = $this->alert;
        $this->data['index_button'] = $this->index_button;
        $this->data['show_numbering'] = $this->show_numbering;
        $this->data['page_icon'] = $this->page_icon?:$this->data['module']->icon;
        $this->data['page_title'] = $this->module_name?:$this->data['module']->name;
        $this->data['button_detail'] = $this->button_detail;
        $this->data['button_edit'] = $this->button_edit;
        $this->data['button_show'] = $this->button_show;
        $this->data['button_add'] = $this->button_add;
        $this->data['button_delete'] = $this->button_delete;
        $this->data['button_filter'] = $this->button_filter;
        $this->data['button_export'] = $this->button_export;
        $this->data['button_addmore'] = $this->button_addmore;
        $this->data['button_cancel'] = $this->button_cancel;
        $this->data['button_save'] = $this->button_save;
        $this->data['button_table_action'] = $this->button_table_action;
        $this->data['button_bulk_action'] = $this->button_bulk_action;
        $this->data['button_import'] = $this->button_import;
        $this->data['button_action_width'] = $this->button_action_width;
        $this->data['button_selected'] = $this->button_selected;
        $this->data['index_statistic'] = $this->index_statistic;
        $this->data['index_additional_view'] = $this->index_additional_view;
        $this->data['table_row_color'] = $this->table_row_color;
        $this->data['pre_index_html'] = $this->pre_index_html;
        $this->data['post_index_html'] = $this->post_index_html;
        $this->data['load_js'] = $this->load_js;
        $this->data['load_css'] = $this->load_css;
        $this->data['script_js'] = $this->script_js;
        $this->data['style_css'] = $this->style_css;
        $this->data['sub_module'] = $this->sub_module;
        $this->data['parent_field'] = (g('parent_field')) ?: $this->parent_field;
        $this->data['parent_id'] = (g('parent_id')) ?: $this->parent_id;
        $this->data['sidebar_mode'] = $this->sidebarModeTrans($this->sidebar_mode);
        view()->share($this->data);
    }

    public function getIndex()
    {
        $data = [];

        $module = cb()->getCurrentModule();

        $this->guardIndex();

        $this->parentModuleCondition($data);

        $data['table'] = $this->table;
        $data['table_pk'] = cb()->pk($this->table);
        $data['page_title'] = $this->module_name?:$module->name;
        $data['page_description'] = trans('crudbooster.default_module_description');
        $data['date_candidate'] = $this->date_candidate;
        $data['limit'] = $limit = (request()->get('limit')) ? request()->get('limit') : $this->limit;

        $tablePK = $data['table_pk'];
        $table_columns = cb()->getTableColumns($this->table);
        $result = DB::table($this->table)->select(DB::raw($this->table.".".$this->primary_key));

        if (request()->get('parent_id')) {
            $table_parent = $this->table;
            $table_parent = cb()->parseSqlTable($table_parent)['table'];
            $result->where($table_parent.'.'.request()->get('foreign_key'), request()->get('parent_id'));
        }

        $this->hook_query_index($result);

        $this->hookQueryIndex($result);

        if (in_array('deleted_at', $table_columns)) {
            $result->where($this->table.'.deleted_at', null);
        }

        $alias = [];
        $join_alias_count = 0;
        $join_table_temp = [];
        $table = $this->table;
        $columns_table = $this->columns_table;
        foreach ($columns_table as $index => $coltab) {

            $join = @$coltab['join'];
            $join_where = @$coltab['join_where'];
            $join_id = @$coltab['join_id'];
            $field = @$coltab['name'];
            $join_table_temp[] = $table;

            if (! $field) {
                continue;
            }

            if (strpos($field, ' as ') !== false) {
                $field = substr($field, strpos($field, ' as ') + 4);
                $field_with = (array_key_exists('join', $coltab)) ? str_replace(",", ".", $coltab['join']) : $field;
                $result->addselect(DB::raw($coltab['name']));
                $columns_table[$index]['type_data'] = 'varchar';
                $columns_table[$index]['field'] = $field;
                $columns_table[$index]['field_raw'] = $field;
                $columns_table[$index]['field_with'] = $field_with;
                $columns_table[$index]['is_subquery'] = true;
                continue;
            }

            if (strpos($field, '.') !== false) {
                $result->addselect($field);
            } else {
                $result->addselect($table.'.'.$field);
            }

            $field_array = explode('.', $field);

            if (isset($field_array[1])) {
                $field = $field_array[1];
                $table = $field_array[0];
            } else {
                $table = $this->table;
            }

            if ($join) {

                $join_exp = explode(',', $join);

                $join_table = $join_exp[0];
                $joinTablePK = cb()->pk($join_table);
                $join_column = $join_exp[1];
                $join_alias = str_replace(".", "_", $join_table);

                if (in_array($join_table, $join_table_temp)) {
                    $join_alias_count += 1;
                    $join_alias = $join_table.$join_alias_count;
                }
                $join_table_temp[] = $join_table;

                $result->leftjoin($join_table.' as '.$join_alias, $join_alias.(($join_id) ? '.'.$join_id : '.'.$joinTablePK), '=', DB::raw($table.'.'.$field.(($join_where) ? ' AND '.$join_where.' ' : '')));
                $result->addselect($join_alias.'.'.$join_column.' as '.$join_alias.'_'.$join_column);

                $join_table_columns = cb()->getTableColumns($join_table);
                if ($join_table_columns) {
                    foreach ($join_table_columns as $jtc) {
                        $result->addselect($join_alias.'.'.$jtc.' as '.$join_alias.'_'.$jtc);
                    }
                }

                $alias[] = $join_alias;
                $columns_table[$index]['type_data'] = cb()->getFieldType($join_table, $join_column);
                $columns_table[$index]['field'] = $join_alias.'_'.$join_column;
                $columns_table[$index]['field_with'] = $join_alias.'.'.$join_column;
                $columns_table[$index]['field_raw'] = $join_column;

                @$join_table1 = $join_exp[2];
                @$joinTable1PK = cb()->pk($join_table1);
                @$join_column1 = $join_exp[3];
                @$join_alias1 = $join_table1;

                if ($join_table1 && $join_column1) {

                    if (in_array($join_table1, $join_table_temp)) {
                        $join_alias_count += 1;
                        $join_alias1 = $join_table1.$join_alias_count;
                    }

                    $join_table_temp[] = $join_table1;

                    $result->leftjoin($join_table1.' as '.$join_alias1, $join_alias1.'.'.$joinTable1PK, '=', $join_alias.'.'.$join_column);
                    $result->addselect($join_alias1.'.'.$join_column1.' as '.$join_column1.'_'.$join_alias1);
                    $alias[] = $join_alias1;
                    $columns_table[$index]['type_data'] = cb()->getFieldType($join_table1, $join_column1);
                    $columns_table[$index]['field'] = $join_column1.'_'.$join_alias1;
                    $columns_table[$index]['field_with'] = $join_alias1.'.'.$join_column1;
                    $columns_table[$index]['field_raw'] = $join_column1;
                }
            } else {

                if(isset($field_array[1])) {
                    $result->addselect($table.'.'.$field.' as '.$table.'_'.$field);
                    $columns_table[$index]['type_data'] = cb()->getFieldType($table, $field);
                    $columns_table[$index]['field'] = $table.'_'.$field;
                    $columns_table[$index]['field_raw'] = $table.'.'.$field;
                }else{
                    $result->addselect($table.'.'.$field);
                    $columns_table[$index]['type_data'] = cb()->getFieldType($table, $field);
                    $columns_table[$index]['field'] = $field;
                    $columns_table[$index]['field_raw'] = $field;
                }

                $columns_table[$index]['field_with'] = $table.'.'.$field;
            }
        }

        if (request()->get('q')) {
            $result->where(function ($w) use ($columns_table, $request) {
                foreach ($columns_table as $col) {
                    if (! $col['field_with']) {
                        continue;
                    }
                    if ($col['is_subquery']) {
                        continue;
                    }
                    $w->orwhere($col['field_with'], "like", "%".request()->get("q")."%");
                }
            });
        }

        if (request()->get('where')) {
            foreach (request()->get('where') as $k => $v) {
                $result->where($table.'.'.$k, $v);
            }
        }

        $filter_is_orderby = false;
        if (request()->get('filter_column')) {

            $filter_column = request()->get('filter_column');
            $result->where(function ($w) use ($filter_column, $fc) {
                foreach ($filter_column as $key => $fc) {

                    $value = @$fc['value'];
                    $type = @$fc['type'];

                    if ($type == 'empty') {
                        $w->whereNull($key)->orWhere($key, '');
                        continue;
                    }

                    if ($value == '' || $type == '') {
                        continue;
                    }

                    if ($type == 'between') {
                        continue;
                    }

                    switch ($type) {
                        default:
                            if ($key && $type && $value) {
                                $w->where($key, $type, $value);
                            }
                            break;
                        case 'like':
                        case 'not like':
                            $value = '%'.$value.'%';
                            if ($key && $type && $value) {
                                $w->where($key, $type, $value);
                            }
                            break;
                        case 'in':
                        case 'not in':
                            if ($value) {
                                $value = explode(',', $value);
                                if ($key && $value) {
                                    $w->whereIn($key, $value);
                                }
                            }
                            break;
                    }
                }
            });

            foreach ($filter_column as $key => $fc) {
                $value = @$fc['value'];
                $type = @$fc['type'];
                $sorting = @$fc['sorting'];

                if ($sorting != '') {
                    if ($key) {
                        $result->orderby($key, $sorting);
                        $filter_is_orderby = true;
                    }
                }

                if ($type == 'between') {
                    if ($key && $value) {
                        $result->whereBetween($key, $value);
                    }
                } else {
                    continue;
                }
            }
        }

        if ($filter_is_orderby == true) {
            $data['result'] = $result->paginate($limit);
        } else {
            if ($this->order_by) {
                if (is_array($this->order_by)) {
                    foreach ($this->order_by as $k => $v) {
                        if (strpos($k, '.') !== false) {
                            $orderby_table = explode(".", $k)[0];
                            $k = explode(".", $k)[1];
                        } else {
                            $orderby_table = $this->table;
                        }
                        $result->orderby($orderby_table.'.'.$k, $v);
                    }
                } else {
                    $this->order_by = explode(";", $this->order_by);
                    foreach ($this->order_by as $o) {
                        $o = explode(",", $o);
                        $k = $o[0];
                        $v = $o[1];
                        if (strpos($k, '.') !== false) {
                            $orderby_table = explode(".", $k)[0];
                        } else {
                            $orderby_table = $this->table;
                        }
                        $result->orderby($orderby_table.'.'.$k, $v);
                    }
                }
                $data['result'] = $result->paginate($limit);
            } else {
                $data['result'] = $result->orderby($this->table.'.'.$this->primary_key, 'desc')->paginate($limit);
            }
        }

        $data['columns'] = $columns_table;

        if ($this->index_return) {
            return $data;
        }

        //LISTING INDEX HTML
        $addaction = $this->data['addaction'];

        if ($this->sub_module) {
            foreach ($this->sub_module as $s) {
                $table_parent = cb()->parseSqlTable($this->table)['table'];
                $addaction[] = [
                    'label' => $s['label'],
                    'icon' => $s['button_icon'],
                    'url' => cb()->adminPath($s['path']).'?return_url='.urlencode(request()->fullUrl()).'&parent_table='.$table_parent.'&parent_columns='.$s['parent_columns'].'&parent_columns_alias='.$s['parent_columns_alias'].'&parent_id=['.(! isset($s['custom_parent_id']) ? "id" : $s['custom_parent_id']).']&foreign_key='.$s['foreign_key'].'&label='.urlencode($s['label']),
                    'color' => $s['button_color'],
                    'showIf' => $s['showIf'],
                ];
            }
        }

        $mainpath = cb()->mainpath();
        $orig_mainpath = $this->data['mainpath'];
        $title_field = $this->title_field;
        $html_contents = [];
        $page = (request()->get('page')) ? request()->get('page') : 1;
        $number = ($page - 1) * $limit + 1;
        foreach ($data['result'] as $row) {
            $html_content = [];

            if ($this->button_bulk_action) {

                $html_content[] = "<input type='checkbox' class='checkbox' name='checkbox[]' value='".$row->{$tablePK}."'/>";
            }

            if ($this->show_numbering) {
                $html_content[] = $number.'. ';
                $number++;
            }

            foreach ($columns_table as $col) {
                if ($col['visible'] === false) {
                    continue;
                }

                $value = @$row->{$col['field']};
                $title = @$row->{$this->title_field};
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
                    $url = (strpos($value, 'http://') !== false) ? $value : asset($value).'?download=1';
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

                if ($col['callback_php']) {
                    foreach ($row as $k => $v) {
                        $col['callback_php'] = str_replace("[".$k."]", $v, $col['callback_php']);
                    }
                    @eval("\$value = ".$col['callback_php'].";");
                }

                //New method for callback
                if (isset($col['callback'])) {
                    $value = call_user_func($col['callback'], $row);
                }

                $datavalue = @unserialize($value);
                if ($datavalue !== false) {
                    if ($datavalue) {
                        $prevalue = [];
                        foreach ($datavalue as $d) {
                            if ($d['label']) {
                                $prevalue[] = $d['label'];
                            }
                        }
                        if ($prevalue && count($prevalue)) {
                            $value = implode(", ", $prevalue);
                        }
                    }
                }

                $html_content[] = $value;
            } //end foreach columns_table

            if ($this->button_table_action):

                $button_action_style = $this->button_action_style;
                $html_content[] = "<div class='button_action' style='text-align:right'>".view('crudbooster::components.action', compact('addaction', 'row', 'button_action_style', 'parent_field'))->render()."</div>";

            endif;//button_table_action

            foreach ($html_content as $i => $v) {
                $this->hook_row_index($i, $v);
                $this->hookRowIndex($i, $v);
                $html_content[$i] = $v;
            }

            $html_contents[] = $html_content;
        } //end foreach data[result]

        $html_contents = ['html' => $html_contents, 'data' => $data['result']];

        $data['html_contents'] = $html_contents;

        return view("crudbooster::default.index", $data);
    }

    public function getAdd()
    {
        $this->guardAdd();

        $page_title = trans("crudbooster.add_data_page_title", ['module' => cb()->getCurrentModule()->name]);
        $page_menu = Route::getCurrentRoute()->getActionName();
        $command = 'add';

        return view('crudbooster::default.form', compact('page_title', 'page_menu', 'command'));
    }

    public function postAddSave()
    {
        $this->guardAddSave();

        $this->validation();
        $this->inputAssignment();

        if (Schema::hasColumn($this->table, 'created_at')) {
            $this->arr['created_at'] = date('Y-m-d H:i:s');
        }

        $this->hook_before_add($this->arr);

        $this->hookBeforeAdd($this->arr);

        $lastInsertId = $id = DB::table($this->table)->insertGetId($this->arr);

        //fix bug if primary key is uuid
        if($this->arr[$this->primary_key]!=$id)
            $id = $this->arr[$this->primary_key];

        $this->afterSavingDataProcess($id);

        $this->hook_after_add($lastInsertId);

        $this->hookAfterAdd($lastInsertId);

        $this->return_url = ($this->return_url) ? $this->return_url : request()->get('return_url');

        //insert log
        cb()->insertLog(trans("crudbooster.log_add", ['name' => $this->arr[$this->title_field], 'module' => cb()->getCurrentModule()->name]));

        if ($this->return_url) {
            if (request()->get('submit') == trans('crudbooster.button_save_more')) {
                cb()->redirect(request()->server('HTTP_REFERER'), trans("crudbooster.alert_add_data_success"), 'success');
            } else {
                cb()->redirect($this->return_url, trans("crudbooster.alert_add_data_success"), 'success');
            }
        } else {
            if (request()->get('submit') == trans('crudbooster.button_save_more')) {
                cb()->redirect(cb()->mainpath('add'), trans("crudbooster.alert_add_data_success"), 'success');
            } else {
                cb()->redirect(cb()->mainpath(), trans("crudbooster.alert_add_data_success"), 'success');
            }
        }
    }

    public function getEdit($id)
    {
        $row = DB::table($this->table)->where($this->primary_key, $id)->first();

        $this->guardEdit($row);

        $page_menu = Route::getCurrentRoute()->getActionName();
        $page_title = trans("crudbooster.edit_data_page_title", ['module' => cb()->getCurrentModule()->name, 'name' => $row->{$this->title_field}]);
        $command = 'edit';
        Session::put('current_row_id', $id);

        return view('crudbooster::default.form', compact('id', 'row', 'page_menu', 'page_title', 'command'));
    }

    public function postEditSave($id)
    {
        $row = cb()->first($this->table, $id);

        $this->guardEditSave($row);

        $this->validation($id);
        $this->inputAssignment($id);

        if (Schema::hasColumn($this->table, 'updated_at')) {
            $this->arr['updated_at'] = date('Y-m-d H:i:s');
        }

        $this->hook_before_edit($this->arr, $id);

        $this->hookBeforeEdit($this->arr, $id);

        // Update data
        DB::table($this->table)->where($this->primary_key, $id)->update($this->arr);

        $this->afterSavingDataProcess($id);

        $this->hook_after_edit($id);

        $this->hookAfterEdit($id);

        $this->return_url = ($this->return_url) ? $this->return_url : request()->get('return_url');

        //insert log
        $old_values = json_decode(json_encode($row), true);
        cb()->insertLog(trans("crudbooster.log_update", [
            'name' => $this->arr[$this->title_field],
            'module' => cb()->getCurrentModule()->name,
        ]), AdminLogsController::displayDiff($old_values, $this->arr));

        if ($this->return_url) {
            cb()->redirect($this->return_url, trans("crudbooster.alert_update_data_success"), 'success');
        } else {
            if (request()->get('submit') == trans('crudbooster.button_save_more')) {
                cb()->redirect(cb()->mainpath('add'), trans("crudbooster.alert_update_data_success"), 'success');
            } else {
                cb()->redirect(cb()->mainpath(), trans("crudbooster.alert_update_data_success"), 'success');
            }
        }
    }

    public function getDelete($id)
    {
        $row = DB::table($this->table)->where($this->primary_key, $id)->first();

        $this->guardDelete($row);

        $this->hook_before_delete($id);

        $this->hookBeforeDelete($id);

        if (cb()->isColumnExists($this->table, 'deleted_at')) {
            DB::table($this->table)->where($this->primary_key, $id)->update(['deleted_at' => date('Y-m-d H:i:s')]);
        } else {
            DB::table($this->table)->where($this->primary_key, $id)->delete();
        }

        //insert log
        cb()->insertLog(trans("crudbooster.log_delete", ['name' => $row->{$this->title_field}, 'module' => cb()->getCurrentModule()->name]));

        $this->hook_after_delete($id);

        $this->hookAfterDelete($id);

        $url = g('return_url') ?: cb()->referer();

        cb()->redirect($url, trans("crudbooster.alert_delete_data_success"), 'success');
    }

    public function getDetail($id)
    {
        $row = DB::table($this->table)->where($this->primary_key, $id)->first();

        $this->guardDetail($row);

        $module = cb()->getCurrentModule();

        $page_menu = Route::getCurrentRoute()->getActionName();
        $page_title = trans("crudbooster.detail_data_page_title", ['module' => $module->name, 'name' => $row->{$this->title_field}]);
        $command = 'detail';

        Session::put('current_row_id', $id);

        return view('crudbooster::default.form', compact('row', 'page_menu', 'page_title', 'command', 'id'));
    }

}
