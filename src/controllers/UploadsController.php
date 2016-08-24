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

class UploadsController extends Controller {

	public function getFile($folder, $filename) {
		$path = storage_path() . DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $filename;

	    if(!File::exists($path)) abort(404);

	    $file             = File::get($path);
	    $type             = File::mimeType($path);
	    $response         = Response::make($file, 200);
	    $response->header("Content-Type", $type);

	    $seconds_to_cache = 3600 * (24*30);
	    $gmt_mtime        = gmdate('D, d M Y H:i:s', time() ) . ' GMT';
		$ts               = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache) . " GMT";		
		$response->header("Expires",$ts);
		$response->header("Last-Modified",$gmt_mtime);
		$response->header("Pragma","cache");
		$response->header("Cache-Control","max-age=$seconds_to_cache, must-revalidate");
	    return $response;
	}
}
