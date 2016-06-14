<?php namespace App\Http\Controllers;
error_reporting(E_ALL ^ E_NOTICE ^ E_STRICT);

/*
Documentation
Controller ini sudah mewakili fungsi CRUD pada setiap controller modul.
Buatlah sebuah controller baru di App\Http\Controllers\ dengan nama sesuai tabel dan kapital serta hindari spasi
contoh : NamaTabelController.php.
Kemudian buat sebuah method '__construct' dengan isian sebagai berikut : 

1). Configure Dashboard : 

$this->col 		= array();
$this->col[] 	= array('label'=>'NAMALABEL','field'=>'NAMAKOLOM_FIELD');

Array $this->col is use for Dashboard Display
There are some key :
	label 			(required)	: label name
	field 			(required)	: field tabel
	join 			(optional)	: relational tabel name
	image 			(optional)	: boolean
	download		(optional)	: boolean
	callback_html 	(optional)	: Write any html code here. Use %field% as value.
	callback_php 	(optional)	: Write any php code here. Use %field% as value.

2). Configure Form :

$this->form 	= array();
$this->form[] 	= array('label'=>'NAMALABEL','name'=>'NAMAFIELD');

Array $this->form is use for Form Display.
There are some key :
	label 				(required)	: label name
	name 				(required)	: field tabel
	type 				(required)	: (text,textarea,radio,select,wysiwyg,select2,datepicker,datetimepicker,hidden,password,upload,browse,qrcode)
	dataenum			(optional)	: support only for type 'select,radio', ex : array('BMW','MERCY','TOYOTA') or array('0|BMW','1|MERCY','2|TOYOTA')
	datatable			(optional)	: support only for type 'select', this will load data from tabel, ex : "NAMATABEL, NAMAKOLOM_STRING"
	datatable_where		(optional)	: sql where query for datatable, ex : status != 'active' and status != 'available'
	select2_source 		(optional)	: name other controller, ex : NamaTabelController
	sub_select			(optional) 	: name for child select. ex for case province and city. 
	help 				(optional) 	: help text
	image 				(optional)	: boolean
	min 				(optional)	: value minimal
	max 				(optional) 	: value maximal
	required 			(optional)	: boolean
	mimes 				(optional)	: good for specify the file type upload, sparate by comma, ex : jpg,png,gif
	value 				(optional)	: Any default value.
	browse_source		(optional)  : Controller Name, ex : NameController
	browse_columns		(optional)  : Only if type is 'browse', ex : (new CompaniesController)->get_cols()
	browse_where		(optional)	: sql where query for browse , ex : status != 'active' and status != 'available'
	type[qrcode][size]	(required)	: size of qr code
	type[qrcode][color]	(required)	: color hex of qr code
	type[qrcode][text]	(required)	: DOM id/name qr code

3). Configure Form Tab (Children Module) : 

$this->form_tab 	= array();
$this->form_tab[] 	= array('label'=>'NAMALABEL','icon'=>'fa fa-bars','route'=>'URL','filter_field'=>'RELATION_FIELD_NAME');

Array $this->form_tab is use for children module tab. 
There are some key : 
	label 			: tab label name
	icon 			: set own awesome icon, ex : fa fa-bars
	route 			: url for tab, ex : action('NamaTabelController@getIndex')
	filter_field 	: field name that use for filter / relation field name. 
If use this method / configure, you should add key `value` @ child form array. With example : $_GET['where'][FILTER_FIELD]
dont forget replace FILTER_FIELD.

4). Hook
This featured is for modify action after or before default do action. These bellow function name you can use 
	hook_before_index(&$result)
	hook_html_index(&$html_contents)
	hook_before_add($add)
	hook_after_add($id)
	hook_before_edit($arr,$id)
	hook_after_edit($id)
	hook_before_delete($id)
	hook_after_delete($id)

*/

use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;

use Session;
use Request;
use DB;
use App;
use Route;
use Hash;
use Cache;
use Validator;
use PDF;
use Excel;

abstract class Controller extends BaseController {
 
	use DispatchesCommands, ValidatesRequests;
	var $data_inputan;
	var $dashboard;
	var $columns_table;
	var $prefixroute;
	var $modulname;
	var $table;
	var $primkey;
	var $titlefield;
	var $theme;
	var $arr           = array();
	var $col           = array();
	var $form          = array();
	var $form_tab      = array();
	var $data          = array();
	var $addaction     = array();
	var $index_orderby      = array();
	var $password_candidate = array('pass','password','pin','pwd','passwd');
	var $date_candidate     = array('date','tanggal','tgl','created_at','waktu','time');
	var $limit              = 20;
	var $index_return       = false;
	var $setting;

