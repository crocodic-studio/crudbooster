<?php namespace crocodicstudio\crudbooster\controllers;

error_reporting(E_ALL ^ E_NOTICE);

use crocodicstudio\crudbooster\controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\PDF;
use Maatwebsite\Excel\Facades\Excel;
use CRUDBooster;
use CB;
use Schema;

class CBController extends Controller {

	public $data_inputan;
	public $columns_table;
	public $module_name;
	public $table;
	public $title_field;
	public $primary_key        = 'id';
	public $arr                = array();
	public $col                = array();
	public $form               = array();
	public $data               = array();
	public $addaction          = array();
	public $orderby            = NULL;
	public $password_candidate = NULL;
	public $date_candidate     = NULL;
	public $limit              = 20;
	public $global_privilege   = FALSE;
	public $show_numbering	   = FALSE;

	public $alert                 = array();
	public $index_button          = array();

	public $button_filter       = TRUE;
	public $button_export       = TRUE;
	public $button_import       = TRUE;
	public $button_show         = TRUE;
	public $button_addmore      = TRUE;
	public $button_table_action = TRUE;
	public $button_bulk_action	= TRUE;
	public $button_add          = TRUE;
	public $button_delete       = TRUE;
	public $button_cancel       = TRUE;
	public $button_save         = TRUE;
	public $button_edit         = TRUE;
	public $button_detail       = TRUE;
	public $button_action_style = 'button_icon';
	public $button_action_width = NULL;

	public $index_statistic       = array();
	public $index_additional_view = array();
	public $pre_index_html        = null;
	public $post_index_html       = null;
	public $load_js               = array();
	public $load_css              = array();
	public $script_js             = NULL;
	public $style_css             = NULL;
	public $sub_module            = array();
	public $show_addaction        = TRUE;
	public $table_row_color 	  = array();
	public $button_selected 	  = array();
	public $return_url 			  = NULL;
	public $parent_field 		  = NULL;
	public $parent_id 			  = NULL;
	public $hide_form			  = array();
	public $index_return 		  = FALSE; //for export


	public function cbLoader() {
		$this->cbInit();

		$this->checkHideForm();

		$this->primary_key 					 = CB::pk($this->table);
		$this->columns_table                 = $this->col;
		$this->data_inputan                  = $this->form;
		$this->data['pk']				     = $this->primary_key;
		$this->data['forms']                 = $this->data_inputan;
		$this->data['hide_form'] 			 = $this->hide_form;
		$this->data['addaction']             = ($this->show_addaction)?$this->addaction:NULL;
		$this->data['table']                 = $this->table;
		$this->data['title_field']           = $this->title_field;
		$this->data['appname']               = CRUDBooster::getSetting('appname');
		$this->data['alerts']                = $this->alert;
		$this->data['index_button']          = $this->index_button;
		$this->data['show_numbering']	     = $this->show_numbering;
		$this->data['button_detail']         = $this->button_detail;
		$this->data['button_edit']           = $this->button_edit;
		$this->data['button_show']           = $this->button_show;
		$this->data['button_add']            = $this->button_add;
		$this->data['button_delete']         = $this->button_delete;
		$this->data['button_filter']         = $this->button_filter;
		$this->data['button_export']         = $this->button_export;
		$this->data['button_addmore']        = $this->button_addmore;
		$this->data['button_cancel']         = $this->button_cancel;
		$this->data['button_save']           = $this->button_save;
		$this->data['button_table_action']   = $this->button_table_action;
		$this->data['button_bulk_action']    = $this->button_bulk_action;
		$this->data['button_import']         = $this->button_import;
		$this->data['button_action_width']   = $this->button_action_width;
		$this->data['button_selected']       = $this->button_selected;
		$this->data['index_statistic']       = $this->index_statistic;
		$this->data['index_additional_view'] = $this->index_additional_view;
		$this->data['table_row_color']       = $this->table_row_color;
		$this->data['pre_index_html']        = $this->pre_index_html;
		$this->data['post_index_html']       = $this->post_index_html;
		$this->data['load_js']               = $this->load_js;
		$this->data['load_css']              = $this->load_css;
		$this->data['script_js']             = $this->script_js;
		$this->data['style_css']             = $this->style_css;
		$this->data['sub_module']            = $this->sub_module;
		$this->data['parent_field'] 		 = (g('parent_field'))?:$this->parent_field;
		$this->data['parent_id'] 		 	 = (g('parent_id'))?:$this->parent_id;

		if(CRUDBooster::getCurrentMethod() == 'getProfile') {
			Session::put('current_row_id',CRUDBooster::myId());
			$this->data['return_url'] = Request::fullUrl();			
		}		

        view()->share($this->data);
	}

	public function cbView($template,$data) {
		$this->cbLoader();
		echo view($template,$data);
	}

	private function checkHideForm() {
		if(!count($this->hide_form)) {
		    return null;
		}
        foreach($this->form as $i=>$f) {
            if(in_array($f['name'], $this->hide_form)) {
                unset($this->form[$i]);
            }
        }
	}

