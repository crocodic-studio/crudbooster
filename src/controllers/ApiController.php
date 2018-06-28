<?php

namespace Crocodicstudio\Crudbooster\controllers;

use Crocodicstudio\Crudbooster\controllers\ApiController\ApiHooks;
use Crocodicstudio\Crudbooster\controllers\ApiController\ExecuteApi;

class ApiController extends Controller
{
    use ApiHooks;
    var $method_type;

    var $permalink;

    var $hook_api_status;

    var $hook_api_message;

    var $validate = false;

    var $last_id_tmp = [];

    public function hook_api_status($boolean)
    {
        $this->hook_api_status = $boolean;
    }

    public function hook_api_message($message)
    {
        $this->hook_api_message = $message;
    }

    public function execute_api()
    {
        (new ExecuteApi($this))->execute();
    }

    /**
     * @param $name
     * @param $row_assign
     * @return array
     */
    /*private function handleFile($name, $row_assign)
    {
        if (! Request::hasFile($name)) {
            return;
        }
        $file = Request::file($name);
        $ext = $file->getClientOriginalExtension();

        //Create Directory Monthly
        Storage::makeDirectory(date('Y-m'));

        //Move file to storage
        $filename = md5(str_random(5)).'.'.$ext;
        if ($file->move(storage_path('app'.DIRECTORY_SEPARATOR.date('Y-m')), $filename)) {
            $v = 'uploads/'.date('Y-m').'/'.$filename;
            $row_assign[$name] = $v;
        }

        return $row_assign;
    }*/

    /**
     * @param $value
     * @param $uploads_format_candidate
     * @param $row_assign
     * @param $name
     * @return mixed
     */
   /* private function handleBase64($value, $uploads_format_candidate, $row_assign, $name)
    {
        $filedata = base64_decode($value);
        $f = finfo_open();
        $mime_type = finfo_buffer($f, $filedata, FILEINFO_MIME_TYPE);
        @$mime_type = explode('/', $mime_type);
        @$mime_type = $mime_type[1];

        if ($mime_type && in_array($mime_type, $uploads_format_candidate)) {
            Storage::makeDirectory(date('Y-m'));
            $filename = md5(str_random(5)).'.'.$mime_type;
            if (file_put_contents(storage_path('app'.DIRECTORY_SEPARATOR.date('Y-m')).'/'.$filename, $filedata)) {
                $v = 'uploads/'.date('Y-m').'/'.$filename;
                $row_assign[$name] = $v;
            }
        }

        return $row_assign;
    }*/
}




