<?php namespace crocodicstudio\crudbooster\controllers;

use crocodicstudio\crudbooster\controllers\Controller;

use Storage;
use Response;
use Image;
use File;
use Request;

class UploadsController extends Controller {

	public function getFile($folder, $filename) {
		$path = storage_path() . DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $filename;

	    if(!Storage::exists($folder.DIRECTORY_SEPARATOR.$filename)) abort(404);

	    $w = Request::get('w');
	    $h = Request::get('h');
	    $h = ($h)?:$w;

	    $extension = File::extension($path);
	    $images_ext = array('jpg','jpeg','png','gif','bmp');

	    if(in_array($extension, $images_ext)) {	  
	    	header('Content-Type: image/'.$extension);  
	    	$img = Image::make($path);
	    	if($w) {
	    		if(!$h) {
	    			$img->fit($w);
	    		}else{
	    			$img->fit($w,$h);
	    		}	    		
	    	}
	    	return $img->response();
	    }else{
	    	return response()->file($path);
	    }	    	    
	}
}
