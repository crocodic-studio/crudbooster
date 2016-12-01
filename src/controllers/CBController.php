<?php namespace crocodicstudio\crudbooster\controllers;
	
error_reporting(E_ALL ^ E_NOTICE);

use crocodicstudio\crudbooster\controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
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
	public $orderby            = array();
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
	public $script_js             = NULL;
	public $sub_module            = array();	
	public $index_array           = FALSE;
	public $index_only_id         = NULL;	
	public $show_addaction        = TRUE;
	public $table_row_color 	  = array();
	public $button_selected 	  = array();
	public $return_url 			  = NULL;

	public function constructor() {					 				

		$this->columns_table     = $this->col; 		
		$this->data_inputan      = $this->form;
		$this->data['forms']     = $this->data_inputan;					
		$this->data['addaction'] = ($this->show_addaction)?$this->addaction:NULL;					
		
		$privileges              = CRUDBooster::myPrivilege();							
						
		$this->data['table']                 = $this->table;		
		$this->data['page_title']            = @$privileges->name;
		$this->data['title_field']           = $this->title_field;
		$this->data['appname']               = CRUDBooster::getSetting('appname');					
		$this->data['alerts']                = $this->alert;
		$this->data['index_button']          = $this->index_button;
		$this->data['button_detail']         = $this->button_detail;		
		$this->data['button_edit']         	 = $this->button_edit;		
		$this->data['button_show']           = $this->button_show;		
		$this->data['button_add']            = $this->button_add;
		$this->data['button_delete']         = $this->button_delete;
		$this->data['button_filter']    	 = $this->button_filter;		
		$this->data['button_export']         = $this->button_export;
		$this->data['button_addmore']        = $this->button_addmore;
		$this->data['button_cancel']         = $this->button_cancel;
		$this->data['button_save']           = $this->button_save;
		$this->data['button_table_action']   = $this->button_table_action;
		$this->data['button_import']         = $this->button_import;
		$this->data['button_action_width']   = $this->button_action_width;
		$this->data['button_selected']   	 = $this->button_selected;
		$this->data['index_statistic']       = $this->index_statistic;
		$this->data['index_additional_view'] = $this->index_additional_view;
		$this->data['table_row_color']       = $this->table_row_color;
		$this->data['load_js']               = $this->load_js;
		$this->data['script_js']             = $this->script_js;				

        view()->share($this->data);
	} 

	public function getIndex(Request $request) {
		$module = CRUDBooster::getCurrentModule();

		if(!CRUDBooster::isView() && $this->global_privilege==FALSE) {
			CRUDBooster::insertLog(trans('crudbooster.log_try_view',['module'=>$module->name]));
			CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));			
		}
		
		$data['table'] 	  = $this->table;
		$data['page_title']       = $module->name;
		$data['page_description'] = trans('crudbooster.default_module_description');		
		$data['date_candidate']   = $this->date_candidate;
		$data['limit'] = $limit   = ($request->get('limit'))?$request->get('limit'):$this->limit;

		// $table_columns 			  = Schema::getColumnListing($this->table);		
		$table_columns 	= Cache::remember('columns_'.$this->table,120,function() {
			return Schema::getColumnListing($this->table);
		});			
		$result                   = DB::table($this->table)->select(DB::raw($this->table.".".$this->primary_key));
				
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
				$join_alias  = $join_table;								

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

		if($request->get('q')) {			
			$result->where(function($w) use ($columns_table, $request) {
				foreach($columns_table as $col) {		
						if(!$col['field_with']) continue;		
						if($col['is_subquery']) continue;			
						$w->orwhere($col['field_with'],"like","%".$request->get("q")."%");				
				}
			});		
		}			

		if($request->get('where')) {			
			foreach($request->get('where') as $k=>$v) {
				$result->where($table.'.'.$k,$v); 
			}			
		}
		
		$filter_is_orderby = false;
		if($request->get('filter_column')) {

			$filter_column = $request->get('filter_column');
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
				foreach($this->orderby as $k=>$v) {
					if(strpos($k, '.')!==FALSE) {
						$orderby_table = explode(".",$k)[0];
					}else{
						$orderby_table = $table;
					}
					$result->orderby($orderby_table.'.'.$k,$v);
				}
				$data['result'] = $result->paginate($limit);
			}else{
				$data['result'] = $result->orderby($this->table.'.'.$this->primary_key,'desc')->paginate($limit);	
			}
		}
								
		$data['columns'] = $columns_table;

		//LISTING INDEX HTML
		$addaction     = $this->data['addaction'];
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
		          }else if(@$col['download']) {
			            $url = (strpos($value,'http://')!==FALSE)?$value:asset($value).'?download=1';
			            if($value) {
			            	$value = "<a class='btn btn-xs btn-primary' href='$url' target='_blank' title='Download File'><i class='fa fa-download'></i> Download</a>";
			            }else{
			            	$value = " - ";
			            }
		          }else{

			            //limit character
			            if($col['str_limit']) {
			            	$value = trim(strip_tags($value));
			            	$value = str_limit($value,$col['str_limit']);
			            }

			            if($col['nl2br']) {
			            	$value = nl2br($value);
			            }
			            
			            if(isset($col['callback_php'])) {			              
			              foreach($row as $k=>$v) {
			              		$col['callback_php'] = str_replace("[".$k."]",$v,$col['callback_php']);			              		
			              }
			              @eval("\$value = ".$col['callback_php'].";");			              
			            }				                                            		          
		          }                

		          $html_content[] = $value;
	        } //end foreach columns_table


	      if($this->button_table_action):	      		      		      

	      		$button_action_style = $this->button_action_style;
	      		$html_content[] = "<div style='text-align:right'>".view('crudbooster::components.action',compact('addaction','row','button_action_style'))->render()."</div>";		      	         	
	          
          endif;//button_table_action


          $this->hook_row_index($html_content);

	      $html_contents[] = $html_content;
		} //end foreach data[result]
		
 		
 		$html_contents = ['html'=>$html_contents,'data'=>$data['result']];				

		$data['html_contents'] = $html_contents;

		return view("crudbooster::default.index",$data);
		
	}

	public function getExportData() {
		return redirect(CRUDBooster::mainpath());
	}

	public function postExportData(Request $request) {
		
		$filetype 			= $request->input('fileformat');
		$filename 			= $request->input('filename');
		$papersize			= $request->input('page_size');
		$paperorientation	= $request->input('page_orientation');	
	
		if($request->input('default_paper_size')) {
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

	public function postDataQuery(Request $request) {
		$query = $request->get('query');
		$query = DB::select(DB::raw($query));
		return response()->json($query);
	}

	public function getDataTable(Request $request) {
		$table = $request->get('table');
		$label = $request->get('label');
		$foreign_key_name = $request->get('fk_name');
		$foreign_key_value = $request->get('fk_value');
		if($table && $label && $foreign_key_name && $foreign_key_value) {
			$query = DB::table($table)->select('id as select_value',$label.' as select_label')->where($foreign_key_name,$foreign_key_value)->orderby($label,'asc')->get();
			return response()->json($query);
		}else{
			return response()->json([]);
		}
	}

	public function getFindData(Request $request) {
		$q        = $request->get('q');
		$id       = $request->get('id');
		$limit    = $request->get('limit')?:10;
		$format   = $request->get('format');
		
		$table1   = ($request->get('table1'))?:$this->table;
		$column1  = ($request->get('column1'))?:$this->title_field;
		
		@$table2  = $request->get('table2');
		@$column2 = $request->get('column2');
		
		@$table3  = $request->get('table3');
		@$column3 = $request->get('column3');
		
		$where    = $request->get('where');

		$fk 	  = $request->get('fk');
		$fk_value = $request->get('fk_value');

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
				$columns = get_columns_table($table2);
				foreach($columns as $col) {
					$rows->addselect($table2.".".$col." as ".$table2."_".$col);
				}								
				$orderby_table  = $table2;
				$orderby_column = $column2;										
			}													

			if($table3 && $column3) {
				$rows->join($table3,$table3.'.id','=',$table2.'.'.$column2);
				$columns = get_columns_table($table3);
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

	public function validation(Request $request) { 

		$request_all = $request->all();
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

					if($e == 'image'){
						if($id) {
							$e = NULL;
						}
					}
					
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
			$res = redirect()->back()->with("errors",$message)->with(['message'=>'Ups please complete the form','message_type'=>'warning'])->withInput();
			\Session::driver()->save();
			$res->send();
        	exit();
		}
	}

	public function input_assignment(Request $request,$id=null) {		

		foreach($this->data_inputan as $ro) {
			$name = $ro['name'];

			if(!$name) continue;

			if($ro['exception']) continue;

			$inputdata = $request->get($name);

			if($ro['type']=='money') {
				$inputdata = preg_replace('/[^\d-]+/', '', $inputdata); 
			}

			if(isset($ro['onlyfor'])) {
				if(is_array($ro['onlyfor'])) {
					if(in_array($request->session()->get('admin_privileges_name'), $ro['onlyfor'])) {
						$this->arr[$name] = $inputdata;
					}else{
						continue;
					}
				}else{
					if($request->session()->get('admin_privileges_name') == $ro['onlyfor']) {
						$this->arr[$name] = $inputdata;
					}else{
						continue;
					}
				}
			}

			if($name && isset($inputdata)) {
				$this->arr[$name] = $inputdata;
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
				$this->arr[$name] = implode(";",$inputdata);
			}

			if(@$ro['type']=='upload') {				
				unset($this->arr[$name]);
				if ($request->hasFile($name))
				{			
					$file = $request->file($name);					
					$ext  = $file->getClientOriginalExtension();

					//Create Directory Monthly 
					Storage::makeDirectory(date('Y-m'));

					//Move file to storage
					$filename = md5(str_random(5)).'.'.$ext;
					if($file->move(storage_path('app'.DIRECTORY_SEPARATOR.date('Y-m')),$filename)) {						
						$this->arr[$name] = 'uploads/'.date('Y-m').'/'.$filename;
					}					  
				}
			}

			if(@$ro['type']=='upload') {
				$url = str_replace(asset('/'),'',$this->arr[$name]);
				$this->arr[$name] = $url;
			}			
		}

	}

	public function getAdd(){

		if(!CRUDBooster::isCreate() && $this->global_privilege==FALSE || $this->button_add==FALSE) {			
			CRUDBooster::insertLog(trans('crudbooster.log_try_add',['module'=>CRUDBooster::getCurrentModule()->name ]));			
			CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
		}

		$page_title      = trans("crudbooster.add_data_page_title",['module'=>CRUDBooster::getCurrentModule()->name]);
		$page_menu       = Route::getCurrentRoute()->getActionName();		
		$command 		 = 'add';
		return view('crudbooster::default.form',compact('page_title','page_menu','command'));			
	}
	
	public function postAddSave(Request $request) {

		// dd($request->all());		

		if(!CRUDBooster::isCreate() && $this->global_privilege==FALSE || $this->button_add==FALSE) {			
			CRUDBooster::insertLog(trans('crudbooster.log_try_add_save',['name'=>$request->input($this->title_field),'module'=>CRUDBooster::getCurrentModule()->name ]));			
			CRUDBooster::redirect(CRUDBooster::adminPath(),trans("crudbooster.denied_access"));
		}

		$this->validation($request);	
		$this->input_assignment($request);

		if (Schema::hasColumn($this->table, 'created_at')) 
		{
		    $this->arr['created_at'] = date('Y-m-d H:i:s');
		}	

		$this->hook_before_add($this->arr);	

		//Auto Increment
		$this->arr[$this->primary_key] = DB::table($this->table)->max($this->primary_key) + 1;	

		DB::table($this->table)->insert($this->arr);		

		$this->hook_after_add($this->arr['id']);

		//Insert Children
		foreach($this->data_inputan as $d) {
			if($d['type'] == 'child') {
				$fields = $d['fields'];
				$table = $d['table'];
				$fk = $d['foreign_key'];
				if($request->get('child_'.$table)) {
					foreach($request->get('child_'.$table) as $child_row) {
						$child_row = json_decode($child_row,true);
						$child_row[$fk] = $this->arr[$this->primary_key];
						CRUDBooster::insert($table,$child_row);
					}
				}
				Session::forget('session_child_data_'.$table);
			}
		}

		$this->return_url = ($this->return_url)?$this->return_url:$request->get('return_url');

		//insert log		
		CRUDBooster::insertLog(trans("crudbooster.log_add",['name'=>$this->arr[$this->title_field],'module'=>CRUDBooster::getCurrentModule()->name]));

		if($this->return_url) {			
			CRUDBooster::redirect($this->return_url,trans("crudbooster.alert_add_data_success"),'success');
		}else{
			if($request->get('submit') == trans('crudbooster.button_save_more')) {
				CRUDBooster::redirect(CRUDBooster::mainpath('add'),trans("crudbooster.alert_add_data_success"),'success');
			}else{
				CRUDBooster::redirect(CRUDBooster::mainpath(),trans("crudbooster.alert_add_data_success"),'success');
			}
		}		
	}

	
	
	public function getEdit(Request $request,$id){		
		
		$row             = DB::table($this->table)->where($this->primary_key,$id)->first();	

		if(!CRUDBooster::isRead() && $this->global_privilege==FALSE || $this->button_edit==FALSE) {			
			CRUDBooster::insertLog(trans("crudbooster.log_try_edit",['name'=>$row->{$this->title_field},'module'=>CRUDBooster::getCurrentModule()->name]));
			CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));			
		}

			
		$page_menu       = Route::getCurrentRoute()->getActionName();				
		$page_title 	 = trans("crudbooster.edit_data_page_title",['module'=>CRUDBooster::getCurrentModule()->name,'name'=>$row->{$this->title_field}]);		
		$command 		 = 'edit';
		$request->session()->put('current_row_id',$id);
		return view('crudbooster::default.form',compact('id','row','page_menu','page_title','command'));
	}

	public function getImportData(Request $request) {
		$data['page_menu']       = Route::getCurrentRoute()->getActionName();		
		$data['page_title']      = 'Import Data '.$module->name;

		if($request->get('file') && !$request->get('import')) {
			$file = base64_decode($request->get('file'));
			$file = trim(str_replace('uploads','app',$file),'/');
			$file = storage_path($file);
			$rows = Excel::load($file,function($reader) {				
			})->get();

			$request->session()->put('total_data_import',count($rows));
			
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
		$data['page_menu']       = Route::getCurrentRoute()->getActionName();		
		$data['page_title']      = trans('crudbooster.import_page_title',['module'=>$module->name]);	
		Session::put('select_column',Request::get('select_column'));

		return view('crudbooster::import',$data);
	}

	public function postDoImportChunk(Request $request) {
		$file_md5 = md5($request->get('file'));

		if($request->get('file') && $request->get('resume')==1) {
			$total = $request->session()->get('total_data_import');
			$prog = intval(Cache::get('success_'.$file_md5)) / $total * 100;
			$prog = round($prog,2);
			if($prog >= 100) {
				Cache::forget('success_'.$file_md5);
			}
			return response()->json(['progress'=> $prog, 'last_error'=>Cache::get('error_'.$file_md5) ]);
		}

		$select_column = $request->session()->get('select_column');
		$select_column = array_filter($select_column);
		$table_columns = DB::getSchemaBuilder()->getColumnListing($this->table);

		
		$file = base64_decode($request->get('file'));
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

				if(substr($colname,0,3) == 'id_') {

					//Skip if value is empty
					if($value->$s == '') continue;

					$relation_table = substr($colname,3);
					$relation_moduls = DB::table('cms_moduls')->where('table',$relation_table)->first();

					$relation_class = __NAMESPACE__ . '\\' . $relation_moduls->controller;
					if(!class_exists($relation_class)) {
						$relation_class = '\App\Http\Controllers\\'.$relation_moduls->controller;
					}
					$relation_class = new $relation_class;

					$title_field = $relation_class->title_field();

					$relation_insert_data = array();
					$relation_insert_data[$title_field] = $value->$s;

					if(Schema::hasColumn($relation_table,'created_at')) {
						$relation_insert_data['created_at'] = date('Y-m-d H:i:s');
					}

					try{
						$relation_exists = DB::table($relation_table)->where($title_field,$value->$s)->first();
						if($relation_exists) {
							$relation_primary_key = $relation_class->primary_key();
							$relation_id = $relation_exists->$relation_primary_key;
						}else{
							$relation_id = DB::table($relation_table)->insertGetId($relation_insert_data);
						}						

						$a[$colname] = $relation_id;
					}catch(\Exception $e) {

					}				
				}else{
					$a[$colname] = $value->$s;
				}				
			}	

			$has_title_field = true;
			foreach($a as $k=>$v) {
				if($k == $this->title_field() && $v == '') {
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

	public function postDoUploadImportData(Request $request) {
	
		if ($request->hasFile('userfile'))
		{			
			$file = $request->file('userfile');					
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

	public function getDetail($id)	{			
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
	 
	public function postEditSave(Request $request,$id) {

		$row = DB::table($this->table)->where($this->primary_key,$id)->first();	

		if(!CRUDBooster::isUpdate() && $this->global_privilege==FALSE || $this->button_detail==FALSE) {			
			CRUDBooster::insertLog(trans("crudbooster.log_try_add",['name'=>$row->{$this->title_field},'module'=>CRUDBooster::getCurrentModule()->name]));
			CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));			
		}
		
		$this->validation($request);
		$this->input_assignment($request,$id);

		if (Schema::hasColumn($this->table, 'updated_at')) 
		{
		    $this->arr['updated_at'] = date('Y-m-d H:i:s');
		}

		$this->hook_before_edit($this->arr,$id);
		
		DB::table($this->table)->where($this->primary_key,$id)->update($this->arr);

		$this->hook_after_edit($id);

		$this->return_url = ($this->return_url)?$this->return_url:$request->get('return_url');		

		//insert log		
		CRUDBooster::insertLog(trans("crudbooster.log_update",['name'=>$this->arr[$this->title_field],'module'=>CRUDBooster::getCurrentModule()->name]));

		if($this->return_url) {			
			CRUDBooster::redirect($this->return_url,trans("crudbooster.alert_update_data_success"),'success');
		}else{
			if($request->get('submit') == trans('crudbooster.button_save_more')) {
				CRUDBooster::redirect(CRUDBooster::mainpath('add'),trans("crudbooster.alert_update_data_success"),'success');
			}else{
				CRUDBooster::redirect(CRUDBooster::mainpath(),trans("crudbooster.alert_update_data_success"),'success');
			}
		}
	}
	
	public function getDelete($id) {

		$row = DB::table($this->table)->where($this->primary_key,$id)->first();	

		if(!CRUDBooster::isDelete() && $this->global_privilege==FALSE || $this->button_delete==FALSE) {			
			CRUDBooster::insertLog(trans("crudbooster.log_try_delete",['name'=>$row->{$this->title_field},'module'=>CRUDBooster::getCurrentModule()->name]));			
			CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));
		}

			
		//insert log		
		CRUDBooster::insertLog(trans("crudbooster.log_delete",['name'=>$row->{$this->title_field},'module'=>CRUDBooster::getCurrentModule()->name]));

		$this->hook_before_delete($id);

		if(Schema::hasColumn($this->table,'deleted_at')) {
			DB::table($this->table)->where($this->primary_key,$id)->update(['deleted_at'=>date('Y-m-d H:i:s')]);
		}else{
			DB::table($this->table)->where($this->primary_key,$id)->delete();
		}
		
		$this->hook_after_delete($id);

		$url = g('return_url')?:CRUDBooster::referer();

		CRUDBooster::redirect($url,trans("crudbooster.alert_delete_data_success"),'success');		
	}

	public function postActionSelected(Request $request) {

		$id_selected = $request->input('checkbox');
		$button_name = $request->input('button_name');

		if($button_name == 'deleted') {
			if(!CRUDBooster::isDelete()) {
				CRUDBooster::insertLog(trans("crudbooster.log_try_delete_selected",['module'=>CRUDBooster::getCurrentModule()->name]));
				CRUDBooster::redirect(CRUDBooster::adminPath(),trans('crudbooster.denied_access'));
			}

			if(Schema::hasColumn($this->table,'deleted_at')) {
				DB::table($this->table)->whereIn('id',$id_selected)->update(['deleted_at'=>date('Y-m-d H:i:s')]);
			}else{
				DB::table($this->table)->whereIn('id',$id_selected)->delete();	
			} 
			CRUDBooster::insertLog(trans("crudbooster.log_delete",['name'=>implode(',',$id_selected),'module'=>CRUDBooster::getCurrentModule()->name]));			
		}

		$this->actionButtonSelected($id_selected,$button_name);
	}

	public function getDeleteImage(Request $request) {		
		$id     = $request->get('id');
		$column = $request->get('column');

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

		DB::table($this->table)->where($this->primary_key,$id)->update([$column=>NULL]);
					
		CRUDBooster::insertLog(trans("crudbooster.log_delete_image",['name'=>$row->{$this->title_field},'module'=>CRUDBooster::getCurrentModule()->name]));

		CRUDBooster::redirect($request->server('HTTP_REFERER'),trans('crudbooster.alert_delete_data_success'));
	}

	public function postUploadSummernote(Request $request) {
		$name = 'userfile';
		if ($request->hasFile($name))
		{			
			$file = $request->file($name);					
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

	public function postAddSaveSession(Request $request) {
		$columns = json_decode($request->get('columns'));
		$fields  = json_decode($request->get('fields'));
		$foreign_key = $request->get('foreign_key');
		$table 	 = $request->get('table');
		$data    = array();	
		
		if(Session::has('session_child_data_'.$table)) {
			$session_child_data = Session::get('session_child_data_'.$table);
		}else{
			$session_child_data = [];
		}							

		$data_item = array();
		foreach($request->all() as $key=>$val) {
			if($key=='columns' || $key=='table' || $key=='fields' || $key=='foreign_key' || $key=='child_parent_id') continue;
			$data_item[$key] = $val;
		}

		$images_ext = array('jpg','jpeg','png','gif','bmp');

		foreach($columns as $col) {
			if($request->hasFile($col->name)) {
				$file = $request->file($col->name);					
				$ext  = strtolower($file->getClientOriginalExtension());

				//Create Directory Monthly 
				Storage::makeDirectory(date('Y-m'));

				//Move file to storage
				$filename = md5(str_random(5)).'.'.$ext;
				if($file->move(storage_path('app'.DIRECTORY_SEPARATOR.date('Y-m')),$filename)) {	
					if(in_array($ext, $images_ext)) {
						$data[] = "<a class='fancybox' href='".asset('uploads/'.date('Y-m').'/'.$filename)."'><img src='".asset('uploads/'.date('Y-m').'/'.$filename)."' width='50px' height='50px'/></a>";
					}else{
						$data[] = "<a target='_blank' href='".asset('uploads/'.date('Y-m').'/'.$filename)."?download=1'>Download</a>";
					}				
					
					$data_item[$col->name] = 'uploads/'.date('Y-m').'/'.$filename;					
				}
			}else{

				if(substr($request->get($col->name), 0, 8) == 'uploads/') {
					$ext = pathinfo($request->get($col->name), PATHINFO_EXTENSION);
					if(in_array($ext, $images_ext)) {
						$data[] = "<a class='fancybox' href='".asset($request->get($col->name))."'><img src='".asset($request->get($col->name))."' width='50px' height='50px'/></a>";
					}else{
						$data[] = "<a target='_blank' href='".asset($request->get($col->name))."?download=1'>Download</a>";
					}
				}else{
					$data[] = $request->get($col->name);	
				}								
			}			
		}


		if($request->get('child_parent_id')) {
			
			$data_item[$foreign_key] = $request->get('child_parent_id');

			if($request->get('id')!='') {				
				DB::table($table)->where('id',$request->get('id'))->update($data_item);
			}else{
				CRUDBooster::insert($table,$data_item);
			}

			return response()->json(['status'=>true]);

		}else{
			if($request->get('id') != '') {
				$id = (int) $request->get('id');
			}else{
				$id = (int) count($session_child_data);
			}	


			//Action
			$data_item = json_encode($data_item);		
			$action = "<a href='javascript:void(0)' data-id='$id' data-table='$table' data-item='$data_item' class='btn btn-success btn-xs btn-edit-child'><i class='fa fa-pencil'></i></a> <a href='javascript:void(0)' data-id='$id' data-table='$table' class='btn btn-warning btn-xs btn-delete-child'><i class='fa fa-trash'></i></a>";
			$action .= "<input type='hidden' name='child_".$table."[".$id."]' value='$data_item'/>";
			$data[] = $action;

			$session_child_data[$id] = $data;
				
			Session::put('session_child_data_'.$table,$session_child_data);		

			return response()->json(['status'=>true]);
		}					
	}

	public function getSessionChildData(Request $request,$table) {

		$fk = $request->get('fk');
		$fk_value = $request->get('fk_value');
		$images_ext = array('jpg','jpeg','png','gif','bmp');

		if($fk && $fk_value) {
			$form = NULL;
			foreach($this->data_inputan as $d) {
				if($d['table'] && $d['table'] == $table) {
					$form = $d;
					break;
				}
			}

			$columns = $form['columns'];			

			$query = DB::table($table)->where($fk,$fk_value)->orderby('id','desc')->get();
			$result = array();
			
			foreach($query as $row) {	

				$ro = array();

				foreach($row as $key=>$val) {				
					foreach($columns as $c) {
						if($key == $c['name']) {

							if(substr($val, 0, 8) == 'uploads/') {
								$ext = pathinfo($val, PATHINFO_EXTENSION);
								if(in_array($ext, $images_ext)) {
									$ro[] = "<a class='fancybox' href='".asset($val)."'><img src='".asset($val)."' width='50px' height='50px'/></a>";
								}else{
									$ro[] = "<a target='_blank' href='".asset($val)."?download=1'>Download</a>";
								}
							}else{
								$ro[] = $val;	
							}							
						}
					}
				}
				

				$id = $row->id;		
				$data_item = json_encode($row);		

				$action = "<a href='javascript:void(0)' data-id='$id' data-table='$table' data-item='$data_item' class='btn btn-success btn-xs btn-edit-child'><i class='fa fa-pencil'></i></a> <a href='javascript:void(0)' data-id='$id' data-table='$table' class='btn btn-warning btn-xs btn-delete-child'><i class='fa fa-trash'></i></a>";
				$action .= "<input type='hidden' name='child_".$table."[".$id."]' value='$data_item'/>";
				$ro[] = $action;

				$result[] = $ro;
			}

			return response()->json($result);

		}else{
			return response()->json(Session::get('session_child_data_'.$table));
		}			
	}

	public function postDeleteSessionChildData(Request $request) {
		$id = $request->get('id');
		$table = $request->get('table');
		$session_child_data = Session::get('session_child_data_'.$table);
		unset($session_child_data[$id]);
		Session::put('session_child_data_'.$table,$session_child_data);

		try{
			if(Schema::hasColumn($table,'deleted_at')) {
				DB::table($table)->where('id',$id)->update(['deleted_at'=>date('Y-m-d H:i:s')]);
			}else{
				DB::table($table)->where('id',$id)->delete();	
			}
			
		}catch(\Exception $e) {

		}

		return response()->json(['status'=>true]);
	}

	public function actionButtonSelected($id_selected,$button_name) {

    }

	public function hook_query_index(&$query) {

	}

	public function hook_row_index(&$columns) {

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
