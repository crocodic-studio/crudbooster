<?php 
namespace App\Http\Controllers;
error_reporting(E_ALL ^ E_NOTICE);

use Session;
use Request;
use DB;
use App;
use Cache;
use Route;

class FileHandelController extends Controller {

	public function getFiles($md5) {
		$id_md5 = strtok($md5,'.');
		if(Cache::has('image_'.$id_md5)) {
			$file = Cache::get('image_'.$id_md5);
		}else{
			$row = DB::table('cms_filemanager')->where('id_md5',$id_md5)->first();
			$file = $row->filedata;
			Cache::forever('image_'.$id_md5,$file);
		}		
		header('Cache-Control: max-age=86400, public');
		header("Content-Type: ".$row->content_type);
		header("Content-Transfer-Encoding: binary");
		header('Content-Length: '. strlen($file));
		echo $file;
		exit;
	}
}