	public function init_setting() {
		$setting = DB::table('cms_settings')->get();
		$setting_array = array();
		foreach($setting as $set) {
			$setting_array[$set->name] = $set->content;
		}
		$this->setting = json_decode(json_encode($setting_array));		
	}

	public function hook_before_index(&$result) {

	}

	public function hook_html_index(&$html_contents) {

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

	public function get_cols() {
		return $this->col;
	}

	public function constructor() {
		
		if(Session::get('admin_id')=='') redirect()->action("AdminController@getLogin")->with('message','Anda belum Login')->send();
		if(Session::get('admin_lock')) return redirect('admin/lockscreen');	

		$this->init_setting();
 		
		$current_url = Request::url();

		//insert log
		$a = array();
		$a['created_at'] = date('Y-m-d H:i:s');
		$a['ipaddress'] = $_SERVER['REMOTE_ADDR'];
		$a['useragent'] = $_SERVER['HTTP_USER_AGENT'];
		$a['url'] = $current_url;
		$a['id_cms_users'] = Session::get('admin_id');
		DB::table('cms_logs')->insert($a);

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
						$w[] = "&where[".$ft['filter_field']."]=%id%";						
						$ft['route'] = $ft['route'].'?is_sub='.Request::get('is_sub').implode('',$w);						
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
					$ft['route'] = $ft['route'].'?is_sub=1&where['.$ft['filter_field'].']=%id%';
				}

				$first_form_tab   = array();
				$first_form_tab[] = array("label"=>"Edit Data","route"=>url("admin/".$this->table."/edit/".Request::segment(4)));
				$this->form_tab   = array_merge($first_form_tab,$this->form_tab);
				if(Request::segment(3)=='edit' || Request::segment(3)=='add') Session::put('form_tab',$this->form_tab);		

			}
		}

		if(Session::get('filter_field')) {
			foreach($this->form as &$f) {
				foreach(Session::get('filter_field') as $k=>$v) {
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
		$this->data['form_tab']  = $this->form_tab; 	
		$this->data['addaction'] = $this->addaction;	
 
		$this->data['current_controller'] = stripslashes(strtok(str_replace("App\Http\Controllers","",Route::currentRouteAction()),"@"));
	
		$tablename = ucfirst($this->table);		

		$this->data['mainpath'] = $this->mainpath();
		$this->dashboard = url($this->data['mainpath']);		

		Session::put('current_mainpath',$this->data['mainpath']);
		$privileges = DB::table("cms_privileges_roles")
		            ->join("cms_moduls","cms_moduls.id","=","cms_privileges_roles.id_cms_moduls")
		            ->where("cms_privileges_roles.id_cms_privileges",Session::get("admin_privileges"))
		            ->where("cms_moduls.path",'like',$this->data['mainpath'].'%')->first();
		
		$this->data['priv']       = $privileges;
		$this->data['dashboard']  = $this->dashboard;		
		$this->data['table']      = $this->table;
		$this->data['modulname']  = $this->modulname;
		$this->data['titlefield'] = $this->titlefield;
		$this->data['appname']    = $this->setting->appname;
		$this->data['setting'] 	  = $this->setting;		
        view()->share($this->data);
	} 

	public function mainpath($path='') {
		$path = ($path)?"/$path":"";
		$url = trim(str_replace(url(),'',Request::url()),'/');

		if(strpos($url, '/add')) {
			return substr($url, 0, strpos($url,'/add')).$path;

		}elseif (strpos($url, '/edit')) {
			return substr($url, 0, strpos($url,'/edit')).$path;

		}else{
			return $url.$path;
		}
	}

	public function getIndex()
	{
		DB::connection()->enableQueryLog();		

		$data['page_title']       = $this->modulname;
		$data['page_description'] = "Data List";
		$data['page_menu'] 		  = Route::getCurrentRoute()->getActionName();
		$data['date_candidate']   = $this->date_candidate;
		$data['limit'] = $limit   = (Request::get('limit'))?:$this->limit;
		$columns                  = \Schema::getColumnListing($this->table);		
		$columns_table            = $this->columns_table;		
		$result                   = DB::table($this->table)->select(DB::raw("SQL_CALC_FOUND_ROWS ".$this->table.".".$this->primkey));
		$this->hook_before_index($result);
		if(Session::get('filter_field')) {
			foreach(Session::get('filter_field') as $k=>$v) {
				if(in_array($k, $columns)){
					$result->where($this->table.'.'.$k,$v);
				}
			}
		}

		if(@$this->data['priv']->sql_where) {
			$sqlwhere = $this->data['priv']->sql_where;
			$sqlwhere = str_ireplace("where ","",$sqlwhere);
			$sqlwhere = str_replace(array("[admin_id]","[admin_id_companies]"),array(Session::get('admin_id'),Session::get('admin_id_companies')),$sqlwhere);
			$result->whereraw($sqlwhere);
		}

		$jointmp = array();
		$alias = array();
		$e = 0;
		foreach($columns_table as $index => $coltab) {
			$join = @$coltab['join'];
			$join_id = @$coltab['join_id'];
			$field = $coltab['field'];
			if($join) {				
				$expjoin = explode(".",$join);
				
				foreach($expjoin as $ej) {	
					if(in_array($ej,$jointmp)) {
						$e++;
						continue;							
					}

					$id = ($join_id)?:"id_".$ej;

					if($e==0) {
						$field2 = $this->table.".".$id;
					}else{
						$table_join = $expjoin[$e-1].($e-1);
						if(in_array($table_join, $alias)) {
							$field2 = $table_join.'.'.$id;
						}else{
							$field2 = $this->table.'.'.$id;
						}
					}					

					$alias[] = $ej.$e;				
					$result->leftjoin($ej." as ".$ej.$e,$ej.$e.".id","=", $field2);
					array_push($jointmp,$ej);

					$table_name = $ej.$e;
					$table_columns = \Schema::getColumnListing($ej);
					if(in_array($field, $table_columns)) {
						$result->addselect($table_name.'.'.$field.' as '.$field.'_'.$table_name);
						$columns_table[$index]['field'] = $field.'_'.$table_name;
						$columns_table[$index]['field_with'] = $table_name.'.'.$field;
						$columns_table[$index]['field_raw'] = $field;	
					}
					
					$e++;									
				} 
				
			}else{
				$result->addselect($this->table.'.'.$field);
				$columns_table[$index]['field_raw'] = $field;
				$columns_table[$index]['field_with'] = $this->table.'.'.$field;
			}			
		}
 

		if(Request::get('q')) {
			
			$result->where(function($w) use ($columns_table) {
				foreach($columns_table as $col) {		
						if(!$col['field_with']) continue;					
						$w->orwhere($col['field_with'],"like","%".Request::get("q")."%");				
				}
			});		
		}			

		if(Request::get('where')) {			
			foreach(Request::get('where') as $k=>$v) {
				$result->where($this->table.'.'.$k,$v); 
			}			
		}

		if(Request::get('sort_column') && Request::get('sort_type')) {
			$result->orderby(Request::get('sort_column'),Request::get('sort_type'));
		}

		if(Request::get('filter_data_column') && Request::get('filter_data_by')) {
			$filter_column = Request::get('filter_data_column');
			$filter_column = (strpos($filter_column, '.')!==FALSE)?$filter_column:$this->table.'.'.$filter_column;
			$filter_data = Request::get('filter_data_by');
			$filter_data = ($filter_data == 'FILTER_TEXT_MANUAL')?Request::get('filter_data_text'):$filter_data;
			$result->where($filter_column,$filter_data);
		}

		if(Request::get('filter_date_range1') && Request::get('filter_date_range2')) {
			$filter_column = Request::get('filter_data_column');
			$filter_column = (strpos($filter_column, '.')!==FALSE)?$filter_column:$this->table.'.'.$filter_column;
			$date1 = Request::get('filter_date_range1');
			$date2 = Request::get('filter_date_range2');
			$result->where(function($w) use($filter_column,$date1,$date2) {
				$w->whereraw("DATE($filter_column) >= '$date1'")->whereraw("DATE($filter_column) <= '$date2'");
			});
		}

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
			$data['result'] = $result->orderby($this->primkey,'desc')->paginate($limit);	
		}
		
		$data['columns'] = $columns_table;

		if($this->index_return==true) {
			$result = array();
			$result = $data;
			$rows = $result['result'];
			unset($result['result']);
			foreach($rows as $row) {
				$result['result'][] = $row;
			}
			return $result;			
		}

		$queries = DB::getQueryLog();		
		
		if(Request::get('format')=='total') {
			return DB::select(DB::raw("SELECT FOUND_ROWS() as total"))[0]->total;
		}


		//LISTING INDEX HTML
		$priv = $this->data['priv'];
		$addaction = $this->data['addaction'];
		$mainpath = $this->mainpath();		
		$titlefield = $this->titlefield;
		$html_contents = array();
		foreach($data['result'] as $row) {
			$html_content = array();

			$html_content[] = "<input type='checkbox' class='checkbox' name='checkbox' value='$row->id'/>";

			foreach($columns_table as $col) {     
	          if($col['visible']===0) continue;

	          $value = @$row->{$col['field']};  
	          $title = @$row->{$this->titlefield};
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
	            $value = strip_tags($value);
	            $value = str_limit($value,50);

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
            	$td .= "<a title='Detail Data' href='".url("$mainpath/edit/$row->id?".urldecode(http_build_query(@$_GET)) )."&detail=1' class='btn btn-xs btn-info'><i class='fa fa-eye'></i></a>&nbsp;";
            }
      		
      		if($priv->is_edit):
      			$param = @$_GET;
      			unset($param['detail']);
      			$td .= "<a title='Edit Data'  href='".url("$mainpath/edit/$row->id?".urldecode(http_build_query(@$param)) )."' class='btn btn-row-edit btn-xs btn-warning'><i class='fa fa-pencil'></i></a>&nbsp;";
            endif;

            if($priv->is_delete):
            	$td .= "<a title='Delete Data' href='".url("$mainpath/delete/$row->id")."' onclick='if(!confirm(\"DELETE CONFIRMATION :\\n----\\nAre you sure want to delete this data ?.\\nWARNING DELETE DATA CAN NOT BE UNDO !\")) return false'  class='btn btn-xs btn-danger' ><i class='fa fa-trash'></i></a>&nbsp;";
            endif;  

                     
            
            if(count(@$addaction)):
                foreach($addaction as $fb):
                	$ajax = (isset($fb["ajax"]))?"ajax-button":"";
                	$color = (isset($fb['color']))?"btn-".$fb['color']:"btn-info";
                	$class = "btn btn-xs $color ".$ajax;
                	$class = (isset($fb['class']))?$fb['class']:$class;
                	$td .= "<a title='".$fb["label"]."' href='".str_replace(array("%id%","%name%"),array($row->id,$row->{$titlefield}),$fb["route"])."' 
                	class='$class'><i class='".$fb["icon"]."'></i></a>&nbsp;";
                endforeach;
            endif;

          	$html_content[] = $td;
          endif;

	      $html_contents[] = $html_content;
		} //end foreach data[result]

		$this->hook_html_index($html_contents);

		$i = 0;
		foreach($html_contents as &$content) {			
			foreach($content as &$row_content) {
				$row_data = $data['result'][$i];
				foreach($row_data as $key=>$val) {
					$row_content = str_ireplace("[$key]",$val,$row_content);
				}				
			}	
			$i++;		
		}

		$data['html_contents'] = $html_contents;
		
		return view($this->theme.".index",$data);
	}

	public function getDataTables() {
		$posts = Request::all();
		$result = array();

		$cols = array();

		$limit = ($posts['length'])?:10;

		$columns_table = $this->columns_table;		
		$rows          = DB::table($this->table)->select(DB::raw("SQL_CALC_FOUND_ROWS ".$this->table.".".$this->primkey))
		->take($limit)
		->skip($posts['start']);

		$jointmp = array();
		$e = 0;
		foreach($columns_table as $index => $coltab) {
			$join = @$coltab['join'];
			$join_id = @$coltab['join_id'];
			$field = $coltab['field'];
			if($join) {				
				$expjoin = explode(".",$join);
				
				foreach($expjoin as $ej) {	
					if(in_array($ej,$jointmp)) {
						$e++;
						continue;							
					}

					$id = ($join_id)?:"id_".$ej;

					if($e==0) {
						$field2 = $this->table.".".$id;
					}else{
						$table_join = $expjoin[$e-1].($e-1);
						if(in_array($table_join, $alias)) {
							$field2 = $table_join.'.'.$id;
						}else{
							$field2 = $this->table.'.'.$id;
						}
					}
				
					$rows->leftjoin($ej." as ".$ej.$e,$ej.$e.".id","=", $field2);
					array_push($jointmp,$ej);

					$table_name = $ej.$e;
					$table_columns = \Schema::getColumnListing($ej);
					if(in_array($field, $table_columns)) {
						$rows->addselect($table_name.'.'.$field.' as '.$field.'_'.$table_name);
						$columns_table[$index]['field'] = $field.'_'.$table_name;
						$columns_table[$index]['field_with'] = $table_name.'.'.$field;
						$columns_table[$index]['field_raw'] = $field;	
						$cols[] = $columns_table[$index]['field'];
					}
					
					$e++;									
				} 
				
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

		$data = array();
		foreach($rows->get() as $row) {
			$data2 = array();
			foreach($row as $v) {
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
		$row = DB::table($this->table)->where($this->table.".id",$id)->select("id",$this->titlefield.' as label')->first();
		return response()->json($row);
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
				$view = view('admin.export',$response)->render();
				$pdf = App::make('dompdf.wrapper');
				$pdf->loadHTML($view);
				$pdf->setPaper($papersize,$paperorientation);
				return $pdf->stream($filename.'.pdf'); 
			break;
			case 'xls':
				Excel::create($filename, function($excel) use ($response) {
					$excel->setTitle($filename)
					->setCreator("crocodic.com")
					->setCompany($this->setting->appname);					
				    $excel->sheet($filename, function($sheet) use ($response) {
				    	$sheet->setOrientation($paperorientation);
				        $sheet->loadView('admin.export',$response);
				    });
				})->export('xls');
			break;
			case 'csv':
				Excel::create($filename, function($excel) use ($response) {
					$excel->setTitle($filename)
					->setCreator("crocodic.com")
					->setCompany($this->setting->appname);					
				    $excel->sheet($filename, function($sheet) use ($response) {
				    	$sheet->setOrientation($paperorientation);
				        $sheet->loadView('admin.export',$response);
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

		if($q || $id || $parid || $parfield) {
			$rows = DB::table($this->table);
			$rows->select('id',$this->titlefield.' as text');
			$rows->where($this->titlefield,'like','%'.$q.'%');
			$rows->take($limit);

			if($id) {
				$rows->where("id",$id);
			}

			if($parid && $parfield) {
				$rows->where($parfield,$parid);
			}

			if(Session::get('filter_field')) {
				$columns = \Schema::getColumnListing($this->table);	
				foreach(Session::get('filter_field') as $k=>$v) {
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
			$array_input[$name] = implode('|',$ai);
		}

		$validator = Validator::make(Request::all(),$array_input);
		
		if ($validator->fails()) 
		{
			$message = $validator->messages();			
			$res = redirect()->back()->with("errors",$message);
			\Session::driver()->save();
			$res->send();
        	exit();
		}
	}

	public function input_assignment() {		
		foreach($this->data_inputan as $ro) {
			$name = $ro['name'];
			$this->arr[$name] = Request::input($name);

			if(in_array($name, $this->password_candidate)) {
				if(!empty($this->arr[$name])) {
					$this->arr[$name] = Hash::make($this->arr[$name]);
				}else{
					unset($this->arr[$name]);
				}
			}

			if(@$ro['type']=='upload') {
				$upload_mode = @$this->setting->upload_mode?:'file';
				$upload_path = @$this->setting->upload_path?:'uploads/';
				unset($this->arr[$name]);
				if (Request::hasFile($name))
				{			
					$file               = Request::file($name);
					$fm                 = array();
					$fm['name']         = $_FILES[$name]['name'];					
					$fm['ext']          = $file->getClientOriginalExtension();
					$fm['size']         = $_FILES[$name]['size'];
					$fm['content_type'] = $_FILES[$name]['type'];

					if($upload_mode=='database') {
						$fm['filedata']     = file_get_contents($_FILES[$name]['tmp_name']);
						DB::table('cms_filemanager')->insert($fm);
						$id_fm              = DB::getPdo()->lastInsertId();
						DB::table('cms_filemanager')->where('id',$id_fm)->update(['id_md5' =>md5($id_fm)]);
						$filename         	= 'upload_virtual/files/'.md5($id_fm).'.'.$fm['ext'];
					}else{
						if(!file_exists($upload_path.date('Y-m'))) {
							if(!mkdir($upload_path.date('Y-m'),0777)) {
								die('Gagal buat folder '.$upload_path.date('Y-m'));
							}
						}
						
						$filename = md5(str_random(12)).'.'.$fm['ext'];
						$file->move($upload_path.date('Y-m'),$filename);						
						$filename = $upload_path.date('Y-m').'/'.$filename;
					}
					if(file_exists($filename)) $this->arr[$name] = $filename;  
				}
			}			
		}
	}

	public function getAdd()
	{
		$data['page_title'] = "Add New Data ( ".$this->modulname." )";
		$data['page_menu']  = Route::getCurrentRoute()->getActionName();	
		return view($this->theme.'.form',$data);
	}
	
	public function postAddSave() {
		$this->validation();	
		$this->input_assignment();	

		$this->hook_before_add($this->arr);						

		DB::table($this->table)->insert($this->arr);
		$lastid        = DB::getPdo()->lastInsertId();
		$ref_parameter = Request::input('ref_parameter');

		$this->hook_after_add($lastid);

		return redirect($this->mainpath().'/edit/'.$lastid.'?'.$ref_parameter)->with(['message'=>"Berhasil tambah data !",'message_type'=>'success']);
	}
	
	public function getEdit($id)
	{				
		$data['row'] = DB::table($this->table)->where($this->primkey,$id)->first();		
		$data['page_menu'] = Route::getCurrentRoute()->getActionName();
		$titlefield  = $this->titlefield;
		$data['page_title'] = "Edit Data ( ".$this->modulname." ".$data['row']->{$titlefield}." )";
		return view($this->theme.'.form',$data);
	}
	 
	public function postEditSave($id) {
		
		$this->validation();
		$this->input_assignment();

		$this->hook_before_edit($this->arr,$id);
		
		DB::table($this->table)->where($this->primkey,$id)->update($this->arr);

		$this->hook_after_edit($id);

		return redirect()->back()->with(['message'=>"Berhasil update data !",'message_type'=>'success']);
	}
	
	public function getDelete($id) {
		$this->hook_before_delete($id);
		DB::table($this->table)->where($this->primkey,$id)->delete();
		$this->hook_after_delete($id);
		return redirect()->back()->with(['message'=>"Berhasil menghapus data !",'message_type'=>"success"]);
	}

	public function postDeleteSelected() {
		$id = Request::input('id');
		if($id) {
			DB::table($this->table)->whereIn("id",$id)->delete();
		}
	}

	public function postUpload() {
		$name = 'userfile';
		if (Request::hasFile($name))
		{						

			$upload_mode = @$this->setting->upload_mode?:'file';
			$upload_path = @$this->setting->upload_path?:'uploads/';

			$file               = Request::file($name);
			$fm                 = array();
			$fm['name']         = $_FILES[$name]['name'];					
			$fm['ext']          = $file->getClientOriginalExtension();
			$fm['size']         = $_FILES[$name]['size'];
			$fm['content_type'] = $_FILES[$name]['type'];

			if($upload_mode=='database') {
				$fm['filedata']     = file_get_contents($_FILES[$name]['tmp_name']);
				DB::table('cms_filemanager')->insert($fm);
				$id_fm              = DB::getPdo()->lastInsertId();
				DB::table('cms_filemanager')->where('id',$id_fm)->update(['id_md5' =>md5($id_fm)]);
				$filename         	= 'upload_virtual/files/'.md5($id_fm).'.'.$fm['ext'];
			}else{
				if(!file_exists($upload_path.date('Y-m'))) {
					if(!mkdir($upload_path.date('Y-m'),0777)) {
						die('Gagal buat folder '.$upload_path.date('Y-m'));
					}
				}
				$filename = md5(str_random(12)).'.'.$fm['ext'];
				$file->move($upload_path.date('Y-m'),$filename);						
				$filename = $upload_path.date('Y-m').'/'.$filename;
			}

			$url      			= asset($filename);
            echo "<script>top.$('.mce-btn.mce-open').parent().find('.mce-textbox').val('$url').closest('.mce-window').find('.mce-primary').click();</script>";			
		}else{
			echo "<script>alert('Gagal upload file !');</script>";
		}
	}

	public function getDeleteImage() {		
		$id     = Request::get('id');
		$column = Request::get('column');
			
		$upload_mode = @$this->setting->upload_mode?:'file';
		if($upload_mode=='file') {
			$row = DB::table($this->table)->where($this->primkey,$id)->first();
			@unlink($row->{$column});
		}else{
			$image  = strtok(basename(Request::get('image')),'.');		
			DB::table('cms_filemanager')->where('id_md5',$image)->delete();
		}

		DB::table($this->table)->where($this->primkey,$id)->update(array($column=>NULL));

		return redirect()->back()->with(['message'=>"Berhasil menghapus gambar !",'message_type'=>"success"]);
	}

	public function get_primary_company($field) {
		$row = DB::table('cms_companies')->where('is_primary',1)->first();
		return $row->{$field};
	}
 
}
