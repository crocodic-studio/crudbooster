<?php namespace crocodicstudio\crudbooster\controllers;

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

class ApiCustomController extends CBController {

	
	public function __construct() {		
		$this->table       = 'cms_apicustom';
		$this->primary_key = 'id';
		$this->title_field = "nama";
		$this->button_show_data = false;
		$this->button_new_data = false;
		$this->button_delete_data = false;
	

		$this->col = array();
		$this->col[] = array("label"=>"Name","name"=>"nama");

		$this->form = array(); 
		$this->form[] = array("label"=>"Nama","name"=>"nama");
		$this->form[] = array("label"=>"Permalink","name"=>"permalink",'readonly'=>true);
		$this->form[] = array("label"=>"Aksi","name"=>"aksi");
		$this->form[] = array("label"=>"kolom","name"=>"kolom",'type'=>'textarea');
		$this->form[] = array("label"=>"Sub Query","name"=>"sub_query_1",'type'=>'textarea');
		$this->form[] = array("label"=>"SQL Where","name"=>"sql_where",'type'=>'textarea');
		$this->form[] = array("label"=>"Keterangan","name"=>"keterangan",'type'=>'textarea');
		$this->form[] = array("label"=>"Parameter","name"=>"parameter",'type'=>'text');				
		
		$this->constructor();
	}


	function getIndex() {		

		$apicustom = DB::table('cms_apicustom')->where('permalink','')->get();
		foreach($apicustom as $a) {
			$permalink = str_replace(' ','_',strtolower($a->nama));
			DB::table('cms_apicustom')->where('id',$a->id)->update(array('permalink'=>$permalink));
		}

		$data = array();

		$tables = list_tables();
		$tables_list = array();
		foreach($tables as $tab) {
			foreach ($tab as $key => $value) {
				$tables_list[] = $value;				
			}
		}

		$data['tables'] = $tables_list;
		$data['page_title'] = 'API Generate & Documentation';
		$data['page_menu'] = Route::getCurrentRoute()->getActionName();
		$data['screetkey'] = Cache::get('screetkey');
		return view('crudbooster::api_generator',$data);
	}
 
	function getGenerateScreetKey() {				

		$string = Cache::rememberForever('screetkey', function() {
		    return md5(str_random(10));
		});
		
		$response = array();
		$response['key'] = $string;
		$response['url'] = url('/');
		return response()->json($response);
	}


	function getColumnTable($table,$type='list') {
		$result = array();

		if(Cache::has('get_column_table'.$table.$type)) {
			$result = Cache::get('get_column_table'.$table.$type);
			return response()->json($result);
		}

		$cols = DB::getSchemaBuilder()->getColumnListing($table);
		
		$result = $cols;
		unset($result['created_at']);
		unset($result['updated_at']);
		$new_result = array(); 
		foreach($result as $ro) {
			$new_result[] = $ro;
			if($type=='list' || $type=='detail') {
				if(substr($ro,0,3)=='id_') {
					$table2 = substr($ro,3);
					$t2 = DB::getSchemaBuilder()->getColumnListing($table2);
					foreach($t2 as $t) {
						if($t!='id' && $t!='created_at' && $t!='updated_at') {
							$t = str_replace("_$table2","",$t);
							$new_result[] = $table2.'_'.$t;
						}
					}
				}
			}
		}

		Cache::put('get_column_table'.$table.$type,$new_result,60);
		
		return response()->json($new_result);
	}

	function postSaveApiCustom() {
		$a = array();
		$kolom = Request::input('kolom');

		if(Request::input('sub_query_1')) {
			$s = explode(' as ',Request::input('sub_query_1'));
			$kolom[] = $s[1];
		}


		$a['tabel']       = Request::input('tabel');
		$a['aksi']        = Request::input('aksi');
		$a['kolom']       = implode(',',Request::input('kolom'));
		if(g('sub_query_1')) $a['sub_query_1'] = Request::input('sub_query_1');
		if(g('sql_where')) $a['sql_where']   = Request::input('sql_where');
		$a['nama']        = Request::input('nama');
		if(g('keterangan')) $a['keterangan']  = Request::input('keterangan');
		if(g('parameter')) $a['parameter']   = Request::input('parameter');
		$a['permalink']   = str_replace(' ','_',strtolower($a['nama']));
		DB::table('cms_apicustom')->insert($a);

		$controllername = ucwords(str_replace('_',' ',$a['permalink']));
		$controllername = str_replace(' ', '', $controllername);
		
		generate_api($controllername,$a['tabel'],$a['permalink'],$a['aksi']);

		$back = $_SERVER['HTTP_REFERER'];
		echo "<script>alert('API Added Successfully');location.href='$back';</script>";

	}

	function getDeleteApi($id) {
		$row = DB::table('cms_apicustom')->where('id',$id)->first();
		DB::table('cms_apicustom')->where('id',$id)->delete();

		$controllername = ucwords(str_replace('_',' ',$row->permalink));
		$controllername = str_replace(' ', '', $controllername);
		@unlink("app/Http/Controllers/Api".$controllername."Controller.php");

		$back = $_SERVER['HTTP_REFERER'];
		echo "<script>alert('Delete Api Successfully !');location.href='$back';</script>";
	}

	

}
