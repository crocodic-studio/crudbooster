<?php namespace crocodicstudio\crudbooster\controllers;

use crocodicstudio\crudbooster\controllers\Controller;

use Storage;
use Response;

class UploadsController extends Controller {

	public function getFile($folder, $filename) {
		$path = storage_path() . DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR . $filename;

	    if(!Storage::exists($folder.DIRECTORY_SEPARATOR.$filename)) abort(404);

	    return response()->file($path);
	}
}
