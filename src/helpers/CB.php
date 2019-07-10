<?php

namespace crocodicstudio\crudbooster\helpers;

use Cache;
use crocodicstudio\crudbooster\exceptions\CBValidationException;
use DB;
use Illuminate\Validation\ValidationException;
use Image;
use Request;
use Route;
use Schema;
use Session;
use Storage;
use Validator;

class CB
{

    public function getRoleByName($roleName) {
        return $this->find("cb_roles",['name'=>$roleName]);
    }

    public function fcm() {
        return new FCM();
    }

    public function sidebar() {
        return new SidebarMenus();
    }

    public function session() {
        return new UserSession();
    }

    public function getDeveloperUrl($path = null) {
        $path = ($path)?"/".trim($path,"/"):null;
        return url(cbConfig("DEV_PATH")).$path;
    }

    public function getProfileUrl() {
        return $this->getAdminUrl()."/profile";
    }

    public function getLogoutUrl() {
        return $this->getAdminUrl()."/logout";
    }

    public function getLoginUrl() {
        return $this->getAdminUrl("login");
    }

    public function getAdminUrl($path = null) {
        $path = ($path)?"/".trim($path,"/"):null;
        return url(cbConfig("ADMIN_PATH")).$path;
    }

    public function getAppName() {
        return env("APP_NAME","CRUDBOOSTER");
    }

    /**
     * @param $value
     * @return null|string
     */
    public function uploadBase64($value)
    {
        $fileData = base64_decode($value);
        $mime_type = finfo_buffer(finfo_open(), $fileData, FILEINFO_MIME_TYPE);
        if($mime_type) {
            if($mime_type = explode('/', $mime_type)) {
                $ext = $mime_type[1];
                if($ext) {
                    $filePath = 'uploads/'.date('Y-m');
                    Storage::makeDirectory($filePath);
                    $filename = sha1(strRandom(5)).'.'.$ext;
                    if (Storage::put($filePath.'/'.$filename, $fileData)) {
                        self::resizeImage($filePath.'/'.$filename);
                        return $filePath.'/'.$filename;
                    }
                }
            }
        }
        return null;
    }

    /**
     * @param $name
     * @param bool $encrypt
     * @param int $resize_width
     * @param null $resize_height
     * @return string
     * @throws \Exception
     */
    public function uploadFile($name, $encrypt = true, $resize_width = 1024, $resize_height = null)
    {
        if (request()->hasFile($name)) {

            $file = request()->file($name);
            $ext = strtolower($file->getClientOriginalExtension());
            if(in_array($ext,cbConfig("UPLOAD_FILE_EXTENSION_ALLOWED"))) {
                $filename = slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                $file_path = 'uploads/'.date('Y-m');

                //Create Directory Monthly
                Storage::makeDirectory($file_path);

                if ($encrypt == true) {
                    $filename = sha1(strRandom(5)).'.'.$ext;
                } else {
                    $filename = slug($filename, '_').'.'.$ext;
                }

                if (Storage::putFileAs($file_path, $file, $filename)) {
                    if($resize_width || $resize_height) {
                        $this->resizeImage($file_path.'/'.$filename, $resize_width, $resize_height);
                    }
                    return $file_path.'/'.$filename;
                } else {
                    throw new \Exception("Something went wrong, file can't upload!");
                }
            }else{
                throw new \Exception("The file format is not allowed!");
            }

        } else {
            throw new \Exception("There is no file send to server!");
        }
    }