	public function getIndex() {
		$this->cbLoader();

		if(Request::get('parent_table')) {
			$parentTablePK = CB::pk(g('parent_table'));
			$data['parent_table'] = DB::table(Request::get('parent_table'))->where($parentTablePK,Request::get('parent_id'))->first();
			if(Request::get('foreign_key')) {
				$data['parent_field'] = Request::get('foreign_key');
			}else{
				$data['parent_field'] = CB::getTableForeignKey(g('parent_table'),$this->table);	
			}

			if($parent_field) {
				foreach($this->columns_table as $i=>$col) {
					if($col['name'] == $parent_field) {
						unset($this->columns_table[$i]);
					}
				}
			}
		}

		$data['table'] 	  = $this->table;
		$data['table_pk'] = CB::pk($this->table);
		$data['page_title']       = $module->name;
		$data['page_description'] = trans('crudbooster.default_module_description');
		$data['date_candidate']   = $this->date_candidate;
		$data['limit'] = $limit   = (Request::get('limit'))?Request::get('limit'):$this->limit;

		$tablePK = $data['table_pk'];
		$table_columns = CB::getTableColumns($this->table);

		$result = DB::table($this->table)
		->select(DB::raw($this->table.".".$this->primary_key));

		if(Request::get('parent_id')) {
			$table_parent = $this->table;
			$table_parent = CRUDBooster::parseSqlTable($table_parent)['table'];
			$result->where($table_parent.'.'.Request::get('foreign_key'),Request::get('parent_id'));
		}


		$this->hookQueryIndex($result);

		if(in_array('deleted_at', $table_columns)) {
			$result->where($this->table.'.deleted_at',NULL);
		}

		$alias            = array();
		$join_alias_count = 0;
		$join_table_temp  = array();
		$table            = $this->table;
		$columns_table    = $this->columns_table;
		foreach($columns_table as $index => $coltab) {

			$join              = @$coltab['join'];
			$join_where        = @$coltab['join_where'];
			$join_id           = @$coltab['join_id'];
			$field             = @$coltab['name'];						

			if(strpos($field,'.')!==FALSE) {
				$result->addselect($field.' as '.str_slug($field,'_'));
				$tableField = substr($field, 0, strpos($field, '.'));
				$fieldOrign = substr($field, strpos($field, '.')+1);
				$columns_table[$index]['type_data']	 = CRUDBooster::getFieldType($tableField,$fieldOrign);
				$columns_table[$index]['field']      = str_slug($field,'_');
				$columns_table[$index]['field_raw']  = $field;
				$columns_table[$index]['field_with'] = $tableField.'.'.$fieldOrign;
			}else{
                $columns_table[$index]['type_data']	 = 'varchar';
                $columns_table[$index]['field_with'] = NULL;
                $columns_table[$index]['field']      = $field;
                $columns_table[$index]['field_raw']  = $field;

				if(CB::isColumnExists($table,$field)) {
					$result->addselect($table.'.'.$field);
					$columns_table[$index]['type_data']	 = CRUDBooster::getFieldType($table,$field);
					$columns_table[$index]['field_with'] = $table.'.'.$field;
				}
			}
		}

		if(Request::get('q')) {
			$result->where(function($w) use ($columns_table, $request) {
				foreach($columns_table as $col) {
						if(!$col['field_with']) continue;						
						$w->orwhere($col['field_with'],"like","%".Request::get("q")."%");
				}
			});
		}

		if(Request::get('where')) {
			foreach(Request::get('where') as $k=>$v) {
				$result->where($table.'.'.$k,$v);
			}
		}

		$filter_is_orderby = false;
		if(Request::get('filter_column')) {

			$filter_column = Request::get('filter_column');
			$result->where(function($w) use ($filter_column,$fc) {
				foreach($filter_column as $key=>$fc) {

					$value = @$fc['value'];
					$type  = @$fc['type'];

					if($type == 'empty') {
						$w->whereNull($key)->orWhere($key,'');
						continue;
					}

					if($value=='' || $type=='') continue;

					if($type == 'between') continue;

					switch($type) {
						default:
							if($key && $type && $value) $w->where($key,$type,$value);
						break;
						case 'like':
						case 'not like':
							$value = '%'.$value.'%';
							if($key && $type && $value) $w->where($key,$type,$value);
						break;
						case 'in':
						case 'not in':
							if($value) {
								$value = explode(',',$value);
								if($key && $value) $w->whereIn($key,$value);
							}
						break;
					}


				}
			});

			foreach($filter_column as $key=>$fc) {
				$value = @$fc['value'];
				$type  = @$fc['type'];
				$sorting = @$fc['sorting'];

				if($sorting!='' && $key) {
                    $result->orderby($key,$sorting);
                    $filter_is_orderby = true;
				}

				if ($type=='between') {
					if($key && $value) $result->whereBetween($key,$value);
				}
			}
		}

		if($filter_is_orderby == true) {
			$data['result']  = $result->paginate($limit);

		}else{
			if($this->orderby) {
				if(is_array($this->orderby)) {
					foreach($this->orderby as $k=>$v) {
						if(strpos($k, '.')!==FALSE) {
							$orderby_table = explode(".",$k)[0];
						}else{
							$orderby_table = $table;
						}
						$result->orderby($orderby_table.'.'.$k,$v);
					}
				}else{
					$this->orderby = explode(";",$this->orderby);
					foreach($this->orderby as $o) {
						$o = explode(",",$o);
						$k = $o[0];
						$v = $o[1];
						if(strpos($k, '.')!==FALSE) {
							$orderby_table = explode(".",$k)[0];
						}else{
							$orderby_table = $table;
						}
						$result->orderby($orderby_table.'.'.$k,$v);
					}
				}
				$data['result'] = $result->paginate($limit);
			}else{
				$data['result'] = $result->orderby($this->table.'.'.$this->primary_key,'desc')->paginate($limit);
			}
		}

		$data['columns'] = $columns_table;

		if($this->index_return) return $data;

		//LISTING INDEX HTML
		$addaction     = $this->data['addaction'];

		if($this->sub_module) {
			foreach($this->sub_module as $s) {
				$table_parent = CRUDBooster::parseSqlTable($this->table)['table'];
				$addaction[] = [
					'label'=>$s['label'],
					'icon'=>$s['button_icon'],
					'url'=>CRUDBooster::adminPath($s['path']).'?parent_table='.$table_parent.'&parent_columns='.$s['parent_columns'].'&parent_columns_alias='.$s['parent_columns_alias'].'&parent_id=['.(!isset($s['custom_parent_id']) ? "id": $s['custom_parent_id']).']&return_url='.urlencode(Request::fullUrl()).'&foreign_key='.$s['foreign_key'].'&label='.urlencode($s['label']),
					'color'=>$s['button_color'],
                                        'showIf'=>$s['showIf']
				];
			}
		}
		
		$mainpath      = CRUDBooster::mainpath();
		$orig_mainpath = $this->data['mainpath'];
		$title_field   = $this->title_field;
		$html_contents = array();
		$page = (Request::get('page'))?Request::get('page'):1; 
		$number = ($page-1)*$limit+1; 
		foreach($data['result'] as $row) {
			$html_content = array();

			if($this->button_bulk_action) {		

				$html_content[] = "<input type='checkbox' class='checkbox' name='checkbox[]' value='".$row->{$tablePK}."'/>";
			}

			if($this->show_numbering) {
				$html_content[] = $number.'. ';
				$number++;
			}

			foreach($columns_table as $col) {
		          if($col['visible']===FALSE) continue;		          

		          $value = @$row->{$col['field']};
		          $title = @$row->{$this->title_field};
		          $label = $col['label'];

		          if(isset($col['image'])) {
			            if($value=='') {			              
			              $value = "<a  data-lightbox='roadtrip' rel='group_{{$table}}' title='$label: $title' href='".asset('vendor/crudbooster/avatar.jpg')."'><img width='40px' height='40px' src='".asset('vendor/crudbooster/avatar.jpg')."'/></a>";
			            }else{
							$pic = (strpos($value,'http://')!==FALSE)?$value:asset($value);				            
				            $value = "<a data-lightbox='roadtrip'  rel='group_{{$table}}' title='$label: $title' href='".$pic."'><img width='40px' height='40px' src='".$pic."'/></a>";
			            }			            
		          }

		          if(@$col['download']) {
			            $url = (strpos($value,'http://')!==FALSE)?$value:asset($value).'?download=1';
			            if($value) {
			            	$value = "<a class='btn btn-xs btn-primary' href='$url' target='_blank' title='Download File'><i class='fa fa-download'></i> Download</a>";
			            }else{
			            	$value = " - ";
			            }
		          }

		            if($col['str_limit']) {
		            	$value = trim(strip_tags($value));
		            	$value = str_limit($value,$col['str_limit']);
		            }

		            if($col['nl2br']) {
		            	$value = nl2br($value);
		            }
		            
			        if(isset($col['callback'])) {
			        	$value = call_user_func($col['callback'],$row);
			        }

		            $datavalue = @unserialize($value);
					if ($datavalue !== false && $datavalue) {
                        $prevalue = [];
                        foreach($datavalue as $d) {
                            if($d['label']) {
                                $prevalue[] = $d['label'];
                            }
                        }
                        if(count($prevalue)) {
                            $value = implode(", ",$prevalue);
                        }
					}

		          $html_content[] = $value;
	        } //end foreach columns_table


	      if($this->button_table_action):

	      		$button_action_style = $this->button_action_style;
	      		$html_content[] = "<div class='button_action' style='text-align:right'>".view('crudbooster::components.action',compact('addaction','row','button_action_style','parent_field'))->render()."</div>";

          endif;//button_table_action


          foreach($html_content as $i=>$v) {
          	$this->hookRowIndex($i,$v);
          	$html_content[$i] = $v;
          }

	      $html_contents[] = $html_content;
		} //end foreach data[result]


 		$html_contents = ['html'=>$html_contents,'data'=>$data['result']];

		$data['html_contents'] = $html_contents;

		return view("crudbooster::default.index",$data);
	}

