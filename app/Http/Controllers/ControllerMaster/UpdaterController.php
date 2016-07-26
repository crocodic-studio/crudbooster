<?php namespace App\Http\Controllers;
set_time_limit(500);
error_reporting(E_ALL ^ E_NOTICE);
use Illuminate\Foundation\Bus\DispatchesCommands;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;

use Session;
use Request;
use DB;
use App;
use Route;
use Validator;
use Schema;

class UpdaterController extends BaseController {

	var $sqlpath = "install/crocodic_cms.sql";
	var $sql;


	function __construct() {
		$this->sql = SplitSQL($this->sqlpath);
	}

	public function getIndex() {
		if (ob_get_level() == 0) ob_start();

		$error_log = array();

		echo "Getting current tables...<br/>";
		ob_flush();
		flush();

		//GET CURRENT TABLES
		$tables_raw = DB::select('SHOW TABLES');
		$tables = array();
		foreach ($tables_raw as $table) {
		    foreach ($table as $key => $value)
		        $tables[] = $value;
		}

		$sql = $this->sql;
		$sql_tables = $sql['tables'];

		foreach($sql_tables as $table) {
			$tmp_table  = "tmp_".$table;	
			$tmp2_table = "tmp2_".$table;	

			echo "- Drop if exists $tmp_table<br/>";
			ob_flush();
			flush();

			echo "- Drop if exists $tmp2_table<br/>";
			ob_flush();
			flush();

			try{
				@Schema::dropIfExists($tmp_table);
				@Schema::dropIfExists($tmp2_table);
			}catch (\Illuminate\Database\QueryException $e) {
				
			}			

			echo "- Create table $tmp_table<br/>";
			ob_flush();
			flush();

			$this->create_table($table,$tmp_table);

			echo "- Create table $tmp2_table<br/>";
			ob_flush();
			flush();
			$this->create_table($table,$tmp2_table);

			echo "- Alter Table $tmp_table<br/>";
			ob_flush();
			flush();

			$this->alter_data_table($table,$tmp_table);

			echo "- Alter Table $tmp2_table<br/>";
			ob_flush();
			flush();
			$this->alter_data_table($table,$tmp2_table);

			if (Schema::hasTable($table)) {
				$data_table_old = DB::table($table)->get();
				if(count($data_table_old)) {
					foreach($data_table_old as $row) {
						$row = (array) $row;
						foreach($row as $col=>$val) {
							echo "--- Checking column $col in $tmp_table...<br/>";
							ob_flush();
							flush();

							if (!Schema::hasColumn($tmp_table, $col)) {
								unset($row[$col]);

								echo "---- Unset $col<br/>";
								ob_flush();
								flush();
							}
						}

						echo "-- Insert old data, id $row[id] to $tmp_table<br/>";
						ob_flush();
						flush();

						DB::table($tmp_table)->insert($row);	
					}
				}
			}

			echo "- Insert data master to $tmp2_table<br/>";
			ob_flush();
			flush();

			$this->insert_data_table($table,$tmp2_table);				

			try{
				$masterdata = DB::table($tmp2_table)->get();

				if($masterdata) {
					foreach($masterdata as $row) {
						$row = (array) $row;
						if(DB::table($tmp_table)->where('id',$row['id'])->count()==0) {
							DB::table($tmp_table)->insert($row);
							echo "-- Insert data master id $row[id] to $tmp_table<br/>";
							ob_flush();
							flush();
						}else{
							echo "-- Data master id $row[id] in $tmp_table is exists<br/>";
						}
					}
				}
				
			}catch (\Illuminate\Database\QueryException $e) {
				
			}
			

			echo "- Drop if exists $table<br/>";
			ob_flush();
			flush();		
			
			try{
				@Schema::dropIfExists($table);
			}catch (\Illuminate\Database\QueryException $e) {
				
			}				

			echo "- Rename $tmp_table to $table<br/>";
			ob_flush();
			flush();

			try{
				@Schema::rename($tmp_table,$table);	
				@Schema::dropIfExists($tmp2_table);
			}catch (\Illuminate\Database\QueryException $e) {
				
			}
			
		}



		//remove folder2
		echo 'Delete folder ControllerApi, Auth, and file Controller.php in folder Controllers<br/>';
		ob_flush();
		flush();
		@$this->rrmdir("app\Http\Controllers\ControllerApi");
		@$this->rrmdir("app\Http\Controllers\Auth");
		@unlink("app\Http\Controllers\Controller.php");


		//buat file api ulang
		$cms_apicustom = DB::table('cms_apicustom')->get();
		foreach($cms_apicustom as $c) {

			echo "- Checking is exists $controllername api<br/>";
			ob_flush();
			flush();

			$controllername = str_replace(' ','',ucwords(str_replace('_',' ',$c->permalink)));
			if(!file_exists("app\Http\Controllers\Api".$controllername."Controller.php")) {

				echo '-- Create new API File '.$controllername.'<br/>';
				ob_flush();
				flush();

				$this->create_api_controller($controllername,$c->permalink);	
			}
		}

		$filesphp = glob("app\Http\Controllers\*.php");
		foreach ($filesphp as $filename) {
			$filename = basename($filename);

			echo "- Check controller Admin<br/>";
			ob_flush();
			flush();

			//memastikan namanya benar
			if(substr($filename, 0,5)=='Admin') {
				$newfilename = str_replace(array("Admin",".php"), "", $filename);
				rename("app\Http\Controllers\\".$filename,"app\Http\Controllers\Admin".$newfilename.".php");

				echo "-- Rename $filename > $newfilename<br/>";
				ob_flush();
				flush();
				
				continue;
			}			

			echo "- Check controller API<br/>";
			ob_flush();
			flush();

			//memastikan namanya benar
			if(substr($filename, 0,3)=='Api') {
				$newfilename = str_replace(array("Api",".php"), "", $filename);
				rename("app\Http\Controllers\\".$filename,"app\Http\Controllers\Api".$newfilename.".php");

				echo "-- Rename $filename > $newfilename<br/>";
				ob_flush();
				flush();

				continue;
			}						
		}

		//Clear Logs
		DB::table('cms_logs')->delete();

		echo "<p><strong>~ Installing finished :) . You Should Rename folder Install to other folder name. ~</strong></p>";
		ob_flush();
		flush();

		ob_end_flush();

	}

