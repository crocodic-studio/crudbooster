<?php

namespace Crocodicstudio\Crudbooster\CBCoreModule;

use Crocodicstudio\Crudbooster\Modules\ModuleGenerator\ControllerGenerator\FieldDetector;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Request;

class FileUploader
{
    public function uploadFile($name)
    {
        if (! Request::hasFile($name)) {
            return null;
        }
        $file = Request::file($name);
        $ext = $file->getClientOriginalExtension();
        $this->validateSize($file);
        $this->validateExtension($ext);
        //Create Directory Monthly
        Storage::makeDirectory(date('Y-m'));

        //Move file to storage
        $filename = md5(str_random(5)).'.'.$ext;
        $filePath = 'uploads'.DIRECTORY_SEPARATOR.date('Y-m');
        Storage::putFileAs($filePath, $file, $filename);

        return 'uploads/'.date('Y-m').'/'.$filename;
    }

    /**
     * @param $ext
     */
    private function validateExtension($ext)
    {
        if (! FieldDetector::isUploadField($ext)) {
            respondWith()->json("The filetype is not allowed!");
        }
    }

    /**
     * @param $file
     */
    private function validateSize($file)
    {
        $fileSize = $file->getClientSize() / 1024;
        if ($fileSize > cbConfig('UPLOAD_MAX_SIZE', 5000)) {
            respondWith()->json("The file size is too large!");
        }
    }
}