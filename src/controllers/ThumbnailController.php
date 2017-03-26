<?php namespace crocodicstudio\crudbooster\controllers;

use crocodicstudio\crudbooster\controllers\Controller;

use Storage;
use Response;
use Image;
use File;
use Request;

class ThumbnailController extends Controller {

	public function getFile($folder, $filename) {
		$path = storage_path() . DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $filename;

	    if(!Storage::exists($folder.DIRECTORY_SEPARATOR.$filename)) abort(404);

	    $w = Request::get('w')?:config('crudbooster.DEFAULT_THUMBNAIL_WIDTH',300);
	    $h = Request::get('h')?:$w;
	    
	    $is_download = Request::get('download');

	    $extension = strtolower(File::extension($path));
	    $images_ext = config('crudbooster.IMAGE_EXTENSIONS',['jpg','jpeg','png','gif','bmp']);

	    if(in_array($extension, $images_ext)) {	  	    	

	    	$img = Image::cache(function($image) use ($path,$w,$h) {
	    		$im = $image->make($path);
		    	if($w) {
		    		if(!$h) {
		    			$im->fit($w);
		    		}else{
		    			$im->fit($w,$h);
		    		}	    		
		    	}
		    	return $im;
	    	});

	    	if($is_download) {	    		
	    		$filename = (Request::get('filename'))?Request::get('filename').'.'.$extension:$filename;
	    		return Response::make($img,200,array('Content-Type'=>'image/'.$extension,'Content-Disposition'=>'attachment; filename='.$filename));				
	    	}else{
	    		return Response::make($img,200,array('Content-Type'=>'image/'.$extension));
	    	}
	    		    	
	    }else{

	    	if($is_download) {
	    		return response()->download($path);
	    	}else{
	    		return response()->file($path);
	    	}	    	
	    }	    	    
	}
}
