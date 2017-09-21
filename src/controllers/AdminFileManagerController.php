<?php namespace crocodicstudio\crudbooster\controllers;

use crocodicstudio\crudbooster\controllers\Controller;
use CRUDBooster;
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

class AdminFileManagerController extends CBController
{
    public function cbInit()
    {
    }

    public function getIndex()
    {

        $path = g('path') ? base64_decode(g('path')) : '';

        if (strpos($path, '..') || $path == '.' || strpos($path, '/.')) {
            return redirect()->route('AdminFileManagerControllerGetIndex');
        }

        $currentPath = $path ? $path : 'uploads';
        $currentPath = trim($currentPath, '/');

        $directories = Storage::directories($currentPath);
        $files = Storage::files($currentPath);

        return view('crudbooster::filemanager.index', ['files' => $files, 'directories' => $directories, 'currentPath' => $currentPath]);
    }

    public function postCreateDirectory()
    {
        $path = base64_decode(g('path'));
        $path = ($path) ?: 'uploads';
        $name = g('name');
        $name = str_slug($name, '_');
        Storage::makeDirectory($path.'/'.$name);

        return CRUDBooster::backWithMsg('The directory has been created!');
    }

    public function postUpload()
    {
        $allowedExtension = explode(',', strtolower(cbConfig('UPLOAD_TYPES')));
        $path = g('path') ? base64_decode(g('path')) : 'uploads';
        $file = Request::file('userfile');
        if (! $file) {
            return null;
        }

        $filename = $file->getClientOriginalName();
        $isAllowed = in_array($file->getClientOriginalExtension(), $allowedExtension);

        if (! $isAllowed) {
            return CRUDBooster::backWithMsg('The file '.$filename.' type is not allowed!', 'warning');
        }

        Storage::putFileAs($path, $file, $filename);

        return CRUDBooster::backWithMsg('The file '.$filename.' has been uploaded!');
    }

    public function getDeleteDirectory($dir)
    {
        $dir = base64_decode($dir);
        Storage::deleteDirectory($dir);

        return CRUDBooster::backWithMsg('The directory has been deleted!');
    }

    public function getDeleteFile($file)
    {
        $file = base64_decode($file);
        Storage::delete($file);

        return CRUDBooster::backWithMsg('The file has been deleted!');
    }
}