	public function getExportData() {

		return redirect(CRUDBooster::mainpath());
	}

	public function postExportData() {
		$this->limit 		= Request::input('limit');
		$this->index_return = TRUE;
		$filetype 			= Request::input('fileformat');
		$filename 			= Request::input('filename');
		$papersize			= Request::input('page_size');
		$paperorientation	= Request::input('page_orientation');
		$response           = $this->getIndex();

		if(Request::input('default_paper_size')) {
			DB::table('cms_settings')->where('name','default_paper_size')->update(['content'=>$papersize]);
		}

		switch($filetype) {
			case "pdf":
				$view = view('crudbooster::export',$response)->render();
				$pdf = App::make('dompdf.wrapper');
				$pdf->loadHTML($view);
				$pdf->setPaper($papersize,$paperorientation);
				return $pdf->stream($filename.'.pdf');
			break;
			case 'xls':
				Excel::create($filename, function($excel) use ($response) {
					$excel->setTitle($filename)
					->setCreator("crudbooster.com")
					->setCompany(CRUDBooster::getSetting('appname'));
				    $excel->sheet($filename, function($sheet) use ($response) {
				    	$sheet->setOrientation($paperorientation);
				        $sheet->loadview('crudbooster::export',$response);
				    });
				})->export('xls');
			break;
			case 'csv':
				Excel::create($filename, function($excel) use ($response) {
					$excel->setTitle($filename)
					->setCreator("crudbooster.com")
					->setCompany(CRUDBooster::getSetting('appname'));
				    $excel->sheet($filename, function($sheet) use ($response) {
				    	$sheet->setOrientation($paperorientation);
				        $sheet->loadview('crudbooster::export',$response);
				    });
				})->export('csv');
			break;
		}
	}

	public function getDataQuery() {
		$key = g('query');
		if(!Cache::has($key)) {
            return response()->json(['items'=>[] ]);
		}
        $query = Cache::get($key);
        $fk_name = g('fk_name');
        $fk_value = g('fk_value');
        if(strpos(strtolower($query), 'where')!==FALSE) {
            if(strpos(strtolower($query), 'order by')) {
                $query = str_replace("ORDER BY","order by",$query);
                $qraw = explode('order by',$query);
                $query = $qraw[0]." and ".$fk_name." = '".$fk_value."' ".$qraw[1];
            }else{
                $query .= " and ".$fk_name." = '".$fk_value."'";
            }
        }else{
            if(strpos(strtolower($query), 'order by')) {
                $query = str_replace("ORDER BY","order by",$query);
                $qraw = explode('order by',$query);
                $query = $qraw[0]." where ".$fk_name." = '".$fk_value."' ".$qraw[1];
            }else{
                $query .= " where ".$fk_name." = '".$fk_value."'";
            }
        }

        $query = DB::select(DB::raw($query));
        return response()->json(['items'=>$query]);
	}

	public function getDataTable() {
		$table = Request::get('table');
		$label = Request::get('label');
		$datatableWhere = urldecode(Request::get('datatable_where'));
		$foreign_key_name = Request::get('fk_name');
		$foreign_key_value = Request::get('fk_value');
		if(!$table || !$label || !$foreign_key_name || !$foreign_key_value) {
			return response()->json([]);
		}
        $query = DB::table($table);
        if($datatableWhere) {
            $query->whereRaw($datatableWhere);
        }
        $query->select('id as select_value',$label.' as select_label');
        $query->where($foreign_key_name,$foreign_key_value);
        $query->orderby($label,'asc');
        return response()->json($query->get());
	}

