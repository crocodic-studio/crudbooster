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

class CBController extends Controller {
 
	
	var $data_inputan;
	var $dashboard;
	var $setting;
	var $columns_table;	
	var $module_name;
	var $table;
	var $primary_key		= 'id';
	var $title_field;	
	var $arr                = array();
	var $col                = array();
	var $form               = array();
	var $form_tab           = array();
	var $form_sub           = array();
	var $form_add           = array();
	var $data               = array();
	var $addaction          = array();
	var $index_orderby      = array();
	var $password_candidate = NULL;
	var $date_candidate     = NULL;
	var $limit              = 20;
	var $index_return       = FALSE;
	var $index_table_only   = FALSE;
	var $table_name         = NULL;	
	var $parent_id			= 0;
	var $parent_field		= NULL;
	var $referal			= NULL;
	var $controller_name 	= NULL;
	var $alert				= array();
	var $index_button		= array();
	var $button_show_data   = TRUE;
	var $button_reload_data = TRUE;
	var $button_new_data    = TRUE;
	var $button_delete_data = TRUE;
	var $button_sort_filter = TRUE;
	var $button_filter_data = TRUE;
	var $button_export_data = TRUE;			
	var $button_cancel		= TRUE;
	var $button_addmore		= TRUE;
	var $button_save		= TRUE;
	var $button_table_action = TRUE;
	var $index_statistic	= array(); 
	var $index_additional_view = array();
	var $load_js 			= array();
	var $script_js 			= NULL;
	var $sub_module			= array();	
	var $index_array 		= FALSE;
	var $index_only_id 		= NULL;
	var $button_import_data = TRUE;
	var $show_addaction 	= TRUE;

	public function constructor() {			

		if(!\Schema::hasTable('cms_settings')) return false;

		$this->init_setting();
 		
		$current_url = Request::url();		

		if(Session::get('foreign_key')) {
			foreach($this->form as &$f) {
				foreach(Session::get('foreign_key') as $k=>$v) {
					if($f['name']==$k) {
						$f['label'] = $k;
						$f['name'] = $k;
						$f['type'] = 'hide';
						unset($f['datatable']);
						unset($f['dataenum']);
						$f['value'] = $v;
					}
				}
			}
		}


		$this->columns_table     = $this->col; 		
		$this->data_inputan      = $this->form;
		$this->data['forms']     = $this->data_inputan; 
		$this->data['form_add']  = $this->form_add;	
		if($this->show_addaction) {
			$this->data['addaction'] = $this->addaction;	
		}
 
		$this->data['current_controller'] = stripslashes(strtok(str_replace("\crocodicstudio\crudbooster\controllers","",Route::currentRouteAction()),"@"));		
	
		$tablename = ucfirst($this->table);		
		
		$privileges = DB::table("cms_privileges_roles")
		            ->join("cms_moduls","cms_moduls.id","=","cms_privileges_roles.id_cms_moduls")
		            ->where("cms_privileges_roles.id_cms_privileges",get_my_id_privilege());		            
		if($this->index_array) {
			$privileges = $privileges->where('cms_moduls.path',get_module_path())->first();
		}else{
			if(Request::segment(3) == 'sub-module') {
				$privileges = $privileges->where('cms_moduls.path',Request::segment(5))->first();
			}else{
				$privileges = $privileges->where('cms_moduls.path',get_module_path())->first();
			}			
		}

		$this->data['mainpath'] = $this->dashboard = url(config('crudbooster.ADMIN_PATH').'/'.get_module_path());
		Session::put('current_mainpath',$this->data['mainpath']);

		  if(Request::segment(3) == 'sub-module') {
	        $module = DB::table('cms_moduls')->where('path',Request::segment(5))->first();
	        $this->data['data_sub_module'] = $module;
	        $mainpath = Route($module->controller."GetIndex");
	      }else{
	        $mainpath = mainpath();
	      }

		if(get_method() == 'getDetail') {
			$this->button_addmore = false;
			$this->button_save    = false;			
		}

		if(Request::segment(3) == 'sub-module') {
			if(Request::segment(6) == 'detail') {
				$this->button_addmore = FALSE;
				$this->button_save = FALSE;
			}
		}
		
		$this->data['priv']               = $privileges;
		$this->data['dashboard']          = $this->dashboard;		
		$this->data['table']              = $this->table;
		$this->data['module_name']        = @$privileges->name;
		$this->data['title_field']        = $this->title_field;
		$this->data['appname']            = $this->setting->appname;
		$this->data['setting']            = $this->setting;	
		$this->data['table_name']         = $this->table_name;	
		$this->data['alerts']             = $this->alert;
		$this->data['index_button']       = $this->index_button;
		$this->data['button_show_data']   = $this->button_show_data;
		$this->data['button_reload_data'] = $this->button_reload_data;
		$this->data['button_new_data']    = $this->button_new_data;
		$this->data['button_delete_data'] = $this->button_delete_data;
		$this->data['button_sort_filter'] = $this->button_sort_filter;		
		$this->data['button_export_data'] = $this->button_export_data;
		$this->data['button_addmore'] 	  = $this->button_addmore;
		$this->data['button_cancel'] 	  = $this->button_cancel;
		$this->data['button_save'] 		  = $this->button_save;
		$this->data['button_table_action'] = $this->button_table_action;
		$this->data['index_statistic']	  = $this->index_statistic;
		$this->data['index_additional_view'] = $this->index_additional_view;
		$this->data['load_js'] 			  = $this->load_js;
		$this->data['script_js'] 		  = $this->script_js;
		$this->data['sub_module']		  = $this->sub_module;
		$this->data['button_import_data'] = $this->button_import_data;

        view()->share($this->data);
	} 

