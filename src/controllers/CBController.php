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

	public $alert                 = array();
	public $index_button          = array();

	public $button_filter       = TRUE;
	public $button_export       = TRUE;
	public $button_import       = TRUE;
	public $button_show         = TRUE;
	public $button_addmore      = TRUE;
	public $button_table_action = TRUE;
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

		$this->columns_table                 = $this->col;
		$this->data_inputan                  = $this->form;
		$this->data['forms']                 = $this->data_inputan;
		$this->data['hide_form'] 			 = $this->hide_form;
		$this->data['addaction']             = ($this->show_addaction)?$this->addaction:NULL;
		$this->data['table']                 = $this->table;
		$this->data['title_field']           = $this->title_field;
		$this->data['appname']               = CRUDBooster::getSetting('appname');
		$this->data['alerts']                = $this->alert;
		$this->data['index_button']          = $this->index_button;
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
		$this->data['button_import']         = $this->button_import;
		$this->data['button_action_width']   = $this->button_action_width;
		$this->data['button_selected']       = $this->button_selected;
		$this->data['index_statistic']       = $this->index_statistic;
		$this->data['index_additional_view'] = $this->index_additional_view;
		$this->data['table_row_color']       = $this->table_row_color;
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
		if(count($this->hide_form)) {
			foreach($this->form as $i=>$f) {
				if(in_array($f['name'], $this->hide_form)) {
					unset($this->form[$i]);
				}
			}
		}
	}

	public function getIndex() {
		$this->cbLoader();

		$module = CRUDBooster::getCurrentModule();

		if(!CRUDBooster::isView() && $this->global_privilege==FALSE) {
			CRUDBooster::insertLog(trans('crudbooster.log_try_view',['module'=>$module->name]));
			CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));
		}

		if(Request::get('parent_table')) {
			$data['parent_table'] = DB::table(Request::get('parent_table'))->where('id',Request::get('parent_id'))->first();
			if(Schema::hasColumn($this->table,'id_'.g('parent_table'))) {
				$data['parent_field'] = $parent_field = 'id_'.g('parent_table');
			}else {
				$data['parent_field'] = $parent_field = g('parent_table').'_id';
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
		$data['page_title']       = $module->name;
		$data['page_description'] = trans('crudbooster.default_module_description');
		$data['date_candidate']   = $this->date_candidate;
		$data['limit'] = $limit   = (Request::get('limit'))?Request::get('limit'):$this->limit;

		// $table_columns 			  = Schema::getColumnListing($this->table);
		$table_columns 	= Cache::remember('columns_'.$this->table,120,function() {
			return Schema::getColumnListing($this->table);
		});
		$result                   = DB::table($this->table)->select(DB::raw($this->table.".".$this->primary_key));

		if(Request::get('parent_id')) {
			if(CRUDBooster::isColumnExists($this->table,'id_'.Request::get('parent_table'))) {
				$result->where($this->table.'.id_'.Request::get('parent_table'),Request::get('parent_id'));
			}elseif (CRUDBooster::isColumnExists($this->table,Request::get('parent_table').'_id')) {
				$result->where($this->table.'.'.Request::get('parent_table').'_id',Request::get('parent_id'));
			}else{
				return redirect()->back()->with(['message'=>'There is no FK for that table','message_type'=>'warning']);
			}
		}


		$this->hook_query_index($result);

		if(in_array('deleted_at', $table_columns)) {
			$result->where($this->table.'.deleted_at',NULL);
		}

		$alias            = array();
		$join_alias_count = 0;
		$join_table_temp  = array();
		$table            = $this->table;
		$columns_table    = $this->columns_table;
		foreach($columns_table as $index => $coltab) {

			$join = @$coltab['join'];
			$field = @$coltab['name'];
			$join_table_temp[] = $table;

			if(!$field) die('Please make sure there is key `name` in each row of col');

			if(strpos($field, ' as ')!==FALSE) {
				$field = substr($field, strpos($field, ' as ')+4);
				$result->addselect(DB::raw($coltab['name']));
				$columns_table[$index]['type_data']   = 'varchar';
				$columns_table[$index]['field']       = $field;
				$columns_table[$index]['field_raw']   = $field;
				$columns_table[$index]['field_with']  = $field;
				$columns_table[$index]['is_subquery'] = true;
				continue;
			}

			if(strpos($field,'.')!==FALSE) {
				$result->addselect($field);
			}else{
				$result->addselect($table.'.'.$field);
			}

			$field_array = explode('.', $field);

			if(isset($field_array[1])) {
				$field = $field_array[1];
				$table = $field_array[0];
			}

			if($join) {

				$join_exp     = explode(',', $join);

				$join_table  = $join_exp[0];
				$join_column = $join_exp[1];
				$join_alias  = str_replace(".", "_", $join_table);

				if(in_array($join_table, $join_table_temp)) {
					$join_alias_count += 1;
					$join_alias = $join_table.$join_alias_count;
				}
				$join_table_temp[] = $join_table;

				$result->leftjoin($join_table.' as '.$join_alias,$join_alias.'.id','=',$table.'.'.$field);
				$result->addselect($join_alias.'.'.$join_column.' as '.$join_alias.'_'.$join_column);

				$join_table_columns = CRUDBooster::getTableColumns($join_table);
				if($join_table_columns) {
					foreach($join_table_columns as $jtc) {
						$result->addselect($join_alias.'.'.$jtc.' as '.$join_alias.'_'.$jtc);
					}
				}

				$alias[] = $join_alias;
				$columns_table[$index]['type_data']	 = CRUDBooster::getFieldType($join_table,$join_column);
				$columns_table[$index]['field']      = $join_alias.'_'.$join_column;
				$columns_table[$index]['field_with'] = $join_alias.'.'.$join_column;
				$columns_table[$index]['field_raw']  = $join_column;

				@$join_table1  = $join_exp[2];
				@$join_column1 = $join_exp[3];
				@$join_alias1  = $join_table1;

				if($join_table1 && $join_column1) {

					if(in_array($join_table1, $join_table_temp)) {
						$join_alias_count += 1;
						$join_alias1 = $join_table1.$join_alias_count;
					}

					$join_table_temp[] = $join_table1;

					$result->leftjoin($join_table1.' as '.$join_alias1,$join_alias1.'.id','=',$join_alias.'.'.$join_column);
					$result->addselect($join_alias1.'.'.$join_column1.' as '.$join_column1.'_'.$join_alias1);
					$alias[] = $join_alias1;
					$columns_table[$index]['type_data']	 = CRUDBooster::getFieldType($join_table1,$join_column1);
					$columns_table[$index]['field']      = $join_column1.'_'.$join_alias1;
					$columns_table[$index]['field_with'] = $join_alias1.'.'.$join_column1;
					$columns_table[$index]['field_raw']  = $join_column1;
				}

			}else{

				$result->addselect($table.'.'.$field);
				$columns_table[$index]['type_data']	 = CRUDBooster::getFieldType($table,$field);
				$columns_table[$index]['field']      = $field;
				$columns_table[$index]['field_raw']  = $field;
				$columns_table[$index]['field_with'] = $table.'.'.$field;
			}
		}

		if(Request::get('q')) {
			$result->where(function($w) use ($columns_table, $request) {
				foreach($columns_table as $col) {
						if(!$col['field_with']) continue;
						if($col['is_subquery']) continue;
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

					if($value=='' || $type=='') continue;

					if($type == 'asc' || $type == 'desc' || $type == 'between') continue;

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

				if($type == 'asc' || $type == 'desc') {
					if($key && $type) $result->orderby($key,$type);
					$filter_is_orderby = true;
				}elseif ($type=='between') {
					if($key && $value) $result->whereBetween($key,$value);
				}else{
					continue;
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
				$addaction[] = [
					'label'=>$s['label'],
					'icon'=>$s['button_icon'],
					'url'=>CRUDBooster::adminPath($s['path']).'?parent_table='.$this->table.'&parent_columns='.$s['parent_columns'].'&parent_id=[id]&return_url='.urlencode(Request::fullUrl()),
					'color'=>$s['button_color']
				];
			}
		}

		$mainpath      = CRUDBooster::mainpath();
		$orig_mainpath = $this->data['mainpath'];
		$title_field   = $this->title_field;
		$html_contents = array();
		foreach($data['result'] as $row) {
			$html_content = array();

			$html_content[] = "<input type='checkbox' class='checkbox' name='checkbox[]' value='$row->id'/>";

			foreach($columns_table as $col) {
		          if($col['visible']===FALSE) continue;

		          $value = @$row->{$col['field']};
		          $title = @$row->{$this->title_field};
		          $label = $col['label'];

		          if(isset($col['image'])) {
			            if($value=='') {
			              $value = "http://placehold.it/50x50&text=NO+IMAGE";
			            }
			            $pic = (strpos($value,'http://')!==FALSE)?$value:asset($value);
			            $pic_small = $pic;
			            $value = "<a class='fancybox' rel='group_{{$table}}' title='$label: $title' href='".$pic."?w=350'><img width='40px' height='40px' src='".$pic_small."?w=40'/></a>";
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

		            if($col['callback_php']) {
		              foreach($row as $k=>$v) {
		              		$col['callback_php'] = str_replace("[".$k."]",$v,$col['callback_php']);
		              }
		              @eval("\$value = ".$col['callback_php'].";");
		            }


		            $datavalue = @unserialize($value);
					if ($datavalue !== false) {
						if($datavalue) {
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
					}

		          $html_content[] = $value;
	        } //end foreach columns_table


	      if($this->button_table_action):

	      		$button_action_style = $this->button_action_style;
	      		$html_content[] = "<div class='button_action' style='text-align:right'>".view('crudbooster::components.action',compact('addaction','row','button_action_style','parent_field'))->render()."</div>";

          endif;//button_table_action


          foreach($html_content as $i=>$v) {
          	$this->hook_row_index($i,$v);
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

	public function postDataQuery() {
		$query = Request::get('query');
		$query = DB::select(DB::raw($query));
		return response()->json($query);
	}

	public function getDataTable() {
		$table = Request::get('table');
		$label = Request::get('label');
		$foreign_key_name = Request::get('fk_name');
		$foreign_key_value = Request::get('fk_value');
		if($table && $label && $foreign_key_name && $foreign_key_value) {
			$query = DB::table($table)->select('id as select_value',$label.' as select_label')->where($foreign_key_name,$foreign_key_value)->orderby($label,'asc')->get();
			return response()->json($query);
		}else{
			return response()->json([]);
		}
	}

	public function getFindData() {
		$q        = Request::get('q');
		$id       = Request::get('id');
		$limit    = Request::get('limit')?:10;
		$format   = Request::get('format');

		$table1   = (Request::get('table1'))?:$this->table;
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
				$rows->join($table2,$table2.'.id','=',$table1.'.'.$column1);
				$columns = CRUDBooster::getTableColumns($table2);
				foreach($columns as $col) {
					$rows->addselect($table2.".".$col." as ".$table2."_".$col);
				}
				$orderby_table  = $table2;
				$orderby_column = $column2;
			}

			if($table3 && $column3) {
				$rows->join($table3,$table3.'.id','=',$table2.'.'.$column2);
				$columns = CRUDBooster::getTableColumns($table3);
				foreach($columns as $col) {
					$rows->addselect($table3.".".$col." as ".$table3."_".$col);
				}
				$orderby_table  = $table3;
				$orderby_column = $column3;
			}

			if($id) {
				$rows->where($table1.".id",$id);
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

	public function validation() {

		$request_all = Request::all();
		$array_input = array();
		$id          = CRUDBooster::getCurrentId();
		$id          = intval($id);
		foreach($this->data_inputan as $di) {
			$ai = array();
			$name = $di['name'];			

			if( !isset($request_all[$name]) ) continue;

			if($di['type'] != 'upload') {
				if(@$di['required']) {
					$ai[] = 'required';
				}
			}

			if($di['type'] == 'upload') {
				if($id) {
					$row = DB::table($this->table)->where($this->primary_key,$id)->first();
					if($row->{$di['name']}=='') {
						$ai[] = 'required';
					}
				}
			}

			if(@$di['min']) {
				$ai[] = 'min:'.$di['min'];
			}
			if(@$di['max']) {
				$ai[] = 'max:'.$di['max'];
			}
			if(@$di['image']) {
				$ai[] = 'image';
			}
			if(@$di['mimes']) {
				$ai[] = 'mimes:'.$di['mimes'];
			}
			$name = $di['name'];
			if(!$name) continue;

			if($di['type']=='money') {
				$request_all[$name] = preg_replace('/[^\d-]+/', '', $request_all[$name]);
			}


			if(@$di['validation']) {
				$exp = explode('|',$di['validation']);
				foreach($exp as &$e) {

					if(strpos($e, 'unique:') !== FALSE) {
						$e = str_replace('unique:','',$e);
						$e_raw = explode(',',$e);

						@$e_table = $e_raw[0];
						@$e_column = $e_raw[1]?:$di['name'];
						@$e_id_ignore = $e_raw[2]?:$id;

						$e = 'unique:'.$e_table.','.$e_column.','.$e_id_ignore;

						if(Schema::hasColumn($e_table,'deleted_at')) {
							$e .= ",id,deleted_at,NULL";
						}

					}

					// if($e == 'image'){
					// 	if($id) {
					// 		$e = NULL;
					// 	}
					// }

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
			}else{
				$res = redirect()->back()->with("errors",$message)->with(['message'=>trans('crudbooster.alert_validation_error',['error'=>implode(', ',$message_all)]),'message_type'=>'warning'])->withInput();
				\Session::driver()->save();
				$res->send();
	        	exit;
			}

		}
	}

	public function input_assignment($id=null) {			

		$hide_form = (Request::get('hide_form'))?unserialize(Request::get('hide_form')):array();

		foreach($this->data_inputan as $ro) {
			$name = $ro['name'];

			if(!$name) continue;

			if($ro['exception']) continue;

			if($name=='hide_form') continue;

			if(count($hide_form)) {
				if(in_array($name, $hide_form)) {
					continue;
				}
			}

			$inputdata = Request::get($name);

			if($ro['type']=='money') {
				$inputdata = preg_replace('/[^\d-]+/', '', $inputdata);
			}

			if($ro['type']=='child') continue;

			if($name) {
				if($inputdata!='') {
					$this->arr[$name] = $inputdata;
				}else{
					$this->arr[$name] = "";
				}
			}

			$password_candidate = explode(',',config('crudbooster.PASSWORD_FIELDS_CANDIDATE'));
			if(in_array($name, $password_candidate)) {
				if(!empty($this->arr[$name])) {
					$this->arr[$name] = Hash::make($this->arr[$name]);
				}else{
					unset($this->arr[$name]);
				}
			}

			if($ro['type']=='checkbox') {
				if(is_array($inputdata)) {
					$this->arr[$name] = implode(";",$inputdata);
				}
			}


			if(@$ro['type']=='upload') {
				// unset($this->arr[$name]);
				if (Request::hasFile($name))
				{
					$file = Request::file($name);
					$ext  = $file->getClientOriginalExtension();

					//Create Directory Monthly
					Storage::makeDirectory(date('Y-m'));

					//Move file to storage
					$filename = md5(str_random(5)).'.'.$ext;
					if($file->move(storage_path('app'.DIRECTORY_SEPARATOR.date('Y-m')),$filename)) {
						$this->arr[$name] = 'uploads/'.date('Y-m').'/'.$filename;
					}
				}

				if(!$this->arr[$name]) {
					$this->arr[$name] = Request::get('_'.$name);
				}
			}

			if(@$ro['type']=='upload') {
				$url = str_replace(asset('/'),'',$this->arr[$name]);
				$this->arr[$name] = $url;
			}
		}
	}

	public function getAddRaw() {
		$this->cbLoader();
		$parent_field = Request::get('parent_field');
		$parent_id    = Request::get('parent_id');
		return view('crudbooster::default.form_body',['parent_field'=>$parent_field,'parent_id'=>$parent_id]);
	}

	public function getAdd(){
		$this->cbLoader();
		if(!CRUDBooster::isCreate() && $this->global_privilege==FALSE || $this->button_add==FALSE) {
			CRUDBooster::insertLog(trans('crudbooster.log_try_add',['module'=>CRUDBooster::getCurrentModule()->name ]));
			CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
		}

		$page_title      = trans("crudbooster.add_data_page_title",['module'=>CRUDBooster::getCurrentModule()->name]);
		$page_menu       = Route::getCurrentRoute()->getActionName();
		$command 		 = 'add';

		return view('crudbooster::default.form',compact('page_title','page_menu','command'));
	}

	public function postAddSave() {
		$this->cbLoader();
		if(!CRUDBooster::isCreate() && $this->global_privilege==FALSE) {
			CRUDBooster::insertLog(trans('crudbooster.log_try_add_save',['name'=>Request::input($this->title_field),'module'=>CRUDBooster::getCurrentModule()->name ]));
			CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
		}

		$this->validation();
		$this->input_assignment();

		if (Schema::hasColumn($this->table, 'created_at'))
		{
		    $this->arr['created_at'] = date('Y-m-d H:i:s');
		}

		$this->hook_before_add($this->arr);

		//Auto Increment
		if(Request::get('temporary')) {
			$this->arr[$this->primary_key] = $id = CRUDBooster::newIdTemporary();
			CRUDBooster::insertTemporary($this->table,$this->arr);
		}else{
			$this->arr[$this->primary_key] = $id = CRUDBooster::newId($this->table);
			DB::table($this->table)->insert($this->arr);
		}

		$this->hook_after_add($this->arr[$this->primary_key]);


		if(Request::get('subtable')) {
			foreach(Request::get('subtable') as $st) {
				$subtable      = $st['table'];
				$fk            = $st['fk'];
				$subtable_data = CRUDBooster::getTemporary($subtable);

				$i = CRUDBooster::newId($subtable);
				foreach($subtable_data as &$s) {
					$s->id = $i;
					$s->$fk = $id;
					foreach($s as $k=>$v) {
						if(CRUDBooster::isForeignKey($k)) {
							unset($s->{$k.'_label'});
						}
					}
					$i++;
				}

				$subtable_data = json_decode(json_encode($subtable_data),true);
				//Bulk Insert
				DB::table($subtable)->insert($subtable_data);
				//Clear Temporary For Related Table
				CRUDBooster::clearTemporary($subtable);
			}
		}


		//Looping Data Input Again After Insert
		foreach($this->data_inputan as $ro) {
			$name = $ro['name'];
			if(!$name) continue;

			$inputdata = Request::get($name);

			//Insert Data Checkbox if Type Datatable
			if($ro['type'] == 'checkbox') {
				if($ro['relationship_table']) {
					$datatable = explode(",",$ro['datatable'])[0];
					$datatable_field = explode(",",$ro['datatable'])[1];
					$foreignKey = CRUDBooster::getForeignKey($this->table,$ro['relationship_table']);
					$foreignKey2 = CRUDBooster::getForeignKey($datatable,$ro['relationship_table']);

					$datatable_query = DB::table($datatable)->whereIn('id',$inputdata)->select('id',$datatable_field.' as label')->get();
					$datatable_input = [];
					foreach($datatable_query as $d) {
						$datatable_input[] = (array) $d;
					}
					$field_temp = serialize($datatable_input);

					if($field_temp) {

						DB::table($this->table)->where("id",$id)->update([$name=>$field_temp]);

						foreach($inputdata as $input_id) {
							DB::table($ro['relationship_table'])->insert([
								'id'=>CRUDBooster::newId($ro['relationship_table']),
								$foreignKey=>$id,
								$foreignKey2=>$input_id
								]);
						}
					}

				}
			}
		}


		$this->return_url = ($this->return_url)?$this->return_url:Request::get('return_url');

		//insert log
		CRUDBooster::insertLog(trans("crudbooster.log_add",['name'=>$this->arr[$this->title_field],'module'=>CRUDBooster::getCurrentModule()->name]));

		if($this->return_url) {
			if(Request::get('submit') == trans('crudbooster.button_save_more')) {
				CRUDBooster::redirect(Request::server('HTTP_REFERER'),trans("crudbooster.alert_add_data_success"),'success');
			}else{
				CRUDBooster::redirect($this->return_url,trans("crudbooster.alert_add_data_success"),'success');
			}

		}else{
			if(Request::get('submit') == trans('crudbooster.button_save_more')) {
				CRUDBooster::redirect(CRUDBooster::mainpath('add'),trans("crudbooster.alert_add_data_success"),'success');
			}else{
				CRUDBooster::redirect(CRUDBooster::mainpath(),trans("crudbooster.alert_add_data_success"),'success');
			}
		}
	}

	public function getEditRaw($id) {
		$this->cbLoader();
		$parent_field = Request::get('parent_field');
		$parent_id    = Request::get('parent_id');
		if(Request::get('temporary')) {
			$row          = CRUDBooster::firstTemporary($this->table,$id);
		}else{
			$row          = DB::table($this->table)->where($this->primary_key,$id)->first();
		}

		Session::put('current_row_id',$id);
		return view('crudbooster::default.form_body',['row'=>$row,'id'=>$id,'parent_field'=>$parent_field,'parent_id'=>$parent_id]);
	}

	public function getEdit($id){
		$this->cbLoader();
		$row             = DB::table($this->table)->where($this->primary_key,$id)->first();

		if(!CRUDBooster::isRead() && $this->global_privilege==FALSE || $this->button_edit==FALSE) {
			CRUDBooster::insertLog(trans("crudbooster.log_try_edit",['name'=>$row->{$this->title_field},'module'=>CRUDBooster::getCurrentModule()->name]));
			CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));
		}


		$page_menu       = Route::getCurrentRoute()->getActionName();
		$page_title 	 = trans("crudbooster.edit_data_page_title",['module'=>CRUDBooster::getCurrentModule()->name,'name'=>$row->{$this->title_field}]);
		$command 		 = 'edit';
		Session::put('current_row_id',$id);
		return view('crudbooster::default.form',compact('id','row','page_menu','page_title','command'));
	}

	public function postEditSave($id) {
		$this->cbLoader();
		$row = DB::table($this->table)->where($this->primary_key,$id)->first();

		if(!CRUDBooster::isUpdate() && $this->global_privilege==FALSE) {
			CRUDBooster::insertLog(trans("crudbooster.log_try_add",['name'=>$row->{$this->title_field},'module'=>CRUDBooster::getCurrentModule()->name]));
			CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));
		}

		$this->validation();
		$this->input_assignment($id);		

		if (Schema::hasColumn($this->table, 'updated_at'))
		{
		    $this->arr['updated_at'] = date('Y-m-d H:i:s');
		}

		$this->hook_before_edit($this->arr,$id);

		if(Request::get('temporary')) {
			CRUDBooster::updateTemporary($this->table,['id'=>$id],$this->arr);
		}else{

			DB::table($this->table)->where($this->primary_key,$id)->update($this->arr);
		}


		$this->hook_after_edit($id);

		//Looping Data Input Again After Insert
		foreach($this->data_inputan as $ro) {
			$name = $ro['name'];
			if(!$name) continue;

			$inputdata = Request::get($name);

			//Insert Data Checkbox if Type Datatable
			if($ro['type'] == 'checkbox') {
				if($ro['relationship_table']) {
					$datatable = explode(",",$ro['datatable'])[0];
					$datatable_field = explode(",",$ro['datatable'])[1];
					$foreignKey = CRUDBooster::getForeignKey($this->table,$ro['relationship_table']);
					$foreignKey2 = CRUDBooster::getForeignKey($datatable,$ro['relationship_table']);

					DB::table($ro['relationship_table'])->where($foreignKey,$id)->delete();

					$datatable_query = DB::table($datatable)->whereIn('id',$inputdata)->select('id',$datatable_field.' as label')->get();
					$datatable_input = [];
					foreach($datatable_query as $d) {
						$datatable_input[] = (array) $d;
					}
					$field_temp = serialize($datatable_input);

					if($field_temp) {
						DB::table($this->table)->where("id",$id)->update([$name=>$field_temp]);
					}

					foreach($inputdata as $input_id) {
						DB::table($ro['relationship_table'])->insert([
							'id'=>CRUDBooster::newId($ro['relationship_table']),
							$foreignKey=>$id,
							$foreignKey2=>$input_id
							]);
					}

				}
			}
		}


		$this->return_url = ($this->return_url)?$this->return_url:Request::get('return_url');

		//insert log
		CRUDBooster::insertLog(trans("crudbooster.log_update",['name'=>$this->arr[$this->title_field],'module'=>CRUDBooster::getCurrentModule()->name]));

		if($this->return_url) {
			CRUDBooster::redirect($this->return_url,trans("crudbooster.alert_update_data_success"),'success');
		}else{
			if(Request::get('submit') == trans('crudbooster.button_save_more')) {
				CRUDBooster::redirect(CRUDBooster::mainpath('add'),trans("crudbooster.alert_update_data_success"),'success');
			}else{
				CRUDBooster::redirect(CRUDBooster::mainpath(),trans("crudbooster.alert_update_data_success"),'success');
			}
		}
	}

	public function getDelete($id) {
		$this->cbLoader();
		$row = DB::table($this->table)->where($this->primary_key,$id)->first();

		if(!CRUDBooster::isDelete() && $this->global_privilege==FALSE || $this->button_delete==FALSE) {
			CRUDBooster::insertLog(trans("crudbooster.log_try_delete",['name'=>$row->{$this->title_field},'module'=>CRUDBooster::getCurrentModule()->name]));
			CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));
		}


		//insert log
		CRUDBooster::insertLog(trans("crudbooster.log_delete",['name'=>$row->{$this->title_field},'module'=>CRUDBooster::getCurrentModule()->name]));

		$this->hook_before_delete($id);

		if(Request::get('temporary')) {
			CRUDBooster::deleteTemporary($this->table,$id);
		}else{

			if(Schema::hasColumn($this->table,'deleted_at')) {
				DB::table($this->table)->where($this->primary_key,$id)->update(['deleted_at'=>date('Y-m-d H:i:s')]);
			}else{
				DB::table($this->table)->where($this->primary_key,$id)->delete();
			}
		}


		$this->hook_after_delete($id);

		$url = g('return_url')?:CRUDBooster::referer();

		CRUDBooster::redirect($url,trans("crudbooster.alert_delete_data_success"),'success');
	}

	public function getDetail($id)	{
		$this->cbLoader();
		$row        = DB::table($this->table)->where($this->primary_key,$id)->first();

		if(!CRUDBooster::isRead() && $this->global_privilege==FALSE || $this->button_detail==FALSE) {
			CRUDBooster::insertLog(trans("crudbooster.log_try_view",['name'=>$row->{$this->title_field},'module'=>CRUDBooster::getCurrentModule()->name]));
			CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));
		}

		$module     = CRUDBooster::getCurrentModule();

		$page_menu  = Route::getCurrentRoute()->getActionName();
		$page_title = trans("crudbooster.detail_data_page_title",['module'=>$module->name,'name'=>$row->{$this->title_field}]);
		$command    = 'detail';

		Session::put('current_row_id',$id);

		return view('crudbooster::default.form',compact('row','page_menu','page_title','command'));
	}

	public function getImportData() {
		$this->cbLoader();
		$data['page_menu']       = Route::getCurrentRoute()->getActionName();
		$data['page_title']      = 'Import Data '.$module->name;

		if(Request::get('file') && !Request::get('import')) {
			$file = base64_decode(Request::get('file'));
			$file = trim(str_replace('uploads','app',$file),'/');
			$file = storage_path($file);
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
		}


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
		$file = trim(str_replace('uploads','app',$file),'/');
		$file = storage_path($file);

		$rows = Excel::load($file,function($reader) {
		})->get();

		$has_created_at = false;
		if(Schema::hasColumn($this->table,'created_at')) {
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
		if (Request::hasFile('userfile'))
		{
			$file = Request::file('userfile');
			$ext  = $file->getClientOriginalExtension();


			$validator = Validator::make([
				'extension'=>$ext,
				],[
				'extension'=>'in:xls,xlsx,csv'
				]);

		    if ($validator->fails())
		    {
		        $message = $validator->errors()->all();
		        return redirect()->back()->with(['message'=>implode('<br/>',$message),'message_type'=>'warning']);
		    }

			//Create Directory Monthly
			Storage::makeDirectory(date('Y-m'));

			//Move file to storage
			$filename = md5(str_random(5)).'.'.$ext;
			$url_filename = '';
			if($file->move(storage_path('app'.DIRECTORY_SEPARATOR.date('Y-m')),$filename)) {
				$url_filename = 'uploads/'.date('Y-m').'/'.$filename;
			}
			$url = CRUDBooster::mainpath('import-data').'?file='.base64_encode($url_filename);
			return redirect($url);
		}else{
			return redirect()->back();
		}
	}

	public function postActionSelected() {
		$this->cbLoader();
		$id_selected = Request::input('checkbox');
		$button_name = Request::input('button_name');

		if($button_name == 'delete') {
			if(!CRUDBooster::isDelete()) {
				CRUDBooster::insertLog(trans("crudbooster.log_try_delete_selected",['module'=>CRUDBooster::getCurrentModule()->name]));
				CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));
			}

			$this->hook_before_delete($id_selected);

			if(Schema::hasColumn($this->table,'deleted_at')) {
				DB::table($this->table)->whereIn('id',$id_selected)->update(['deleted_at'=>date('Y-m-d H:i:s')]);
			}else{
				DB::table($this->table)->whereIn('id',$id_selected)->delete();
			}
			CRUDBooster::insertLog(trans("crudbooster.log_delete",['name'=>implode(',',$id_selected),'module'=>CRUDBooster::getCurrentModule()->name]));

			$this->hook_after_delete($id_selected);

			$message = trans("crudbooster.alert_delete_selected_success");
			return redirect()->back()->with(['message_type'=>'success','message'=>$message]);
		}

		$this->actionButtonSelected($id_selected,$button_name);

		$action = str_replace(['-','_'],' ',$button_name);
		$action = ucwords($action);
		$message = trans("crudbooster.alert_action",['action'=>$action]);
		return redirect()->back()->with(['message_type'=>'success','message'=>$message]);
	}

	public function getDeleteImage() {
		$this->cbLoader();
		$id     = Request::get('id');
		$column = Request::get('column');

		$row    = DB::table($this->table)->where($this->primary_key,$id)->first();

		if(!CRUDBooster::isDelete() && $this->global_privilege==FALSE) {
			CRUDBooster::insertLog(trans("crudbooster.log_try_delete_image",['name'=>$row->{$this->title_field},'module'=>CRUDBooster::getCurrentModule()->name]));
			CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));
		}

		$row = DB::table($this->table)->where($this->primary_key,$id)->first();

		$file = str_replace('uploads/','',$row->{$column});
		if(Storage::exists($file)) {
        	Storage::delete($file);
       	}

       	if(Request::get('temporary')) {
       		CRUDBooster::updateTemporary($this->table,['id'=>$id],[$column=>'']);
       	}else{
			DB::table($this->table)->where($this->primary_key,$id)->update([$column=>NULL]);
       	}

		CRUDBooster::insertLog(trans("crudbooster.log_delete_image",['name'=>$row->{$this->title_field},'module'=>CRUDBooster::getCurrentModule()->name]));

		CRUDBooster::redirect(Request::server('HTTP_REFERER'),trans('crudbooster.alert_delete_data_success'),'success');
	}

	public function postUploadSummernote() {
		$this->cbLoader();
		$name = 'userfile';
		if (Request::hasFile($name))
		{
			$file = Request::file($name);
			$ext  = $file->getClientOriginalExtension();

			//Create Directory Monthly
			Storage::makeDirectory(date('Y-m'));

			//Move file to storage
			$filename = md5(str_random(5)).'.'.$ext;
			if($file->move(storage_path('app'.DIRECTORY_SEPARATOR.date('Y-m')),$filename)) {
				echo asset('uploads/'.date('Y-m').'/'.$filename);
			}
		}
	}

	public function actionButtonSelected($id_selected,$button_name) {
    }

	public function hook_query_index(&$query) {
	}

	public function hook_row_index($index,&$value) {
    }

	public function hook_before_add(&$arr) {
	}

	public function hook_after_add($id) {
	}

	public function hook_before_edit(&$arr,$id) {
	}

	public function hook_after_edit($id) {
	}

	public function hook_before_delete($id) {
	}

	public function hook_after_delete($id) {
	}

}