	public function getDataModalDatatable() {
		$data = Request::get('data');
		$data = base64_decode(json_decode($data,true));

		$columns = $data['columns'];
		$columns = explode(',',$columns);

		$result = DB::table($data['table']);
		if(Request::get('q')) {
			$result->where(function($where) use ($columns) {
				foreach($columns as $c=>$col) {
					if($c==0) {
						$where->where($col,'like','%'.Request::get('q').'%');
					}else{
						$where->orWhere($col,'like','%'.Request::get('q').'%');
					}
				}
			});
		}

		if($data['sql_where']) {
			$result->whereraw($data['sql_where']);
		}

		if($data['sql_orderby']) {
			$result->orderByRaw($data['sql_orderby']);
		}else{
			$result->orderBy($data['column_value'],'desc');
		}
		$limit = ($data['limit'])?:6;
		return view('crudbooster::default.type_components.datamodal.browser',['result'=>$result->paginate($limit),'data'=>$data]);
	}	

	public function getUpdateSingle() {
		$table = Request::get('table');
		$column = Request::get('column');
		$value = Request::get('value');
		$id = Request::get('id');
		$tablePK = CB::pk($table);
		DB::table($table)->where($tablePK,$id)->update([$column => $value]);

        return CRUDBooster::backWithMsg(trans('crudbooster.alert_delete_data_success'));
	}

	public function postFindData() {
		$q = Request::get('q');
		$data = Request::get('data');
		$data = base64_decode($data);
		$data = json_decode($data,true);
		$id = Request::get('id');

		$fieldValue = $data['field_value'];		

		$table = $data['table'];
		$rows = DB::table($table);
		$rows->select($table.'.*');

		if($data['sql_orderby']) {
			$rows->orderbyRaw($data['sql_orderby']);
		}else{			
			$rows->orderby($fieldValue,'desc');
		}
		if($data['limit']) {
			$rows->take($data['limit']);
		}else{
			$rows->take(10);
		}

		if($data['field_label']) {
			$rows->addselect($data['field_label'].' as text');
		}

		if($data['field_value']) {
			$rows->addselect($data['field_value'].' as id');
		}

		if($data['sql_where']) {
			$rows->whereRaw($data['sql_where']);
		}

		if($q) {
			$rows->where($data['field_label'],'like','%'.$q.'%');
		}

		if($id) {
			$rows->where($data['field_value'],$id);
		}

		$result = $rows->get();
		return response()->json(['items'=>$result]);
	}

	public function postFindDataOld() {
		$q        = Request::get('q');
		$id       = Request::get('id');
		$limit    = Request::get('limit')?:10;
		$format   = Request::get('format');

		$table1   = (Request::get('table1'))?:$this->table;
		$table1PK = CB::pk($table1);
		$column1  = (Request::get('column1'))?:$this->title_field;

		@$table2  = Request::get('table2');
		@$column2 = Request::get('column2');

		@$table3  = Request::get('table3');
		@$column3 = Request::get('column3');

		$where    = Request::get('where');

		$fk 	  = Request::get('fk');
		$fk_value = Request::get('fk_value');

		if($q || $id || $table1) {
			$rows = DB::table($table1);
			$rows->select($table1.'.*');
			$rows->take($limit);

			if(Schema::hasColumn($table1,'deleted_at')) {
				$rows->where($table1.'.deleted_at',NULL);
			}

			if($fk && $fk_value) {
				$rows->where($table1.'.'.$fk,$fk_value);
			}

			if($table1 && $column1) {

				$orderby_table  = $table1;
				$orderby_column = $column1;
			}

			if($table2 && $column2) {
				$table2PK = CB::pk($table2);
				$rows->join($table2,$table2.'.'.$table2PK,'=',$table1.'.'.$column1);
				$columns = CRUDBooster::getTableColumns($table2);
				foreach($columns as $col) {
					$rows->addselect($table2.".".$col." as ".$table2."_".$col);
				}
				$orderby_table  = $table2;
				$orderby_column = $column2;
			}

			if($table3 && $column3) {
				$table3PK = CB::pk($table3);
				$rows->join($table3,$table3.'.'.$table3PK,'=',$table2.'.'.$column2);
				$columns = CRUDBooster::getTableColumns($table3);
				foreach($columns as $col) {
					$rows->addselect($table3.".".$col." as ".$table3."_".$col);
				}
				$orderby_table  = $table3;
				$orderby_column = $column3;
			}

			if($id) {
				$rows->where($table1.".".$table1PK,$id);
			}

			if($where) {
				$rows->whereraw($where);
			}

			if($format) {
				$format = str_replace('&#039;', "'", $format);
				$rows->addselect(DB::raw("CONCAT($format) as text"));
				if($q) $rows->whereraw("CONCAT($format) like '%".$q."%'");
			}else{
				$rows->addselect($orderby_table.'.'.$orderby_column.' as text');
				if($q) $rows->where($orderby_table.'.'.$orderby_column,'like','%'.$q.'%');
			}

			$result          = array();
			$result['items'] = $rows->get();
		}else{
			$result          = array();
			$result['items'] = array();
		}
		return response()->json($result);
	}