	public function columns_table() {
		return $this->columns_table;
	}

	public function sub_module() {
		return $this->sub_module;
	}	

	public function mainpath() {
		return $this->data['mainpath'];
	}

	public function title_field() {
		return $this->title_field;
	}
	public function primary_key() {
		return $this->primary_key;
	}

	public function form_by_name($name) {
		foreach($this->form as $f) {
			if($f['name'] == $name) {
				return $f;
			}
		}
	}

	public function getIndex()
	{
		// DB::connection()->enableQueryLog();		

		Session::forget('current_row_id');

		$is_sub_module = (Request::segment(3) == 'sub-module')?TRUE:FALSE;

		if($this->data['priv']->limit_data) {
			$this->limit = $this->data['priv']->limit_data;
		}

		if($this->data['priv']->is_visible==0) {

			insert_log("Trying view data at ".$this->data['module_name']);
			return redirect('admin')->with(['message'=>'You can not access that area !','message_type'=>'warning']);
		}

		$data['table_name'] 	  = $this->table_name;
		$data['page_title']       = $this->data['module_name'];
		$data['page_description'] = "Data List";
		$data['page_menu'] 		  = Route::getCurrentRoute()->getActionName();
		$data['date_candidate']   = $this->date_candidate;
		$data['limit'] = $limit   = (Request::get('limit'))?Request::get('limit'):$this->limit;

		if(Cache::has('columns_'.$this->table)) {
			$columns = Cache::get('columns_'.$this->table);	
		}else{
			$columns = Cache::rememberForever('columns_'.$this->table, function() {
			    return \Schema::getColumnListing($this->table);
			});
		}		
		
		$columns_table            = $this->columns_table;		
		$result                   = DB::table($this->table)->select(DB::raw($this->table.".".$this->primary_key));
		
		$this->hook_before_index($result);
		$this->hook_query_index($result);

		if(Session::get('foreign_key')) {
			foreach(Session::get('foreign_key') as $k=>$v) {
				if(in_array($k, $columns)){
					$result->where($this->table.'.'.$k,$v);
				}
			}
		}

		//From Dashboard Chart Query 
		if(Request::get('dq')) {
			$dq = decrypt(Request::get('dq'));
			if($dq) {
				$dq = unserialize($dq);
				if($dq['sql_where']) {
					$result->whereraw($dq['sql_where']);
				}
				if($dq['limit']) {
					$limit = $dq['limit']; 
				}
				if(isset($dq['noaction']) && $dq['noaction']==1) {
					$this->button_table_action = false;
				}
			}			
		}

		if($this->data['priv']->is_softdelete==1) {
			$result->where($this->table.'.deleted_at',NULL);
		}

		if(@$this->data['priv']->sql_where) {
			$sqlwhere = $this->data['priv']->sql_where;
			$sqlwhere = str_ireplace("where ","",$sqlwhere);
			$sqlwhere = str_replace(array("[admin_id]","[admin_id_companies]"),array(get_my_id(),get_my_id_company()),$sqlwhere);
			$result->whereraw($sqlwhere);
		}
		
		$alias = array();
		$join_alias_count = 0;
		$join_table_temp = array();
		foreach($columns_table as $index => $coltab) {
			$join = @$coltab['join'];
			$table = $this->table;
			$field = $coltab['name'];			

			if(!$field) die('Please make sure there is key `name` in each row of col');

			//Jika ada subquery
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
				$result->addselect($join_alias.'.'.$join_column.' as '.$join_column.'_'.$join_alias);
				
				$alias[] = $join_alias;
				$columns_table[$index]['type_data']	 = get_field_type($join_table,$join_column);
				$columns_table[$index]['field']      = $join_column.'_'.$join_alias;
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
					$columns_table[$index]['type_data']	 = get_field_type($join_table1,$join_column1);
					$columns_table[$index]['field']      = $join_column1.'_'.$join_alias1;
					$columns_table[$index]['field_with'] = $join_alias1.'.'.$join_column1;
					$columns_table[$index]['field_raw']  = $join_column1;
				}
																				 	
			}else{ 

				$result->addselect($table.'.'.$field);
				$columns_table[$index]['type_data']	 = get_field_type($table,$field);
				$columns_table[$index]['field']      = $field;
				$columns_table[$index]['field_raw']  = $field;
				$columns_table[$index]['field_with'] = $table.'.'.$field;
			}					
		}

 		if($this->parent_field && $this->parent_id) {
 			$result->where($table.'.'.$this->parent_field,$this->parent_id);
 		}

		if(Request::get('q') && !$this->index_array) {
			
			$result->where(function($w) use ($columns_table) {
				foreach($columns_table as $col) {		
						if(!$col['field_with']) continue;		
						if($col['is_subquery']) continue;			
						$w->orwhere($col['field_with'],"like","%".Request::get("q")."%");				
				}
			});		
		}			

		if(Request::get('where') && !$this->index_array) {			
			foreach(Request::get('where') as $k=>$v) {
				$result->where($table.'.'.$k,$v); 
			}			
		}
		
		$filter_is_orderby = false;
		if(Request::get('filter_column') && !$this->index_array) {

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

		if($this->index_only_id) {
			$result->where($this->table.'.'.$this->primary_key,$this->index_only_id);
		}


		if($filter_is_orderby == true) {
			$data['result']  = $result->paginate($limit);
		}else{
			if(@$this->data['priv']->sql_orderby) {
				$data['result'] = $result->orderbyraw($this->data['priv']->sql_orderby)->paginate($limit);
			}else{
				if($this->index_orderby) {
					foreach($this->index_orderby as $k=>$v) {
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
		}
								
		$data['columns'] = $columns_table;

		if($this->index_return==true) {			
			return $data;			
		}

		// $queries = DB::getQueryLog();				

		if(Request::get('format')=='total') {
			return calc_eloquent_found($result);
		}


		//LISTING INDEX HTML
		$priv          = $this->data['priv'];
		$addaction     = $this->data['addaction'];
		$mainpath      = mainpath();		
		$orig_mainpath = $this->data['mainpath'];
		$title_field   = $this->title_field;
		$html_contents = array();
		foreach($data['result'] as $row) {
			$html_content = array();

			$html_content[] = "<input type='checkbox' class='checkbox' name='checkbox' value='$row->id'/>";

			foreach($columns_table as $col) {     
		          if($col['visible']===FALSE) continue;

		          $value = @$row->{$col['field']}; 

		          $title = @$row->{$this->title_field};

		          if(isset($col['image'])) {
			            if($value=='') {
			              $value = "http://placehold.it/50x50&text=NO+IMAGE";
			            }
			            $pic = (strpos($value,'http://')!==FALSE)?$value:asset($value);
			            $pic_small = $pic;
			            $value = "<a class='fancybox' rel='group_{{$table}}' title='$col[label]: $title' href='".$pic."?w=350'><img width='40px' height='40px' src='".$pic_small."?w=40'/></a>";
		          }else if(@$col['download']) {
			            $url = (strpos($value,'http://')!==FALSE)?$value:asset($value);
			            if($value) {
			            	$value = "<a class='btn btn-sm btn-primary' href='$url' target='_blank' title='Download File'>Download</a>";
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
			              $col['callback_php'] = str_replace('%field%',$value,$col['callback_php']);  
			              foreach($row as $k=>$v) {
			              		$col['callback_php'] = str_replace("[".$k."]",$v,$col['callback_php']);			              		
			              }
			              @eval("\$value = ".$col['callback_php'].";");			              
			            }

			            if(isset($col['callback_html'])) {
			              $callback = str_replace('%field%',$value,$col['callback_html']);
			              foreach($row as $k=>$v) {
			              		$col['callback_html'] = str_replace("[".$k."]",$v,$col['callback_html']);			              		
			              }
			              $value = $callback;                                  
			            }		                                            		           
		          }                

		          $html_content[] = $value;
	      } //end foreach columns_table


	      if($this->button_table_action):
		      if($priv->is_edit!=0 || $priv->is_delete!=0 || $priv->is_read!=0):
	         	$td = "<div class='btn-group btn-group-action'>
	                  <button type='button' class='btn btn-xs btn-primary btn-action'>Action</button>
	                  <button type='button' class='btn btn-xs btn-primary dropdown-toggle' title='Click to see menu' data-toggle='dropdown'>
	                    <span class='caret'></span>
	                    <span class='sr-only'>Toggle Dropdown</span>
	                  </button>
	                  <ul class='dropdown-menu dropdown-menu-action' role='menu'>";
	            if(!$this->index_array) {
		            if(count(@$addaction)):				
		                foreach($addaction as $fb):
		                	$ajax  = (isset($fb["ajax"]))?"class='ajax-button'":"";                     			                	

		                	$url = $fb['route'];
		                	foreach($row as $k=>$v) {
				            	$url = str_replace("[".$k."]",$v,$url);			              		
				            }

		                	$icon  = $fb['icon'];
		                	$label = ($icon)?"<i class='$icon'></i> ".$fb['label']:$fb['label'];               	
		                	$td    .= "<li><a title='".$fb["label"]."' href='".$url."' $ajax >$label</a></li>";
		                endforeach;
		                $td .= "<li class='divider'></li>";
		            endif;
	        	}

	            if($this->sub_module && $this->show_addaction) {
	            	$sub_module_i = 0;
	            	foreach($this->sub_module as $sm) {     
	            		if(Request::segment(5) == $sm['path']) continue;       		
	            		$icon = ($sm['icon'])?:"fa fa-bars";
	            		$url = mainpath("sub-module/$row->id/$sm[path]");
	            		$td .= "<li><a title='$sm[label]' href='$url'><i class='$icon'></i> $sm[label]</a></li>";
	            		$sub_module_i++;
	            	}
	            	if($sub_module_i) {
	            		$td .= "<li class='divider'></li>";
	            	}            	
	            }

	         	
	         	if($priv->is_read) {

	         		if($this->index_array) {
	         			$url = url("$orig_mainpath/detail/$row->id");
	         		}else{
	         			$url = url("$mainpath/detail/$row->id");
	         		}

	            	$td .= "<li><a title='Detail Data' href='$url'><i class='fa fa-search'></i> Detail Data</a></li>";
	            }
	      		
	      		if($priv->is_edit):      	

				    if($this->index_array) {
	         			$url = url("$orig_mainpath/edit/$row->id");
	         		}else{
	         			$url = url("$mainpath/edit/$row->id");
	         		}
	      			
	      			$td .= "<li><a title='Edit Data' href='$url'><i class='fa fa-pencil'></i> Edit Data</a></li>";
	            endif;

	            if($priv->is_delete):
	            	if($this->index_array) {    			
	         			$url = url("$orig_mainpath/delete/$row->id");
				    }else{
				    	$url = url("$mainpath/delete/$row->id");
				    }			    
	            	$td .= "<li><a title='Delete Data' href='javascript:;' onclick='swal({   title: \"Are you sure?\",   text: \"You will not be able to recover this record data!\",   type: \"warning\",   showCancelButton: true,   confirmButtonColor: \"#DD6B55\",   confirmButtonText: \"Yes, delete it!\",   closeOnConfirm: false }, function(){  location.href=\"$url\" });'><i class='fa fa-trash'></i> Delete</a></li>";
	            endif;  

	            

	            $td .= "
	                  </ul>
	                </div>";

	          	$html_content[] = $td;
	          endif;
          endif;//button_table_action

	      $html_contents[] = $html_content;
		} //end foreach data[result]
 		
 		$html_contents = ['html'=>$html_contents,'data'=>$data['result']];
		$this->hook_html_index($html_contents['html'],$html_contents['data']);
		$this->hook_row_index($html_contents['html'],$html_contents['data']);

		if($this->index_array == TRUE) {
			return $html_contents['html'];
		}

		$data['html_contents'] = $html_contents['html'];

		return view("crudbooster::default.index",$data);
		
	}

	public function getSubModule($parent_id,$module_path,$action='index',$id=NULL) {
		$parent_module_path = Request::segment(2);
		$parent_module = DB::table('cms_moduls')->where('path',$parent_module_path)->first();
		$sub_module = DB::table('cms_moduls')->where('path',$module_path)->first();
		if(!$parent_module || !$sub_module) return redirect()->back()->with(['message'=>'Sorry the module is does not exists !','message_type'=>'warning']);


		$sub_module_config = $this->sub_module();
		$parent_field = '';
		foreach($sub_module_config as $s) {
			if($s['path'] == $module_path) {
				$parent_field = $s['foreign_key'];
				break;
			}
		}

		if(!$parent_field) die('You did not set the foreign key at $this->sub_module ');

		$sub_module_class = __NAMESPACE__ . '\\' . $sub_module->controller;
		if(!class_exists($sub_module_class)) {
			$sub_module_class = '\App\Http\Controllers\\'.$sub_module->controller;
		}

		$sub_module_class = new $sub_module_class;		
		$sub_module_class->parent_id = $parent_id;	
		$sub_module_class->parent_field = $parent_field;	
		$method_need_id = array('getDetail','getEdit','postEditSave','getDelete');

		$controller_class = new \ReflectionClass($sub_module_class);                          
        $controller_methods = $controller_class->getMethods(\ReflectionMethod::IS_PUBLIC);
        foreach($controller_methods as $method) {
        	if ($method->class != 'Illuminate\Routing\Controller') {                                             
                // return $sub_module_class->{$method->name};
                $method_name = $method->name;
                if(substr($method_name, 0, 3) == 'get') {
                    $slug = substr($method_name, 3);
                    $slug = array_filter(preg_split('/(?=[A-Z])/',$slug));   
                    $slug = strtolower(implode('-',$slug));
                    
                    if($action == $slug) {
                    	if(in_array($method_name, $method_need_id)) {
                    		return $sub_module_class->$method_name($id);                    	
                    	}else{
                    		return $sub_module_class->$method_name();                    	
                    	}                     	
                    }

                }elseif(substr($method_name, 0, 4) == 'post') {
                    $slug = substr($method_name, 4);
                    $slug = array_filter(preg_split('/(?=[A-Z])/',$slug));   
                    $slug = strtolower(implode('-',$slug));                                

                    if($action == $slug) {
                    	if(in_array($method_name, $method_need_id)) {
                    		return $sub_module_class->$method_name($id);                    	
                    	}else{
                    		return $sub_module_class->$method_name();                    	
                    	}                     	
                    }
                }
               
            }
        }	        
	}	

	public function getExportData() {
		return redirect(mainpath());
	}

	public function postExportData() {
		$this->limit        = Request::input('limit');
		$this->index_return = true;
		$response           = $this->getIndex();
		$filetype 			= Request::input('fileformat');
		$filename 			= Request::input('filename');
		$papersize			= Request::input('page_size');
		$paperorientation	= Request::input('page_orientation');	
	
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
					->setCompany($this->setting->appname);					
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
					->setCompany($this->setting->appname);					
				    $excel->sheet($filename, function($sheet) use ($response) {
				    	$sheet->setOrientation($paperorientation);
				        $sheet->loadview('crudbooster::export',$response);
				    });
				})->export('csv');
			break;
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

			if(\Schema::hasColumn($table1,'deleted_at')) {
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

	public function validation() {

		$request_all = Request::all();
		$array_input = array();
		$id          = get_row_id();
		$id          = intval($id);
		foreach($this->data_inputan as $di) {
			$ai = array();		
			$name = $di['name'];

			if( !isset($request_all[$name]) ) continue; 

			if($di['type'] != 'upload_standard') {
				if(@$di['required']) {
					$ai[] = 'required';
				}	
			}

			if($di['type'] == 'upload_standard') {
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

	public function input_assignment($id=null) {		
		foreach($this->data_inputan as $ro) {
			$name = $ro['name'];

			if(!$name) continue;

			$inputdata = Request::get($name);

			if($ro['type']=='money') {
				$inputdata = preg_replace('/[^\d-]+/', '', $inputdata); 
			}

			if(isset($ro['onlyfor'])) {
				if(is_array($ro['onlyfor'])) {
					if(in_array(Session::get('admin_privileges_name'), $ro['onlyfor'])) {
						$this->arr[$name] = $inputdata;
					}else{
						continue;
					}
				}else{
					if(Session::get('admin_privileges_name') == $ro['onlyfor']) {
						$this->arr[$name] = $inputdata;
					}else{
						continue;
					}
				}
			}

			if($name && isset($inputdata)) {
				$this->arr[$name] = $inputdata;
			}

			if($name == 'slug') {
				$this->arr[$name] = slug(Request::get($this->title_field),$this->table,$this->title_field,$id);
			}

			if(in_array($name, $this->password_candidate)) {
				if(!empty($this->arr[$name])) {
					$this->arr[$name] = Hash::make($this->arr[$name]);
				}else{
					unset($this->arr[$name]);
				}
			}

			if($ro['type']=='checkbox') {
				$this->arr[$name] = implode(";",$inputdata);
			}

			if(@$ro['type']=='upload_standard') {				
				unset($this->arr[$name]);
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
			}

			if(@$ro['type']=='upload') {
				$url = str_replace(asset('/'),'',$this->arr[$name]);
				$this->arr[$name] = $url;
			}			
		}

	}

	public function getAdd(){
		$data['page_title']      = $this->data['module_name'].": Add New Data";
		$data['page_menu']       = Route::getCurrentRoute()->getActionName();	
		$data['table_name']      = $this->table_name;
		$data['referal']         = $this->referal;
		$data['controller_name'] = $this->controller_name;
		$data['parent_field']    = $this->parent_field;
		$data['parent_id']       = $this->parent_id;

		return view('crudbooster::default.form',$data);
		
	}
	
	public function postAddSave() {

		if($this->data['priv']->is_create==0) {

			insert_log("Trying add data ".Request::get($this->title_field)." at ".$this->data['module_name']);
			return redirect('admin')->with(['message'=>'You can not access that area !','message_type'=>'warning']);
		}

		$this->validation();	
		$this->input_assignment();

		if (\Schema::hasColumn($this->table, 'created_at')) 
		{
		    $this->arr['created_at'] = date('Y-m-d H:i:s');
		}	

		$this->hook_before_add($this->arr);		

		DB::table($this->table)->insert($this->arr);
		$lastid        = DB::getPdo()->lastInsertId();
		$ref_parameter = Request::input('ref_parameter');

		$this->hook_after_add($lastid);

		//insert log
		insert_log("Add new data ".$this->arr[$this->title_field]." at ".$this->data['module_name']);

		if(Request::get('return_url')) {
			return redirect(Request::get('return_url'))->with(['message'=>'The data has been added !','message_type'=>'success']);
		}

		if(Request::get('referal')) {
			return redirect(Request::get('referal').'?'.$ref_parameter)->with(['message'=>'The data has been added !','message_type'=>'success']);
		}else{
			if(Request::get('ref_mainpath')) {
				if(Request::get('submit') == 'Save & Add More') {
					return redirect(Request::get('ref_mainpath').'/add')->with(['message'=>"The data has been added !",'message_type'=>'success']);	
				}else{
					return redirect(Request::get('ref_mainpath'))->with(['message'=>"The data has been added !",'message_type'=>'success']);	
				}				
			}else{
				return redirect(mainpath())->with(['message'=>"The data has been added !",'message_type'=>'success']);
			}				
		}
		
	}
	
	public function getEdit($id){				
		$data['row']             = DB::table($this->table)->where($this->primary_key,$id)->first();		
		$data['page_menu']       = Route::getCurrentRoute()->getActionName();
		$title_field             = $this->title_field;
		$data['page_title']      = $this->data['module_name'].": Edit ".$data['row']->{$title_field};
		$data['table_name']      = $this->table_name;
		$data['referal']         = $this->referal;
		$data['controller_name'] = $this->controller_name;
		$data['parent_field']    = $this->parent_field;
		$data['parent_id']       = $this->parent_id;

		Session::put('current_row_id',$id);

		return view('crudbooster::default.form',$data);
	}

	public function getImportData() {
		$path = $this->data['priv']->path;
		$module = DB::table('cms_moduls')->where('path',$path)->first();
		$data['module'] 		 = $module;
		$data['page_menu']       = Route::getCurrentRoute()->getActionName();
		$title_field             = $this->title_field;
		$data['page_title']      = 'Import Data '.$module->name;
		$data['table']      	 = $this->table;
		$data['referal']         = $this->referal;
		$data['controller_name'] = $this->controller_name;


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
		$path = $this->data['priv']->path;
		$module = DB::table('cms_moduls')->where('path',$path)->first();
		$data['module'] 		 = $module;
		$data['page_menu']       = Route::getCurrentRoute()->getActionName();
		$title_field             = $this->title_field;
		$data['page_title']      = 'Import Data '.$module->name;
		$data['table']      	 = $this->table;
		$data['referal']         = $this->referal;
		$data['controller_name'] = $this->controller_name;		

		Session::put('select_column',Request::get('select_column'));

		return view('crudbooster::import',$data);
	}

	public function postDoImportChunk() {
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
		if(\Schema::hasColumn($this->table,'created_at')) {
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
					$relation_moduls = DB::table('cms_moduls')->where('table_name',$relation_table)->first();

					$relation_class = __NAMESPACE__ . '\\' . $relation_moduls->controller;
					if(!class_exists($relation_class)) {
						$relation_class = '\App\Http\Controllers\\'.$relation_moduls->controller;
					}
					$relation_class = new $relation_class;

					$title_field = $relation_class->title_field();

					$relation_insert_data = array();
					$relation_insert_data[$title_field] = $value->$s;

					if(\Schema::hasColumn($relation_table,'created_at')) {
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

	public function postDoUploadImportData() {
	
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
			$url = mainpath('import-data').'?file='.base64_encode($url_filename);
			// $url = action($this->data['priv']->controller.'@getImportData').'?file='.base64_encode($url_filename);			
			return redirect($url);		  
		}else{
			return redirect()->back();
		}
	}

	public function getDetail($id)	{				
		$data['row']             = DB::table($this->table)->where($this->primary_key,$id)->first();		
		$data['page_menu']       = Route::getCurrentRoute()->getActionName();
		$title_field             = $this->title_field;
		$data['page_title']      = $this->data['module_name'].": Detail ".$data['row']->{$title_field};
		$data['table_name']      = $this->table_name;
		$data['referal']         = $this->referal;
		$data['controller_name'] = $this->controller_name;
		$data['parent_field']    = $this->parent_field;
		$data['parent_id']       = $this->parent_id;

		Session::put('current_row_id',$id);

		return view('crudbooster::default.form',$data);
	}
	 
	public function postEditSave($id) {

		$row = DB::table($this->table)->where($this->primary_key,$id)->first();	

		if($this->data['priv']->is_edit==0) {
			
			insert_log("Trying add data ".$row->{$this->title_field}." at ".$this->data['module_name']);
			return redirect('admin')->with(['message'=>'You can not access that area !','message_type'=>'warning']);
		}
		
		$this->validation();
		$this->input_assignment($id);

		if (\Schema::hasColumn($this->table, 'updated_at')) 
		{
		    $this->arr['updated_at'] = date('Y-m-d H:i:s');
		}

		$this->hook_before_edit($this->arr,$id);
		
		DB::table($this->table)->where($this->primary_key,$id)->update($this->arr);

		$this->hook_after_edit($id);

		//insert log

		insert_log("Update data ".$this->arr[$this->title_field]." at ".$this->data['module_name']);

		if(Request::get('return_url')) {
			return redirect(Request::get('return_url'))->with(['message'=>'The data has been updated !','message_type'=>'success']);
		}

		if(Request::get('referal')) {
			return redirect(Request::get('referal'))->with(['message'=>'The data has been updated !','message_type'=>'success']);
		}else{
			if(Request::get('ref_mainpath')) {
				if(Request::get('submit') == 'Save & Add More') {
					return redirect(Request::get('ref_mainpath').'/add')->with(['message'=>"The data has been updated, you can add more !",'message_type'=>'success']);
				}else{
					return redirect(Request::get('ref_mainpath'))->with(['message'=>"The data has been updated !",'message_type'=>'success']);
				}
			}else{
				return redirect(mainpath())->with(['message'=>"The data has been updated !",'message_type'=>'success']);
			}			
		}
	}
	
	public function getDelete($id) {

		$row = DB::table($this->table)->where($this->primary_key,$id)->first();	

		if($this->data['priv']->is_delete==0) {

			insert_log("Trying delete data ".$row->{$this->title_field}." at ".$this->data['module_name']);
			return redirect('admin')->with(['message'=>'You can not access that area !','message_type'=>'warning']);
		}

			
		//insert log
		insert_log("Delete data ".$row->{$this->title_field}." at ".$this->data['module_name']);

		$this->hook_before_delete($id);

		if($this->data['priv']->is_softdelete==1) {
			DB::table($this->table)->where($this->primary_key,$id)->update(['deleted_at'=>date('Y-m-d H:i:s')]);
		}else{
			DB::table($this->table)->where($this->primary_key,$id)->delete();
		}
		
		$this->hook_after_delete($id);

		return redirect()->back()->with(['message'=>"Data has been deleted !",'message_type'=>"success"]);
	}

	public function postDeleteSelected() {

		if($this->data['priv']->is_delete==0) {

			insert_log("Trying delete data at ".$this->data['module_name']);
			return redirect('admin')->with(['message'=>'You can not access that area !','message_type'=>'warning']);
		}

		$id = Request::input('id');
		if($id) {

			foreach($id as $d) {
				//insert log
				$row               = DB::table($this->table)->where($this->primary_key,$d)->first();

				insert_log("Delete data ".$row->{$this->title_field}." at ".$this->data['module_name']);
			}

			
			if($this->data['priv']->is_softdelete==1) {
				DB::table($this->table)->whereIn($this->primary_key,$id)->update(['deleted_at'=>date('Y-m-d H:i:s')]);
			}else{
				DB::table($this->table)->whereIn($this->primary_key,$id)->delete();
			}

		}
	}

	public function getDeleteImage() {		
		$id     = Request::get('id');
		$column = Request::get('column');

		$row    = DB::table($this->table)->where($this->primary_key,$id)->first();

		if($this->data['priv']->is_create==0) {

			insert_log("Trying delete image ".$row->{$this->title_field}." at ".$this->data['module_name']);
			return redirect('admin')->with(['message'=>'You can not access that area !','message_type'=>'warning']);
		}
			
		$row = DB::table($this->table)->where($this->primary_key,$id)->first();	

		// if(Storage::exists($row->{$column})) Storage::delete($row->{$column});

		$file = str_replace('uploads/','',$row->{$column});
		if(Storage::exists($file)) {
            		Storage::delete($file);
       		}

		DB::table($this->table)->where($this->primary_key,$id)->update(array($column=>NULL));
			
		insert_log("Delete image for ".$row->{$this->title_field}." at ".$this->data['module_name']);

		return redirect()->back()->with(['message'=>"The file has been deleted !",'message_type'=>"success"]);
	}

	public function getDeleteFilemanager() {		
		$id     = Request::get('id');
		$column = Request::get('column');

		$row    = DB::table($this->table)->where($this->primary_key,$id)->first();

		if($this->data['priv']->is_create==0) {			
			insert_log("Trying delete image ".$row->{$this->title_field}." at ".$this->data['module_name']);
			return redirect('admin')->with(['message'=>'You can not access that area !','message_type'=>'warning']);
		}
			
		$row = DB::table($this->table)->where($this->primary_key,$id)->first();	

		if($column && $row->{$column})	@unlink($row->{$column});

		DB::table($this->table)->where($this->primary_key,$id)->update(array($column=>NULL));		

		insert_log("Delete image for ".$row->{$this->title_field}." at ".$this->data['module_name']);
		return redirect()->back()->with(['message'=>"The file has been deleted !",'message_type'=>"success"]);
	}

	public function init_setting() {

		$this->password_candidate = explode(',',config('crudbooster.PASSWORD_FIELDS_CANDIDATE'));
		$this->date_candidate     = explode(',',config('crudbooster.DATE_FIELDS_CANDIDATE'));

		if(\Schema::hasTable('cms_settings')) {
			$setting = DB::table('cms_settings')->get();
			$setting_array = array();
			foreach($setting as $set) {
				$setting_array[$set->name] = $set->content;
			}
			$this->setting = json_decode(json_encode($setting_array));
		}
				
	}

	public function hook_query_index(&$query) {

	}

	public function hook_row_index(&$html,$data) {        

    }

	public function hook_before_index(&$result) {

	}

	public function hook_html_index(&$html,$data) {

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
