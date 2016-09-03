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
use Illuminate\Support\Facades\Excel;

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
	var $is_sub				= FALSE;
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
	var $index_statistic	= array(); // key: label, icon, color, count

	public function constructor() {			

		if(!\Schema::hasTable('cms_settings')) return false;

		$this->init_setting();
 		
		$current_url = Request::url();		

		if(Request::get('is_sub') && Session::get('form_tab')) {
			if(!$this->form_tab) {
				$this->form_tab = Session::get('form_tab');
				$this->form_tab = super_unique($this->form_tab,'route');
			}else{
				$last_form_tab = Session::get('form_tab');						

				if(Request::segment(3)=='edit' || Request::segment(3)=='add') {
					

					foreach($this->form_tab as &$ft) {		
						$w = array();
						foreach(Request::get('where') as $a=>$b) { 
							$w[] = "&where[".$a."]=".$b;
						}						
						$w[] = "&where[".$ft['foreign_key']."]=%id%";												
						$ft['route'] = route($ft['controller'].'GetIndex').'?is_sub='.Request::get('is_sub').implode('',$w);					
						$last_form_tab[] = $ft;
					}
					Session::put('form_tab',$last_form_tab);												
				}

				$last_form_tab = super_unique($last_form_tab,'route');	
				$this->form_tab = $last_form_tab;				
			}
		}else{

			//Add Parent Editor
			if($this->form_tab) {

				foreach($this->form_tab as &$ft) {
					$ft['route'] = route($ft['controller'].'GetIndex').'?is_sub=1&where['.$ft['foreign_key'].']=%id%';
				}

				$first_form_tab   = array();
				$first_form_tab[] = array("label"=>"Edit Data","route"=>url(mainpath("edit/".get_row_id())));
				$this->form_tab   = array_merge($first_form_tab,$this->form_tab);
				if(Request::segment(3)=='edit' || Request::segment(3)=='add') Session::put('form_tab',$this->form_tab);		

			}
		}

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

		//FORM SUB 
		foreach($this->form_sub as &$fs) {
			$className = __NAMESPACE__ . '\\' . $fs['controller'];
			$fs['classname'] = $className;
		}

		$this->columns_table     = $this->col; 		
		$this->data_inputan      = $this->form;
		$this->data['forms']     = $this->data_inputan;
		$this->data['form_tab']  = $this->form_tab; 	
		$this->data['form_sub']  = $this->form_sub; 
		$this->data['form_add']  = $this->form_add;	
		$this->data['addaction'] = $this->addaction;	
 
		$this->data['current_controller'] = stripslashes(strtok(str_replace("\crocodicstudio\crudbooster\controllers","",Route::currentRouteAction()),"@"));		
	
		$tablename = ucfirst($this->table);		

		$this->data['mainpath'] = $this->dashboard = mainpath();		 

		Session::put('current_mainpath',$this->data['mainpath']);

		$privileges = DB::table("cms_privileges_roles")
		            ->join("cms_moduls","cms_moduls.id","=","cms_privileges_roles.id_cms_moduls")
		            ->where("cms_privileges_roles.id_cms_privileges",get_my_id_privilege())
		            ->where("cms_moduls.path",get_module_path())->first();


		if(get_method() == 'getDetail') {
			$this->button_addmore = false;
			$this->button_save    = false;			
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
		$this->data['index_statistic']	  = $this->index_statistic;

        view()->share($this->data);
	} 



	public function getIndex()
	{
		// DB::connection()->enableQueryLog();		

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
		$result                   = DB::table($this->table)->select(DB::raw("SQL_CALC_FOUND_ROWS ".$this->table.".".$this->primary_key));
		
		$this->hook_before_index($result);

		if(Session::get('foreign_key')) {
			foreach(Session::get('foreign_key') as $k=>$v) {
				if(in_array($k, $columns)){
					$result->where($this->table.'.'.$k,$v);
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
		$e = 0;		
		foreach($columns_table as $index => $coltab) {
			$join = @$coltab['join'];
			if($join) {
				$join_exp   = explode(',', $join);
				$join_table = $join_exp[0];
				$join_name  = $join_exp[1];
			}
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

			if($join) {		
				//field = id relasi nya
				$join_alias = $join_table.$index;
				$result->leftjoin($join_table.' as '.$join_alias,$join_alias.'.id','=',$this->table.'.'.$field);
				$result->addselect($join_alias.'.'.$join_name.' as '.$join_name.'_'.$join_alias);
				$result->addselect($this->table.'.'.$field); #field asli tetap di masukkan
				$alias[] = $join_alias;
				$columns_table[$index]['type_data']	 = get_field_type($join_table,$join_name);
				$columns_table[$index]['field']      = $join_name.'_'.$join_alias;
				$columns_table[$index]['field_with'] = $join_alias.'.'.$join_name;
				$columns_table[$index]['field_raw']  = $join_name;
				 	
			}else{
				$result->addselect($this->table.'.'.$field);
				$columns_table[$index]['type_data']	 = get_field_type($this->table,$field);
				$columns_table[$index]['field']      = $field;
				$columns_table[$index]['field_raw']  = $field;
				$columns_table[$index]['field_with'] = $this->table.'.'.$field;
			}			
		}
 		
 		if($this->is_sub==true) {
 			$q = 'q_'.str_slug($this->table_name);
 			if(Request::get($q)) {
 				$result->where(function($w) use ($columns_table,$q) {
					foreach($columns_table as $col) {		
							if(!$col['field_with']) continue;		
							if($col['is_subquery']) continue;			
							$w->orwhere($col['field_with'],"like","%".Request::get($q)."%");				
					}
				});
 			}
 		}

 		if($this->is_sub==true) {
 			$result->where($this->table.'.'.$this->parent_field,$this->parent_id);
 		}

		if(Request::get('q')) {
			
			$result->where(function($w) use ($columns_table) {
				foreach($columns_table as $col) {		
						if(!$col['field_with']) continue;		
						if($col['is_subquery']) continue;			
						$w->orwhere($col['field_with'],"like","%".Request::get("q")."%");				
				}
			});		
		}			

		if(Request::get('where')) {			
			foreach(Request::get('where') as $k=>$v) {
				$result->where($this->table.'.'.$k,$v); 
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
			if(@$this->data['priv']->sql_orderby) {
				$data['result'] = $result->orderbyraw($this->data['priv']->sql_orderby)->paginate($limit);
			}else{
				if($this->index_orderby) {
					foreach($this->index_orderby as $k=>$v) {
						if(strpos($k, '.')!==FALSE) {
							$orderby_table = explode(".",$k)[0];
						}else{
							$orderby_table = $this->table;
						}
						$result->orderby($orderby_table.'.'.$k,$v);
					}
					$data['result'] = $result->paginate($limit);
				}else{
					$data['result'] = $result->orderby($this->primary_key,'desc')->paginate($limit);	
				}
			}
		}
								
		$data['columns'] = $columns_table;

		if($this->index_return==true) {			
			return $data;			
		}

		$queries = DB::getQueryLog();				

		if(Request::get('format')=='total') {
			return DB::select(DB::raw("SELECT FOUND_ROWS() as total"))[0]->total;
		}


		//LISTING INDEX HTML
		$priv          = $this->data['priv'];
		$addaction     = $this->data['addaction'];
		$mainpath      = mainpath();		
		$title_field   = $this->title_field;
		$html_contents = array();
		foreach($data['result'] as $row) {
			$html_content = array();

			if($this->is_sub==false) $html_content[] = "<input type='checkbox' class='checkbox' name='checkbox' value='$row->id'/>";

			foreach($columns_table as $col) {     
	          if($col['visible']===0) continue;

	          $value = @$row->{$col['field']}; 

	          $title = @$row->{$this->title_field};

	          if(@$col['image']) {
	            if($value=='') {
	              $value = "http://placehold.it/50x50&text=NO+IMAGE";
	            }
	            $pic = (strpos($value,'http://')!==FALSE)?$value:asset($value);
	            $pic_small = $pic;
	            $html_content[] = "<a class='fancybox' rel='group_{{$table}}' title='$col[label]: $title' href='".$pic."'><img class='img-circle' width='40px' height='40px' src='".$pic_small."'/></a>";
	          }else if(@$col['download']) {
	            $url = (strpos($value,'http://')!==FALSE)?$value:asset($value);
	            $html_content[] = "<a class='btn btn-sm btn-primary' href='$url' target='_blank' title='Download File'>Download</a>";
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
	              @eval("\$value = ".$col['callback_php'].";");
	            }

	            if(isset($col['callback_html'])) {
	              $callback = str_replace('%field%',$value,$col['callback_html']);
	              $value = $callback;                                  
	            }
	                                            
	            $html_content[] = $value;
	          }                                                         
	      } //end foreach columns_table



	      if($priv->is_edit!=0 || $priv->is_delete!=0 || $priv->is_read!=0):
         	$td = "";
         	
         	if($priv->is_read) {
         		if($this->is_sub==true) {         			
         			$data_modul = ['modul'=>$this->table_name,'action'=>"detail","id"=>$row->id];
         			$current_url = Request::url();
         			$current_url .= "?submodul=".json_encode($data_modul).'#form_simple_'.str_slug($this->table_name);
			      	$url = $current_url;
			    }else{
			    	$url = url("$mainpath/detail/$row->id");
			    }	
            	$td .= "<a title='Detail Data' href='$url' class='btn btn-xs btn-info'><i class='fa fa-eye'></i></a>&nbsp;";
            }
      		
      		if($priv->is_edit):

      			if($this->is_sub==true) {         			
         			$data_modul = ['modul'=>$this->table_name,'action'=>"edit","id"=>$row->id];
         			$current_url = Request::url();
         			$current_url .= "?submodul=".json_encode($data_modul).'#form_simple_'.str_slug($this->table_name);
			      	$url = $current_url;
			    }else{			    	
			    	$url = url("$mainpath/edit/$row->id");
			    }
      			
      			$td .= "<a title='Edit Data'  href='$url' class='btn btn-row-edit btn-xs btn-warning'><i class='fa fa-pencil'></i></a>&nbsp;";
            endif;

            if($priv->is_delete):
            	if($this->is_sub==true) {         			
         			$data_modul = ['modul'=>$this->table_name,'action'=>"detail","id"=>$row->id];
         			$current_url = route($this->controller_name.'GetDelete',['id'=>$row->id]);         			
			      	$url = $current_url;
			    }else{
			    	$url = url("$mainpath/delete/$row->id");
			    }
            	$td .= "<a title='Delete Data' href='javascript:;' onclick='swal({   title: \"Are you sure?\",   text: \"You will not be able to recover this record data!\",   type: \"warning\",   showCancelButton: true,   confirmButtonColor: \"#DD6B55\",   confirmButtonText: \"Yes, delete it!\",   closeOnConfirm: false }, function(){  location.href=\"$url\" });'  class='btn btn-xs btn-danger' ><i class='fa fa-trash'></i></a>&nbsp;";
            endif;  

                     
            
            if(count(@$addaction)):				
                foreach($addaction as $fb):
                	$ajax  = (isset($fb["ajax"]))?"ajax-button":"";
                	$color = (isset($fb['color']))?"btn-".$fb['color']:"btn-info";
                	$class = "btn btn-xs $color ".$ajax;
                	$class = (isset($fb['class']))?$fb['class']:$class;
                	$url   = str_replace(array("%id%","%name%"),array($row->id,$row->{$title_field}),$fb["route"]);
                	$url   .= '?referal='.urlencode(Request::url());
                	$td    .= "<a title='".$fb["label"]."' href='".$url."' 
                	class='$class'><i class='".$fb["icon"]."'></i></a>&nbsp;";
                endforeach;
            endif;

          	$html_content[] = $td;
          endif;

	      $html_contents[] = $html_content;
		} //end foreach data[result]
 		
 		$html_contents = ['html'=>$html_contents,'data'=>$data['result']];
		$this->hook_html_index($html,$data);


		$data['html_contents'] = $html_contents['html'];

		if($this->index_table_only == true) {
			return view("crudbooster::default.table_simple",$data);	
		}else{
			return view("crudbooster::default.index",$data);
		}
		
	}

	public function getDataTables() {
		$posts = Request::all();
		$result = array();

		$cols = array();

		$limit = ($posts['length'])?:10;

		$columns_table = $this->columns_table;		
		$rows          = DB::table($this->table)->select(DB::raw("SQL_CALC_FOUND_ROWS ".$this->table.".".$this->primary_key))
		->take($limit)
		->skip($posts['start']);
		
		$e = 0;
		foreach($columns_table as $index => $coltab) {
			
			$join = @$coltab['join'];
			if($join) {
				$join_exp   = explode(',', $join);
				$join_table = $join_exp[0];
				$join_name  = $join_exp[1];
			}
			$field = $coltab['name'];

			//Jika ada subquery
			if(strpos($field, ' as ')!==FALSE) {
				$field = substr($field, strpos($field, ' as ')+4);
				$rows->addselect(DB::raw($coltab['name']));
				$columns_table[$index]['field']       = $field;
				$columns_table[$index]['field_raw']   = $field;
				$columns_table[$index]['field_with']  = $field;
				$columns_table[$index]['is_subquery'] = true;
				continue;
			}

			if($join) {		
				//field = id relasi nya
				$join_alias = $join_table.$index;
				$rows->leftjoin($join_table.' as '.$join_alias,$join_alias.'.id','=',$this->table.'.'.$field);
				$rows->addselect($join_alias.'.'.$join_name.' as '.$join_name.'_'.$join_alias);
				$rows->addselect($this->table.'.'.$field); #field asli tetap di masukkan
				$alias[]                             = $join_alias;
				$columns_table[$index]['field']      = $join_name.'_'.$join_alias;
				$columns_table[$index]['field_with'] = $join_alias.'.'.$join_name;
				$columns_table[$index]['field_raw']  = $join_name;
				$cols[] = $columns_table[$index]['field'];
			}else{
				$rows->addselect($this->table.'.'.$field);
				$columns_table[$index]['field_raw'] = $field;
				$columns_table[$index]['field_with'] = $this->table.'.'.$field;
				$cols[] = $field;
			}			
		}
 

		if($posts['search']['value']) {
			$search = $posts['search']['value'];
			$rows->where(function($w) use ($columns_table,$search) {
				foreach($columns_table as $col) {		
						if(!$col['field_with']) continue;					
						$w->orwhere($col['field_with'],"like","%".$search."%");				
				}
			});		
		}

		if($posts['browse_where']) {
			$rows->whereraw($posts['browse_where']);
		}

		if($posts['order']) {
			foreach($posts['order'] as $order) {
				$column = $cols[$order['column']];
				$column_sort = $order['dir'];
				$rows->orderby($column,$column_sort);
			}
		}

		$images = ['jpg','jpeg','png','gif','bmp'];
		$files  = ['pdf','doc','docx','xls','xlsx','txt'];
		
		$data   = array();
		foreach($rows->get() as $row) {
			$data2 = array();
			foreach($row as $v) {
				$ext = pathinfo($v, PATHINFO_EXTENSION);

				if(strpos('http://', $v)!==FALSE) {					
					if(in_array($ext, $images)) {
						$v = "<img src='".$v."' class='img-circle' width='60px' height='60px'/>";
					}
					if(in_array($ext, $files)) {
						$v = "<a href='".$v."' title='Download File'>Download File</a>";
					}
				}else{
					if(in_array($ext, $images)) {
						$v = "<img src='".asset($v)."' class='img-thumbnail' style='width:90px;height:60px'/>";
					}
					if(in_array($ext, $files)) {
						$v = "<a href='".asset($v)."' title='Download File'>Download File</a>";
					}
				}

				$data2[] = $v;
			}
			$data[] = $data2;
		}

		$result['draw'] = $posts['draw'];
		$result['recordsTotal'] = DB::table($this->table)->count();
		$result['recordsFiltered'] = DB::select(DB::raw("select found_rows() as total"))[0]->total;
		$result['data'] = $data;
		return response()->json($result);
	}

	public function getCurrentDataTables($id) {
		$row = DB::table($this->table)->where($this->table.".id",$id)->select("id",$this->title_field.' as label')->first();
		return response()->json($row);
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
		$parid    = Request::get('parid');
		$parfield = Request::get('parfield');
		$limit 	  = Request::get('limit')?:10;

		if(Cache::has('columns_'.$this->table)) {
			$columns = Cache::get('columns_'.$this->table);	
		}else{
			$columns = Cache::rememberForever('columns_'.$this->table, function() {
			    return \Schema::getColumnListing($this->table);
			});
		}

		if($q || $id || $parid || $parfield) {
			$rows = DB::table($this->table);
			$rows->select('id',$this->title_field.' as text');
			$rows->where($this->title_field,'like','%'.$q.'%');
			$rows->take($limit);

			if($id) {
				$rows->where("id",$id);
			}

			if($parid && $parfield) {
				$rows->where($parfield,$parid);
			}

			if(Session::get('foreign_key')) {				
				foreach(Session::get('foreign_key') as $k=>$v) {
					if(in_array($k, $columns)){
						$rows->where($this->table.'.'.$k,$v);
					}
				}
			}

			$result          = array();
			$result['items'] = $rows->get();
		}else{
			$result          = array();
			$result['items'] = array();
		}
		return response()->json($result);
	}

	public function getFindGroupData() {
		$column = Request::get('column');
		$raw = explode('.',$column);
		$table = $raw[0];
		$table = substr($table,0,(strlen($table)-1));
		$table = (strpos($column, '.')!==FALSE)?$table:$this->table;
		$col = (strpos($column, '.')!==FALSE)?$raw[1]:$column;

		if(Cache::has('find_group_data'.$column)) {
			$rows = Cache::get('find_group_data'.$column);
		}else{
			$rows = DB::table($table)->groupby($col)->lists($col);
			foreach($rows as &$row) {
				$row = trim(str_limit(strip_tags($row),80));
			}	
			if($rows) Cache::put('find_group_data'.$column,$rows,15);
		}		
		return response()->json($rows);
	}

	public function validation() {

		$request_all = Request::all();
		$array_input = array();
		foreach($this->data_inputan as $di) {
			$ai = array();			
			if(@$di['required']) {
				$ai[] = 'required';
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
				$id = get_row_id();
				$id = intval($id);
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
			$res = redirect()->back()->with("errors",$message)->withInput();
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

	public function getAdd()
	{
		$data['page_title']      = $this->data['module_name'].": Add New Data";
		$data['page_menu']       = Route::getCurrentRoute()->getActionName();	
		$data['table_name']      = $this->table_name;
		$data['referal']         = $this->referal;
		$data['controller_name'] = $this->controller_name;
		$data['parent_field']    = $this->parent_field;
		$data['parent_id']       = $this->parent_id;

		if($this->is_sub) {
			return view('crudbooster::default.form_simple',$data)->render();
		}else{
			return view('crudbooster::default.form',$data);
		}
		
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

		if(Request::get('addmore')) {
			return redirect(mainpath().'/add?'.$ref_parameter)->with(['message'=>'The data has been added !, please add more...','message_type'=>'success']);
		}

		if(Request::get('referal')) {
			return redirect(Request::get('referal'))->with(['message'=>'The data has been added !','message_type'=>'success']);
		}else{
			return redirect(mainpath().'/edit/'.$lastid.'?'.$ref_parameter)->with(['message'=>"The data has been added !",'message_type'=>'success']);	
		}
		
	}
	
	public function getEdit($id)
	{				
		$data['row']             = DB::table($this->table)->where($this->primary_key,$id)->first();		
		$data['page_menu']       = Route::getCurrentRoute()->getActionName();
		$title_field             = $this->title_field;
		$data['page_title']      = $this->data['module_name'].": Edit ".$data['row']->{$title_field};
		$data['table_name']      = $this->table_name;
		$data['referal']         = $this->referal;
		$data['controller_name'] = $this->controller_name;
		$data['parent_field']    = $this->parent_field;
		$data['parent_id']       = $this->parent_id;

		if($this->is_sub) {
			return view('crudbooster::default.form_simple',$data)->render();
		}else{
			return view('crudbooster::default.form',$data);
		}
	}

	public function getDetail($id)
	{				
		$data['row']             = DB::table($this->table)->where($this->primary_key,$id)->first();		
		$data['page_menu']       = Route::getCurrentRoute()->getActionName();
		$title_field             = $this->title_field;
		$data['page_title']      = $this->data['module_name'].": Detail ".$data['row']->{$title_field};
		$data['table_name']      = $this->table_name;
		$data['referal']         = $this->referal;
		$data['controller_name'] = $this->controller_name;
		$data['parent_field']    = $this->parent_field;
		$data['parent_id']       = $this->parent_id;

		if($this->is_sub) {
			return view('crudbooster::default.form_simple',$data)->render();			
		}else{
			return view('crudbooster::default.form',$data);
		}
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

		if(Request::get('addmore')) {
			return redirect(mainpath().'/add?'.$ref_parameter)->with(['message'=>'The data has been added !, please add more...','message_type'=>'success']);
		}

		if(Request::get('referal')) {
			return redirect(Request::get('referal'))->with(['message'=>'The data has been added !','message_type'=>'success']);
		}else{
			return redirect()->back()->with(['message'=>"The data has been updated !",'message_type'=>'success']);
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

		if(Storage::exists($row->{$column})) Storage::delete($row->{$column});

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

	public function get_primary_company($field) {
		$row = DB::table('cms_companies')->where('is_primary',1)->first();
		return $row->{$field};
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

	public function mainpath($path='') {
		$path = ($path)?"/$path":"";
		$controllername = str_replace(["\crocodicstudio\crudbooster\controllers\\","App\Http\Controllers\\"],"",strtok(Route::currentRouteAction(),'@') );		
		$route_url = route($controllername.'GetIndex');		
		return $route_url.$path;		
	}

	public function get_cols() {
		return $this->col;
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