	public function validation($id=NULL) {

		$request_all = Request::all();
		$array_input = array();
		$componentPath = "vendor".DIRECTORY_SEPARATOR."crocodicstudio".DIRECTORY_SEPARATOR."crudbooster".DIRECTORY_SEPARATOR."src".DIRECTORY_SEPARATOR."views".DIRECTORY_SEPARATOR."default".DIRECTORY_SEPARATOR."type_components".DIRECTORY_SEPARATOR;
		foreach($this->data_inputan as $di) {
			$ai = array();
			$name = $di['name'];
			$type = $di['type'];					
			if(!$name) continue;	

			if( !isset($request_all[$name]) ) continue;			

			if($di['required'] && !Request::hasFile($name)) {
				$ai[] = 'required';
			}

			if(file_exists(base_path($componentPath.$type.DIRECTORY_SEPARATOR.'hookInputValidation.php'))) {
				require_once(base_path($componentPath.$type.DIRECTORY_SEPARATOR.'hookInputValidation.php'));
			}

			if(@$di['validation']) {
				$exp = explode('|',$di['validation']);
				if (count($exp)) {
					foreach ($exp as &$validationItem) {
						if (substr($validationItem, 0,6) !== 'unique') {
						    continue;
						}
                        $parseUnique = explode(',',str_replace('unique:','',$validationItem));
                        $uniqueTable = ($parseUnique[0])?:$this->table;
                        $uniqueColumn = ($parseUnique[1])?:$name;
                        $uniqueIgnoreId = ($parseUnique[2])?:(($id)?:'');

                        //Make sure table name
                        $uniqueTable = CB::parseSqlTable($uniqueTable)['table'];

                        //Rebuild unique rule
                        $uniqueRebuild = [];
                        $uniqueRebuild[] = $uniqueTable;
                        $uniqueRebuild[] = $uniqueColumn;
                        if ($uniqueIgnoreId) {
                            $uniqueRebuild[] = $uniqueIgnoreId;
                        } else {
                            $uniqueRebuild[] = 'NULL';
                        }

                        //Check whether deleted_at exists or not
                        if (Schema::hasColumn($uniqueTable,'deleted_at')) {
                            $uniqueRebuild[] = CB::findPrimaryKey($uniqueTable);
                            $uniqueRebuild[] = 'deleted_at';
                            $uniqueRebuild[] = 'NULL';
                        }
                        $uniqueRebuild = array_filter($uniqueRebuild);
                        $validationItem = 'unique:'.implode(',',$uniqueRebuild);
					}
				} else {
					$exp = array();
				}


				$validation = implode('|',$exp);

				$array_input[$name] = $validation;
			}else{
				$array_input[$name] = implode('|',$ai);
			}
		}

		$validator = Validator::make($request_all,$array_input);

		if ($validator->fails())
		{
			$message = $validator->messages();
			$message_all = $message->all();

			if(Request::ajax()) {
				$res = response()->json(['message'=>trans('crudbooster.alert_validation_error',['error'=>implode(', ',$message_all)]),'message_type'=>'warning'])->send();
				exit;
			}
            $res = redirect()->back()->with("errors",$message)->with(['message'=>trans('crudbooster.alert_validation_error',['error'=>implode(', ',$message_all)]),'message_type'=>'warning'])->withInput();
            \Session::driver()->save();
            $res->send();
            exit;


		}
	}

	public function inputAssignment($id=null) {			

		$hide_form = (Request::get('hide_form'))?unserialize(Request::get('hide_form')):array();			
		$componentPath = "vendor".DIRECTORY_SEPARATOR."crocodicstudio".DIRECTORY_SEPARATOR."crudbooster".DIRECTORY_SEPARATOR."src".DIRECTORY_SEPARATOR."views".DIRECTORY_SEPARATOR."default".DIRECTORY_SEPARATOR."type_components".DIRECTORY_SEPARATOR;

		foreach($this->data_inputan as $ro) {
			$name = $ro['name'];
			$type = $ro['type']?:'text';
			$inputdata = Request::get($name);
			
			if(!$name) continue;			
			if($ro['exception']) continue;

			if(count($hide_form) && in_array($name, $hide_form)) {
                continue;
			}			

			if(file_exists( base_path($componentPath.$type.DIRECTORY_SEPARATOR.'hookInputAssignment.php') )) {																								
				require_once(base_path($componentPath.$type.DIRECTORY_SEPARATOR.'hookInputAssignment.php'));
			}

			if(Request::hasFile($name)) {
			   continue;
			}
            if($inputdata!='') {
                $this->arr[$name] = $inputdata;
            }else{
                if(CB::isColumnNULL($this->table,$name)) {
                    continue;
                }

                $this->arr[$name] = "";
            }

		}		
	}

	public function getAdd(){
		$this->cbLoader();

		$page_title      = trans("crudbooster.add_data_page_title",['module'=>CRUDBooster::getCurrentModule()->name]);
		$page_menu       = Route::getCurrentRoute()->getActionName();
		$command 		 = 'add';

		return view('crudbooster::default.form',compact('page_title','page_menu','command'));
	}

