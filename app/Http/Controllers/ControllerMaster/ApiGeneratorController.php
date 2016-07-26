<?php namespace App\Http\Controllers;
error_reporting(E_ALL ^ E_NOTICE);
use Request;
use Session;
use App\Users;
use Validator;
use DB;
use Hash;
use Cache;
use Route;

class ApiGeneratorController extends Controller {	

	function getIndex() {
		if(!Session::get('admin_id')) return redirect('admin/login')->with("message","Silahkan Login Terlebih Dahulu !");

		$apicustom = DB::table('cms_apicustom')->where('permalink','')->get();
		foreach($apicustom as $a) {
			$permalink = str_replace(' ','_',strtolower($a->nama));
			DB::table('cms_apicustom')->where('id',$a->id)->update(array('permalink'=>$permalink));
		}

		$data = array();

		$exception_table = array('cms_dashboard','cms_logs','cms_moduls','cms_moduls_group','cms_privileges','cms_privileges_roles','cms_apicustom','cms_filemanager');
		$tables = DB::select('SHOW TABLES');
		$tables_list = array();
		foreach($tables as $tab) {
			foreach ($tab as $key => $value) {
				if(in_array($value, $exception_table)) continue;
				$tables_list[] = $value;
			}
		}

		$data['tables'] = $tables_list;
		$data['page_title'] = 'API Generate & Documentation';
		$data['page_menu'] = Route::getCurrentRoute()->getActionName();
		$data['screetkey'] = @unserialize(str_replace("* WARNING !! DO NOT REMOVE THIS BELLOW KEY *\n","",strip_tags(file_get_contents("resources/_token"))))['key'];
		return view('admin.api_generator',$data);
	}
 
	function getGenerateScreetKey() {
		$path = "resources/";
		$string = str_random(10);
		$string = md5($string);
		
		$data = "* WARNING !! DO NOT REMOVE THIS BELLOW KEY *\n";
		$data .= serialize(array("url"=>url('/'),'key'=>$string));

		@file_put_contents($path."_token", $data);
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
		if(Request::input('sub_query_2')) {
			$s = explode(' as ',Request::input('sub_query_2'));
			$kolom[] = $s[1];
		}
		if(Request::input('sub_query_3')) {
			$s = explode(' as ',Request::input('sub_query_3'));
			$kolom[] = $s[1];
		}

		$a['tabel'] = Request::input('tabel');
		$a['aksi'] = Request::input('aksi');
		$a['kolom'] = implode(',',Request::input('kolom'));
		$a['sub_query_1'] = Request::input('sub_query_1');
		$a['sub_query_2'] = Request::input('sub_query_2');
		$a['sub_query_3'] = Request::input('sub_query_3');
		$a['sql_where'] = Request::input('sql_where');
		$a['nama'] = Request::input('nama');
		$a['keterangan'] = Request::input('keterangan');
		$a['parameter'] = Request::input('parameter');
		$a['permalink'] = str_replace(' ','_',strtolower($a['nama']));
		DB::table('cms_apicustom')->insert($a);

		$controllername = ucwords(str_replace('_',' ',$a['permalink']));
		$controllername = str_replace(' ', '', $controllername);
		$this->create_api_controller($controllername,$a['permalink'],$a['aksi']);

		$back = $_SERVER['HTTP_REFERER'];
		echo "<script>alert('Added Api Successfully');location.href='$back';</script>";

	}


	function create_api_controller($controllername,$permalink,$tipe) {
$php = '
<?php namespace App\Http\Controllers;

date_default_timezone_set("Asia/Jakarta");
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

use Request;
use Session;
use Mail;
use Validator;
use DB;
use App;
use PDF;
use Excel;
use Hash;

class Api'.$controllername.'Controller extends ApiController {

	function __construct() {	
		$this->table 	 = "'.$table.'";
		$this->parameter = "'.$parameter.'";
		$this->permalink = "'.$permalink.'";
		$this->response  = "'.$response.'"; 
	}
';

$php .= "\n".'
	public function hook_before(&$postdata) {
		//optional if you want to make any fantastic code before api execute
	}';


$php .= "\n".'
	public function hook_after($postdata,&$result) {
		//optional if you want to make any fantastic code after api execute
	}';

$php .= "\n".'
	public function hook_query_list(&$data) {
		
	}';

$php .= "\n".'
	public function hook_query_detail(&$data) {
		
	}';

$php .= "\n".'
}
';

		$php = trim($php);
		$path = "app/Http/Controllers/";
		file_put_contents($path.'Api'.$controllername.'Controller.php', $php);
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