	private function alter_data_table($table,$table_destination='') {
		$sql = $this->sql;
		$query = $sql['tables_alter'][$table];		
		try {
			foreach($query as $q) {
				if($table_destination) {
					$q = str_replace("`$table`","`$table_destination`",$q);
				}
				DB::select(DB::raw($q));
			}			
		}catch (\Illuminate\Database\QueryException $e) {
			
		}
	}

	private function insert_data_table($table,$table_destination='') {
		$sql = $this->sql;
		$queries = $sql['tables_insert'][$table];
		foreach($queries as $query) {
			if($table_destination) {
				$query = str_replace("`$table`","`$table_destination`",$query);
			}
			try {
				DB::select(DB::raw($query));			
			}catch (\Illuminate\Database\QueryException $e) {
				echo "failed insert data table<br/>";
			}
		}		
	}

	private function create_table($table,$rename="") {
		$sql = $this->sql;
		$query = $sql['tables_create'][$table];
		if($rename) {
			$query = str_replace("`$table`","`$rename`",$query);
		}
		try {
			DB::select(DB::raw($query));
		}catch (\Illuminate\Database\QueryException $e) {
			
		}		
	}


	private  function rrmdir($dir) { 
	   if (is_dir($dir)) { 
	     $objects = scandir($dir); 
	     foreach ($objects as $object) { 
	       if ($object != "." && $object != "..") { 
	         if (is_dir($dir."/".$object))
	           rrmdir($dir."/".$object);
	         else
	           unlink($dir."/".$object); 
	       } 
	     }
	     rmdir($dir); 
	   } 
	 }



	private function create_api_controller($controllername,$permalink) {
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
		$this->permalink = "'.$permalink.'";
	}

	public function hook_before(&$postdata) {
		//optional if you want to make any fantastic code before api execute
	}
	public function hook_after($postdata,&$result) {
		//optional if you want to make any fantastic code after api execute
	}

	public function hook_query_list(&$data) {
		
	}

	public function hook_query_detail(&$data) {
		
	}

}
';

		$php = trim($php);
		$path = "app/Http/Controllers/";
		file_put_contents($path.'Api'.$controllername.'Controller.php', $php);
	}

}