	public function postAddSave() {
		$this->cbLoader();

		$this->validation();
		$this->inputAssignment();		

		if(Schema::hasColumn($this->table, 'created_at'))
		{
		    $this->arr['created_at'] = date('Y-m-d H:i:s');
		}

		$this->hookBeforeAdd($this->arr);


		$this->arr[$this->primary_key] = $id = CRUDBooster::newId($this->table);		
		DB::table($this->table)->insert($this->arr);		


		//Looping Data Input Again After Insert
		foreach($this->data_inputan as $ro) {
			$name = $ro['name'];
			if(!$name) continue;

			$inputdata = Request::get($name);

			//Insert Data Checkbox if Type Datatable
			if($ro['type'] == 'checkbox' && $ro['relationship_table']) {
					$datatable = explode(",",$ro['datatable'])[0];
					$foreignKey2 = CRUDBooster::getForeignKey($datatable,$ro['relationship_table']);
					$foreignKey = CRUDBooster::getForeignKey($this->table,$ro['relationship_table']);
					DB::table($ro['relationship_table'])->where($foreignKey,$id)->delete();

					if($inputdata) {
						$relationship_table_pk = CB::pk($ro['relationship_table']);
						foreach($inputdata as $input_id) {
							DB::table($ro['relationship_table'])->insert([
								$relationship_table_pk=>CRUDBooster::newId($ro['relationship_table']),
								$foreignKey=>$id,
								$foreignKey2=>$input_id
								]);
						}
					}
			}


			if($ro['type'] == 'select2' && $ro['relationship_table']) {
					$datatable = explode(",",$ro['datatable'])[0];
					$foreignKey2 = CRUDBooster::getForeignKey($datatable,$ro['relationship_table']);
					$foreignKey = CRUDBooster::getForeignKey($this->table,$ro['relationship_table']);
					DB::table($ro['relationship_table'])->where($foreignKey,$id)->delete();

					if($inputdata) {
						foreach($inputdata as $input_id) {
							$relationship_table_pk = CB::pk($row['relationship_table']);
							DB::table($ro['relationship_table'])->insert([
								$relationship_table_pk=>CRUDBooster::newId($ro['relationship_table']),
								$foreignKey=>$id,
								$foreignKey2=>$input_id
								]);
						}
					}
			}

			if($ro['type']=='child') {
				$name = str_slug($ro['label'],'');
				$columns = $ro['columns'];				
				$count_input_data = count(Request::get($name.'-'.$columns[0]['name']))-1;
				$child_array = [];

				for($i=0;$i<=$count_input_data;$i++) {
					$fk = $ro['foreign_key'];
					$column_data = [];
					$column_data[$fk] = $id;
					foreach($columns as $col) {
						$colname = $col['name'];
						$column_data[$colname] = Request::get($name.'-'.$colname)[$i];
					}
					$child_array[] = $column_data;
				}	

				$childtable = CRUDBooster::parseSqlTable($ro['table'])['table'];
				DB::table($childtable)->insert($child_array);
			}


			
		}


		$this->hookAfterAdd($this->arr[$this->primary_key]);


		$this->return_url = ($this->return_url)?$this->return_url:Request::get('return_url');

		//insert log
		CRUDBooster::insertLog(trans("crudbooster.log_add",['name'=>$this->arr[$this->title_field],'module'=>CRUDBooster::getCurrentModule()->name]));

		if($this->return_url) {
			if(Request::get('submit') == trans('crudbooster.button_save_more')) {
				CRUDBooster::redirect(Request::server('HTTP_REFERER'),trans("crudbooster.alert_add_data_success"),'success');
			}
            CRUDBooster::redirect($this->return_url,trans("crudbooster.alert_add_data_success"),'success');

		}
        if(Request::get('submit') == trans('crudbooster.button_save_more')) {
            CRUDBooster::redirect(CRUDBooster::mainpath('add'),trans("crudbooster.alert_add_data_success"),'success');
        }
        CRUDBooster::redirect(CRUDBooster::mainpath(),trans("crudbooster.alert_add_data_success"),'success');
    }

	public function getEdit($id){
		$this->cbLoader();
		$row             = DB::table($this->table)->where($this->primary_key,$id)->first();

		$page_menu       = Route::getCurrentRoute()->getActionName();
		$page_title 	 = trans("crudbooster.edit_data_page_title",['module'=>CRUDBooster::getCurrentModule()->name,'name'=>$row->{$this->title_field}]);
		$command 		 = 'edit';
		Session::put('current_row_id',$id);
		return view('crudbooster::default.form',compact('id','row','page_menu','page_title','command'));
	}

	public function postEditSave($id) {
		$this->cbLoader();
		$row = DB::table($this->table)->where($this->primary_key,$id)->first();

		$this->validation($id);
		$this->inputAssignment($id);			
			
		if (Schema::hasColumn($this->table, 'updated_at'))
		{
		    $this->arr['updated_at'] = date('Y-m-d H:i:s');
		}
		

		$this->hookBeforeEdit($this->arr,$id);		
		DB::table($this->table)->where($this->primary_key,$id)->update($this->arr);		

		//Looping Data Input Again After Insert
		foreach($this->data_inputan as $ro) {
			$name = $ro['name'];
			if(!$name) continue;

			$inputdata = Request::get($name);

			//Insert Data Checkbox if Type Datatable
			if($ro['type'] == 'checkbox' && $ro['relationship_table']) {
					$datatable = explode(",",$ro['datatable'])[0];

					$foreignKey2 = CRUDBooster::getForeignKey($datatable,$ro['relationship_table']);
					$foreignKey = CRUDBooster::getForeignKey($this->table,$ro['relationship_table']);
					DB::table($ro['relationship_table'])->where($foreignKey,$id)->delete();

					if($inputdata) {
						foreach($inputdata as $input_id) {
							$relationship_table_pk = CB::pk($ro['relationship_table']);
							DB::table($ro['relationship_table'])->insert([
								$relationship_table_pk=>CRUDBooster::newId($ro['relationship_table']),
								$foreignKey=>$id,
								$foreignKey2=>$input_id
								]);
						}
					}
			}


			if($ro['type'] == 'select2' && $ro['relationship_table']) {
					$datatable = explode(",",$ro['datatable'])[0];

					$foreignKey2 = CRUDBooster::getForeignKey($datatable,$ro['relationship_table']);
					$foreignKey = CRUDBooster::getForeignKey($this->table,$ro['relationship_table']);
					DB::table($ro['relationship_table'])->where($foreignKey,$id)->delete();

					if($inputdata) {
						foreach($inputdata as $input_id) {
							$relationship_table_pk = CB::pk($ro['relationship_table']);
							DB::table($ro['relationship_table'])->insert([
								$relationship_table_pk=>CRUDBooster::newId($ro['relationship_table']),
								$foreignKey=>$id,
								$foreignKey2=>$input_id
								]);
						}
					}
			}

			if($ro['type']=='child') {
				$name = str_slug($ro['label'],'');
				$columns = $ro['columns'];				
				$count_input_data = count(Request::get($name.'-'.$columns[0]['name']))-1;
				$child_array = [];
				$childtable = CRUDBooster::parseSqlTable($ro['table'])['table'];
				$fk = $ro['foreign_key'];

				DB::table($childtable)->where($fk,$id)->delete();
				$lastId = CRUDBooster::newId($childtable);
				$childtablePK = CB::pk($childtable);

				for($i=0;$i<=$count_input_data;$i++) {
					
					$column_data = [];
					$column_data[$childtablePK] = $lastId;
					$column_data[$fk] = $id;
					foreach($columns as $col) {
						$colname = $col['name'];
						$column_data[$colname] = Request::get($name.'-'.$colname)[$i];
					}
					$child_array[] = $column_data;

					$lastId++;
				}	

				$child_array = array_reverse($child_array);
				
				DB::table($childtable)->insert($child_array);
			}


		}

		$this->hookAfterEdit($id);


		$this->return_url = ($this->return_url)?$this->return_url:Request::get('return_url');

		//insert log
		CRUDBooster::insertLog(trans("crudbooster.log_update",['name'=>$this->arr[$this->title_field],'module'=>CRUDBooster::getCurrentModule()->name]));

		if($this->return_url) {
			CRUDBooster::redirect($this->return_url,trans("crudbooster.alert_update_data_success"),'success');
		}

        if(Request::get('submit') == trans('crudbooster.button_save_more')) {
            CRUDBooster::redirect(CRUDBooster::mainpath('add'),trans("crudbooster.alert_update_data_success"),'success');
        }

        CRUDBooster::redirect(CRUDBooster::mainpath(),trans("crudbooster.alert_update_data_success"),'success');


	}

