<?php

namespace crocodicstudio\crudbooster\CBCoreModule;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Request;

class FileUploader
{
    function uploadFile($name)
    {
        if (! Request::hasFile($name)) {
            return null;
        }
        $file = Request::file($name);
        $ext = $file->getClientOriginalExtension();
        $filesize = $file->getClientSize() / 1024;
        if ($filesize > cbConfig('UPLOAD_MAX_SIZE', 5000)) {
            echo "The filesize is too large!";
            exit;
        }
        if (! in_array($ext, explode(',', cbConfig('UPLOAD_TYPES')))) {
            echo "The filetype is not allowed!";
            exit;
        }
        //Create Directory Monthly
        Storage::makeDirectory(date('Y-m'));

        //Move file to storage
        $filename = md5(str_random(5)).'.'.$ext;
        $file_path = 'uploads'.DIRECTORY_SEPARATOR.date('Y-m');
        Storage::putFileAs($file_path, $file, $filename);

        return 'uploads/'.date('Y-m').'/'.$filename;
    }
}