    public function resizeImage($fullFilePath, $resize_width = null, $resize_height = null, $qty = 100, $thumbQty = 75)
    {
        $images_ext = cbConfig("UPLOAD_IMAGE_EXTENSION_ALLOWED");

        $filename = basename($fullFilePath);
        $file_path = trim(str_replace($filename, '', $fullFilePath), '/');
        $ext = pathinfo($fullFilePath, PATHINFO_EXTENSION);

        if (in_array(strtolower($ext), $images_ext)) {

            if ($resize_width && $resize_height) {
                $img = Image::make(storage_path('app/'.$file_path.'/'.$filename));
                $img->fit($resize_width, $resize_height);
                $img->save(storage_path('app/'.$file_path.'/'.$filename), $qty);
            } elseif ($resize_width && ! $resize_height) {
                $img = Image::make(storage_path('app/'.$file_path.'/'.$filename));
                $img->resize($resize_width, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $img->save(storage_path('app/'.$file_path.'/'.$filename), $qty);
            } elseif (! $resize_width && $resize_height) {
                $img = Image::make(storage_path('app/'.$file_path.'/'.$filename));
                $img->resize(null, $resize_height, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $img->save(storage_path('app/'.$file_path.'/'.$filename), $qty);
            } else {
                $img = Image::make(storage_path('app/'.$file_path.'/'.$filename));
                if ($img->width() > 1300) {
                    $img->resize(1300, null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                }
                $img->save(storage_path('app/'.$file_path.'/'.$filename), $qty);
            }
        }
    }

    public function update($table, $id, $data)
    {
        DB::table($table)
            ->where($this->pk($table), $id)
            ->update($data);
    }

    public function updateCompact($table, $id, $params) {
        $data = [];
        foreach ($params as $param) {
            $data[$param] = request($param);
        }
        $this->update($table, $id, $data);
    }

    public function find($table, $id)
    {
        if (is_array($id)) {
            $first = DB::table($table);
            foreach ($id as $k => $v) {
                $first->where($k, $v);
            }

            return $first->first();
        } else {
            $pk = $this->pk($table);

            return DB::table($table)->where($pk, $id)->first();
        }
    }

    public function listAllTable()
    {
        return DB::connection()->getDoctrineSchemaManager()->listTableNames();
    }

    public function findAll($table, $condition_array = [])
    {
        return DB::table($table)->where($condition_array)->get();
    }

    public function redirectBack($message, $type = 'warning')
    {
        if (request()->ajax()) {
            return response()->json(['message' => $message, 'message_type' => $type, 'redirect_url' => $_SERVER['HTTP_REFERER']]);
        } else {
            return redirect()->back()->withInput()
                ->with(['message'=> $message, 'message_type'=> $type]);
        }
    }

    public function redirect($to, $message, $type = 'warning')
    {
        if (Request::ajax()) {
            return response()->json(['message' => $message, 'message_type' => $type, 'redirect_url' => $to]);
        } else {
            return redirect($to)->with(['message' => $message, 'message_type' => $type]);
        }
    }


    public function getCurrentMethod()
    {
        $action = str_replace("App\Http\Controllers", "", Route::currentRouteAction());
        $atloc = strpos($action, '@') + 1;
        $method = substr($action, $atloc);

        return $method;
    }

    public function stringBetween($string, $start, $end)
    {
        $string = ' '.$string;
        $ini = strpos($string, $start);
        if ($ini == 0) {
            return '';
        }
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;

        return substr($string, $ini, $len);
    }

    /**
     * @param array $rules
     * @param array $messages
     * @throws CBValidationException
     */
    public function validation($rules = [], $messages = [])
    {
        $input_arr = request()->all();

        foreach ($rules as $a => $b) {
            if (is_int($a)) {
                $rules[$b] = 'required';
            } else {
                $rules[$a] = $b;
            }
        }

        $validator = Validator::make($input_arr, $rules, $messages);
        if ($validator->fails()) {
            $message = $validator->errors()->all();
            throw new CBValidationException(implode("; ",$message));
        }
    }

    public function pk($table)
    {
        return $this->findPrimaryKey($table);
    }

    public function findPrimaryKey($table)
    {
        $pk = DB::getDoctrineSchemaManager()->listTableDetails($table)->getPrimaryKey();
        if(!$pk) {
            return null;
        }
        return $pk->getColumns()[0];
    }

    public function urlFilterColumn($key, $type, $value = '', $singleSorting = true)
    {
        $params = Request::all();
        $mainpath = trim(self::mainpath(), '/');
        $key = sanitizeXSS($key);
        $type = sanitizeXSS($type);
        $value = sanitizeXSS($value);

        if ($params['filter_column'] && $singleSorting) {
            foreach ($params['filter_column'] as $k => $filter) {
                foreach ($filter as $t => $val) {
                    if ($t == 'sorting') {
                        unset($params['filter_column'][$k]['sorting']);
                    }
                }
            }
        }

        $params['filter_column'][$key][$type] = $value;

        if (isset($params)) {
            return $mainpath.'?'.http_build_query($params);
        } else {
            return $mainpath.'?filter_column['.$key.']['.$type.']='.$value;
        }
    }


    public function getUrlParameters($exception = null)
    {
        $get = request()->all();
        $inputhtml = '';

        if ($get) {
            if (is_array($exception)) {
                foreach ($exception as $e) {
                    unset($get[$e]);
                }
            }
            $string_parameters = http_build_query($get);
            $string_parameters_array = explode('&', $string_parameters);
            foreach ($string_parameters_array as $s) {
                $part = explode('=', $s);
                if(isset($part[0]) && isset($part[1])) {
                    $name = htmlspecialchars(urldecode($part[0]));
                    $name = strip_tags($name);
                    $value = htmlspecialchars(urldecode($part[1]));
                    $value = strip_tags($value);
                    if ($name) {
                        $inputhtml .= "<input type='hidden' name='$name' value='$value'/>\n";
                    }
                }
            }
        }

        return $inputhtml;
    }


    public function routeGet($prefix, $controller) {
        $alias = str_replace("@"," ", $controller);
        $alias = ucwords($alias);
        $alias = str_replace(" ","",$alias);
        Route::get($prefix, ['uses' => $controller, 'as' => $alias]);
    }

    public function routePost($prefix, $controller) {
        $alias = str_replace("@"," ", $controller);
        $alias = ucwords($alias);
        $alias = str_replace(" ","",$alias);
        Route::post($prefix, ['uses' => $controller, 'as' => $alias]);
    }

    public function routeGroupBackend(callable $callback, $namespace = 'crocodicstudio\crudbooster\controllers') {
        Route::group([
            'middleware' => ['web', \crocodicstudio\crudbooster\middlewares\CBBackend::class],
            'prefix' => cbConfig('ADMIN_PATH'),
            'namespace' => $namespace,
        ], $callback);
    }

    /*
    | --------------------------------------------------------------------------------------------------------------
    | Alternate route for Laravel Route::controller
    | --------------------------------------------------------------------------------------------------------------
    | $prefix       = path of route
    | $controller   = controller name
    |
    */
    public function routeController($prefix, $controller)
    {

        $prefix = trim($prefix, '/').'/';

        if(substr($controller,0,1) != "\\") {
            $controller = "\App\Http\Controllers\\".$controller;
        }

        $exp = explode("\\", $controller);
        $controller_name = end($exp);

        try {
            Route::get($prefix, ['uses' => $controller.'@getIndex', 'as' => $controller_name.'GetIndex']);

            $controller_class = new \ReflectionClass($controller);
            $controller_methods = $controller_class->getMethods(\ReflectionMethod::IS_PUBLIC);
            $wildcards = '/{one?}/{two?}/{three?}/{four?}/{five?}';
            foreach ($controller_methods as $method) {

                if ($method->class != 'Illuminate\Routing\Controller' && $method->name != 'getIndex') {
                    if (substr($method->name, 0, 3) == 'get') {
                        $method_name = substr($method->name, 3);
                        $slug = array_filter(preg_split('/(?=[A-Z])/', $method_name));
                        $slug = strtolower(implode('-', $slug));
                        $slug = ($slug == 'index') ? '' : $slug;
                        Route::get($prefix.$slug.$wildcards, ['uses' => $controller.'@'.$method->name, 'as' => $controller_name.'Get'.$method_name]);
                    } elseif (substr($method->name, 0, 4) == 'post') {
                        $method_name = substr($method->name, 4);
                        $slug = array_filter(preg_split('/(?=[A-Z])/', $method_name));
                        Route::post($prefix.strtolower(implode('-', $slug)).$wildcards, [
                            'uses' => $controller.'@'.$method->name,
                            'as' => $controller_name.'Post'.$method_name,
                        ]);
                    }
                }
            }
        } catch (\Exception $e) {

        }
    }
}