	public function getDelete($id) {
		$this->cbLoader();
		$row = DB::table($this->table)->where($this->primary_key,$id)->first();

		//insert log
		CRUDBooster::insertLog(trans("crudbooster.log_delete",['name'=>$row->{$this->title_field},'module'=>CRUDBooster::getCurrentModule()->name]));

		$this->hookBeforeDelete($id);

		if(Schema::hasColumn($this->table,'deleted_at')) {
			DB::table($this->table)->where($this->primary_key,$id)->update(['deleted_at'=>date('Y-m-d H:i:s')]);
		}else{
			DB::table($this->table)->where($this->primary_key,$id)->delete();
		}


		$this->hookAfterDelete($id);

		$url = g('return_url')?:CRUDBooster::referer();

		CRUDBooster::redirect($url,trans("crudbooster.alert_delete_data_success"),'success');
	}

	public function getDetail($id)	{
		$this->cbLoader();
		$row        = DB::table($this->table)->where($this->primary_key,$id)->first();

		$module     = CRUDBooster::getCurrentModule();

		$page_menu  = Route::getCurrentRoute()->getActionName();
		$page_title = trans("crudbooster.detail_data_page_title",['module'=>$module->name,'name'=>$row->{$this->title_field}]);
		$command    = 'detail';

		Session::put('current_row_id',$id);

		return view('crudbooster::default.form',compact('row','page_menu','page_title','command','id'));
	}

	public function getImportData() {
		$this->cbLoader();
		$data['page_menu']       = Route::getCurrentRoute()->getActionName();
		$data['page_title']      = 'Import Data '.$module->name;

		if(!Request::get('file') || Request::get('import')) {
            return view('crudbooster::import',$data);
		}

        $file = base64_decode(Request::get('file'));
        $file = 'storage'.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.$file;
        $rows = Excel::load($file,function($reader) {
        })->get();

        Session::put('total_data_import',count($rows));

        $data_import_column = array();
        foreach($rows as $value) {
            $a = array();
            foreach($value as $k=>$v) {
                $a[] = $k;
            }
            if(count($a)) {
                $data_import_column = $a;
            }
            break;
        }

        $table_columns = DB::getSchemaBuilder()->getColumnListing($this->table);

        $data['table_columns'] = $table_columns;
        $data['data_import_column'] = $data_import_column;


		return view('crudbooster::import',$data);
	}

	public function postDoneImport() {
		$this->cbLoader();
		$data['page_menu']       = Route::getCurrentRoute()->getActionName();
		$data['page_title']      = trans('crudbooster.import_page_title',['module'=>$module->name]);
		Session::put('select_column',Request::get('select_column'));

		return view('crudbooster::import',$data);
	}

	public function postDoImportChunk() {
		$this->cbLoader();
		$file_md5 = md5(Request::get('file'));

		if(Request::get('file') && Request::get('resume')==1) {
			$total = Session::get('total_data_import');
			$prog = intval(Cache::get('success_'.$file_md5)) / $total * 100;
			$prog = round($prog,2);
			if($prog >= 100) {
				Cache::forget('success_'.$file_md5);
			}
			return response()->json(['progress'=> $prog, 'last_error'=>Cache::get('error_'.$file_md5) ]);
		}

		$select_column = Session::get('select_column');
		$select_column = array_filter($select_column);
		$table_columns = DB::getSchemaBuilder()->getColumnListing($this->table);


		$file = base64_decode(Request::get('file'));
		$file = 'storage'.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.$file;
		$rows = Excel::load($file,function($reader) {
		})->get();

		$has_created_at = false;
		if(CRUDBooster::isColumnExists($this->table,'created_at')) {
			$has_created_at = true;
		}

		$data_import_column = array();
		foreach($rows as $value) {
			$a = array();
			foreach($select_column as $sk => $s) {
				$colname = $table_columns[$sk];

				if(CRUDBooster::isForeignKey($colname)) {

					//Skip if value is empty
					if($value->$s == '') continue;

					if(intval($value->$s)) {
						$a[$colname] = $value->$s;
					}else{
						$relation_table = CRUDBooster::getTableForeignKey($colname);
						$relation_moduls = DB::table('cms_moduls')->where('table_name',$relation_table)->first();

						$relation_class = __NAMESPACE__ . '\\' . $relation_moduls->controller;
						if(!class_exists($relation_class)) {
							$relation_class = '\App\Http\Controllers\\'.$relation_moduls->controller;
						}
						$relation_class = new $relation_class;
						$relation_class->cbLoader();

						$title_field = $relation_class->title_field;

						$relation_insert_data = array();
						$relation_insert_data[$title_field] = $value->$s;

						if(CRUDBooster::isColumnExists($relation_table,'created_at')) {
							$relation_insert_data['created_at'] = date('Y-m-d H:i:s');
						}

						try{
							$relation_exists = DB::table($relation_table)->where($title_field,$value->$s)->first();
							if($relation_exists) {
								$relation_primary_key = $relation_class->primary_key;
								$relation_id = $relation_exists->$relation_primary_key;
							}else{
								$relation_id = DB::table($relation_table)->insertGetId($relation_insert_data);
							}

							$a[$colname] = $relation_id;
						}catch(\Exception $e) {
							exit($e);
						}
					} //END IS INT

				}else{
					$a[$colname] = $value->$s;
				}
			}

			$has_title_field = true;
			foreach($a as $k=>$v) {
				if($k == $this->title_field && $v == '') {
					$has_title_field = false;
					break;
				}
			}

			if($has_title_field==false) continue;

			try{

				if($has_created_at) {
					$a['created_at'] = date('Y-m-d H:i:s');
				}

				DB::table($this->table)->insert($a);
				Cache::increment('success_'.$file_md5);
			}catch(\Exception $e) {
				$e = (string) $e;
				Cache::put('error_'.$file_md5,$e,500);
			}
		}
		return response()->json(['status'=>true]);
	}

