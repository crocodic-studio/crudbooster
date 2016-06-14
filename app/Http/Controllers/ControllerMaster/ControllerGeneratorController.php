<?php namespace App\Http\Controllers;
error_reporting(E_ALL ^ E_NOTICE);
use Request;
use Session;
use App\Users;
use Validator;
use DB;
use Hash;
use Route;

class ControllerGeneratorController extends Controller {	

	var $image_candidate = array("image","picture","file","foto","gambar","photo","thumb","thumbnail");

	function getIndex() {
		if(!Session::get('admin_id')) return redirect('admin/login')->with("message","Silahkan Login Terlebih Dahulu !");

		$data = array();

		$exception_table = array('cms_dashboard','cms_logs','cms_moduls','cms_moduls_group','cms_privileges','cms_privileges_roles','cms_users','cms_apicustom','cms_settings','cms_companies','cms_filemanager');
		$tables = DB::select('SHOW TABLES');
		$tables_list = array();
		foreach($tables as $tab) {
			foreach ($tab as $key => $value) {
				if(in_array($value, $exception_table)) continue;
				$tables_list[] = $value;
			}
		}

		$data['tables'] = $tables_list;
		$data['page_title'] = 'Controller Generator';
		$data['page_menu'] = Route::getCurrentRoute()->getActionName();
		return view('admin.controller_generator',$data);
	}

	function getIsGenerate($table) {

		$controllername = ucwords(str_replace('_',' ',$table));
		$controllername = str_replace(' ','',$controllername).'Controller';
		$path = "app/Http/Controllers/";
		if(file_exists($path.'Admin'.$controllername.'.php')) {
			$result = array();
			$result['status'] = 1;
			$result['message'] = $path.'Admin'.$controllername.'.php';
			return response()->json($result);
		}else{
			$result = array();
			$result['status'] = 0;
			$result['message'] = $path.'Admin'.$controllername.'.php';
			return response()->json($result);
		}
	}

	private function get_namefield_table($coloms) {

		$name_col_candidate = array("name","nama","title","judul","content");	

		$name_col = '';

		foreach($coloms as $c) {
			foreach($name_col_candidate as $cc) {
				if( strpos($c,$cc) !==FALSE ) {
					$name_col = $c;
					break;
				}
			}
			if($name_col) break;
		}

		if($name_col == '') $name_col = 'id';

		return $name_col;
	}

	function getGenerate($table) {
		$controllername = ucwords(str_replace('_',' ',$table));
		$modulname = $controllername;
		$controllername = str_replace(' ','',$controllername).'Controller';
		$path = "app/Http/Controllers/";

		if(file_exists($path.$controllername.'.php')) {
			echo 0;
			exit;
		}
		$coloms = $this->getColumnTable($table);

		$name_col = $this->get_namefield_table($coloms);
				
$php = '
<?php 
namespace App\Http\Controllers;
error_reporting(E_ALL ^ E_NOTICE);

use Session;
use Request;
use DB;
use App;
use Route;
use Validator;

class Admin'.$controllername.' extends Controller {

	public function __construct() {
		$this->modulname     = "'.$modulname.'";
		$this->table         = "'.$table.'";
		$this->primkey       = "id";
		$this->titlefield    = "'.$name_col.'";
		$this->theme         = "admin.default";	
		$this->prefixroute   = "admin/";
		$this->index_orderby = array("id"=>"desc");	

		$this->col = array();
		';

		foreach($coloms as $c) {
		$label = str_replace("id_","",$c);
		$label = ucwords(str_replace("_"," ",$label));
		$field = $c;

		if(substr($field,0,3)=='id_') {
			$jointable = str_replace('id_','',$field);
			$joincols = $this->getColumnTable($jointable);
			$joinname = $this->get_namefield_table($joincols);
			$php .= "\t\t".'$this->col[] = array("label"=>"'.$label.'","field"=>"'.$joinname.'","join"=>"'.$jointable.'");'."\n";
		}else{
			$image = '';
			if(in_array($field, $this->image_candidate)) $image = ',"image"=>1';
			$php .= "\t\t".'$this->col[] = array("label"=>"'.$label.'","field"=>"'.$field.'" '.$image.');'."\n";	
		}

		}

		$php .= "\t\t\n".'$this->form = array();'."\n";

		foreach($coloms as $c) {
		$label = str_replace("id_","",$c);
		$label = ucwords(str_replace("_"," ",$label));		
		$field = $c;

			$typedata = DB::connection()->getDoctrineColumn($table, $field)->getType()->getName();
			$typedata = strtolower($typedata);
			switch($typedata) {
				default:
				case 'varchar':
				case 'char':
				$type = "text";
				break;
				case 'text':
				case 'longtext':
				$type = 'textarea';
				break;
				case 'date':
				$type = 'date';
				break;
				case 'datetime':
				case 'timestamp':
				$type = 'datetime';
				break;
			}

		$datatable = '';
		if(substr($field,0,3)=='id_') {
			$jointable = str_replace('id_','',$field);
			$joincols = $this->getColumnTable($jointable);
			$joinname = $this->get_namefield_table($joincols);
			$datatable = ',"datatable"=>"'.$jointable.','.$joinname.'"';
			$type = 'select';
		}

		
		if(in_array($field, $this->image_candidate)) {
			$type = 'upload';
		}

		$php .= "\t\t".'$this->form[] = array("label"=>"'.$label.'","name"=>"'.$field.'","type"=>"'.$type.'" '.$datatable.' );'."\n";	
		}

$php .= '																										
		
		$this->constructor();
	}
	
}
		';

		$php = trim($php);

		//create file controller
		file_put_contents($path.'Admin'.$controllername.'.php', $php);
		echo 1;
	}


	private function getColumnTable($table) {
		$cols = DB::getSchemaBuilder()->getColumnListing($table);
		$result = array();
		$result = $cols;

		$new_result = array(); 
		foreach($result as $ro) {
			if($ro=='created_at' || $ro=='updated_at' || $ro=='id') continue;
			$new_result[] = $ro;
		}
		return $new_result;
	}

	
}
