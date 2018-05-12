<?php

namespace crocodicstudio\crudbooster\Modules\FileManagerModule;

use crocodicstudio\crudbooster\controllers\CBController;
use crocodicstudio\crudbooster\helpers\CRUDBooster;
use crocodicstudio\crudbooster\Modules\ModuleGenerator\ControllerGenerator\FieldDetector;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Request;

class AdminFileManagerController extends CBController
{
    public function cbInit()
    {
    }

    public function getIndex()
    {
        $path = request('path') ? base64_decode(request('path')) : '';

        if (strpos($path, '..') || $path == '.' || strpos($path, '/.')) {
            return redirect()->route('AdminFileManagerControllerGetIndex');
        }

        $currentPath = $path ?: 'uploads';
        $currentPath = trim($currentPath, '/');

        $directories = Storage::directories($currentPath);
        $files = Storage::files($currentPath);

        return view('CbFileManager::index', ['files' => $files, 'directories' => $directories, 'currentPath' => $currentPath]);
    }

    public function postCreateDirectory()
    {
        $path = (base64_decode(request('path'))) ?: 'uploads';
        $name = str_slug(request('name'), '_');
        Storage::makeDirectory($path.'/'.$name);

        backWithMsg('The directory has been created!');
    }

    public function postUpload()
    {
        $path = request('path') ? base64_decode(request('path')) : 'uploads';
        $file = Request::file('userfile');
        if (! $file) {
            return null;
        }

        $fileName = $file->getClientOriginalName();

        if (! FieldDetector::isUploadField($file->getClientOriginalExtension())) {
            backWithMsg('The file '.$fileName.' type is not allowed!', 'warning');
        }

        Storage::putFileAs($path, $file, $fileName);

        backWithMsg('The file '.$fileName.' has been uploaded!');
    }

    public function getDeleteDirectory($dir)
    {
        $dir = base64_decode($dir);
        Storage::deleteDirectory($dir);

        backWithMsg('The directory has been deleted!');
    }

    public function getDeleteFile($file)
    {
        $file = base64_decode($file);
        if(Storage::delete($file))
            backWithMsg('The file has been deleted!');

        backWithMsg('The file did not deleted!', 'warning');
    }
}