	public function postDoUploadImportData() {
		$this->cbLoader();
		if (!Request::hasFile('userfile'))
		{
            return redirect()->back();
		}
        $file = Request::file('userfile');
        $ext  = $file->getClientOriginalExtension();


        $validator = Validator::make([ 'extension'=>$ext,], [ 'extension'=>'in:xls,xlsx,csv']);

        if ($validator->fails())
        {
            $message = implode('<br/>',$validator->errors()->all());
            return CRUDBooster::backWithMsg($message, 'warning');
        }

        //Create Directory Monthly
        Storage::makeDirectory('uploads/'.date('Y-m'));

        //Move file to storage
        $filename = md5(str_random(5)).'.'.$ext;
        Storage::putFileAs('uploads/'.date('Y-m'),$file,$filename);
        $url_filename = 'uploads/'.date('Y-m').'/'.$filename;
        $url = CRUDBooster::mainpath('import-data').'?file='.base64_encode($url_filename);
        return redirect($url);
	}

	public function postActionSelected() {
		$this->cbLoader();
		$id_selected = Request::input('checkbox');
		$button_name = Request::input('button_name');

		if(!$id_selected) {
			CRUDBooster::redirect($_SERVER['HTTP_REFERER'],'Please select at least one data!','warning');
		}

		if($button_name == 'delete') {

			$this->hookBeforeDelete($id_selected);
			$tablePK = CB::pk($this->table);
			if(Schema::hasColumn($this->table,'deleted_at')) {
				
				DB::table($this->table)->whereIn($tablePK,$id_selected)->update(['deleted_at'=>date('Y-m-d H:i:s')]);
			}else{
				DB::table($this->table)->whereIn($tablePK,$id_selected)->delete();
			}
			CRUDBooster::insertLog(trans("crudbooster.log_delete",['name'=>implode(',',$id_selected),'module'=>CRUDBooster::getCurrentModule()->name]));

			$this->hookAfterDelete($id_selected);

            return CRUDBooster::backWithMsg(trans("crudbooster.alert_delete_selected_success"));;
		}

		$action = str_replace(['-','_'],' ',$button_name);
		$action = ucwords($action);
		$type = 'success';
		$message = trans("crudbooster.alert_action",['action'=>$action]);

		if($this->actionButtonSelected($id_selected,$button_name) === false) {
		    $message = !empty($this->alert['message']) ? $this->alert['message'] : 'Error';
		    $type = !empty($this->alert['type']) ? $this->alert['type'] : 'danger';
		}
        return CRUDBooster::backWithMsg($message, $type);
	}

	public function getDeleteImage() {
		$this->cbLoader();
		$id     = Request::get('id');
		$column = Request::get('column');

		$row    = DB::table($this->table)->where($this->primary_key,$id)->first();

		$row = DB::table($this->table)->where($this->primary_key,$id)->first();
		$file = $row->{$column};
		if(Storage::exists($file)) {
        	Storage::delete($file);
       	}

       	DB::table($this->table)->where($this->primary_key,$id)->update([$column=>NULL]);

		CRUDBooster::insertLog(trans("crudbooster.log_delete_image",['name'=>$row->{$this->title_field},'module'=>CRUDBooster::getCurrentModule()->name]));

		CRUDBooster::redirect(Request::server('HTTP_REFERER'),trans('crudbooster.alert_delete_data_success'),'success');
	}

	public function postUploadSummernote() {
		$this->cbLoader();
		$name = 'userfile';
		$uploadTypes = explode(',',config('crudbooster.UPLOAD_TYPES'));
		$uploadMaxSize = config('crudbooster.UPLOAD_MAX_SIZE')?:5000;

		if (!Request::hasFile($name))
		{
		    return null;
        }

        $file = Request::file($name);
        $ext  = $file->getClientOriginalExtension();
        $filesize = $file->getClientSize() / 1024;

        if($filesize > $uploadMaxSize) {
            echo "The filesize is too large!";
            exit;
        }

        if(!in_array($ext, $uploadTypes)) {
            echo "The filetype is not allowed!";
            exit;
        }
        
        //Create Directory Monthly
        Storage::makeDirectory(date('Y-m'));
        //Move file to storage
        $filename = md5(str_random(5)).'.'.$ext;
        $file_path = 'uploads'.DIRECTORY_SEPARATOR.date('Y-m');
        Storage::putFileAs($file_path,$file,$filename);
        echo asset('uploads/'.date('Y-m').'/'.$filename);
	}

	public function postUploadFile() {
		$this->cbLoader();
		$name = 'userfile';
		if (!Request::hasFile($name))
		{
		    return null;
        }
        $file = Request::file($name);
        $ext  = $file->getClientOriginalExtension();
        $filesize = $file->getClientSize() / 1024;
        $uploadMaxSize = config('crudbooster.UPLOAD_MAX_SIZE', 5000);

        if($filesize > $uploadMaxSize) {
            echo "The filesize is too large!";
            exit;
        }
        if(!in_array($ext, $uploadTypes)) {
            echo "The filetype is not allowed!";
            exit;
        }
        //Create Directory Monthly
        Storage::makeDirectory(date('Y-m'));

        //Move file to storage
        $filename = md5(str_random(5)).'.'.$ext;
        $file_path = 'uploads'.DIRECTORY_SEPARATOR.date('Y-m');
        Storage::putFileAs($file_path,$file,$filename);
        echo 'uploads/'.date('Y-m').'/'.$filename;
	}

	public function actionButtonSelected($id_selected,$button_name) {
    }

	public function hookQueryIndex(&$query) {
	}

	public function hookRowIndex($index,&$value) {
    }

	public function hookBeforeAdd(&$arr) {
	}

	public function hookAfterAdd($id) {
	}

	public function hookBeforeEdit(&$arr,$id) {
	}

	public function hookAfterEdit($id) {
	}

	public function hookBeforeDelete($id) {
	}

	public function hookAfterDelete($id) {
	}

}
