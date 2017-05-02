<?php

namespace Crocodicstudio\Crudbooster\Http\Controllers;

use Crocodicstudio\Crudbooster\Http\Controllers\Controller;
use Storage;
use Response;
use Image;
use File;
use Request;

class CBThumbnailController extends Controller
{
	public function getFile($folder, $fileName)
	{
		$path = storage_path() . DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $fileName;

	    if(!Storage::exists($folder.DIRECTORY_SEPARATOR.$fileName)) abort(404);

	    $w = Request::get('w')?:config('crudbooster.default_thumbnail_width',300);
	    $h = Request::get('h')?:$w;
	    
	    $isDownload = Request::get('download');

	    $extension = strtolower(File::extension($path));
	    $imageExtension = config('crudbooster.image_extensions');
	    $imageExtension = explode($imageExtension);

	    if (in_array($extension, $imageExtension)) {

	    	$img = Image::cache(function($image) use ($path,$w,$h) {
	    		$im = $image->make($path);
		    	if ($w) {
		    		if (!$h) {
		    			$im->fit($w);
		    		} else {
		    			$im->fit($w,$h);
		    		}
		    	}
		    	return $im;
	    	});

	    	if ($isDownload) {
	    		$fileName = (Request::get('fileName'))?Request::get('fileName').'.'.$extension:$fileName;
	    		return Response::make($img,200,array('Content-Type'=>'image/'.$extension,'Content-Disposition'=>'attachment; filename='.$fileName));
	    	} else {
	    		return Response::make($img,200,array('Content-Type'=>'image/'.$extension));
	    	}

	    } else {

	    	if ($isDownload) {
	    		return response()->download($path);
	    	} else {
	    		return response()->file($path);
	    	}
	    }
	}
}
