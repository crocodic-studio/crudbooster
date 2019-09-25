<?php

namespace crocodicstudio\crudbooster\helpers;

use Illuminate\Http\File;
use Illuminate\Support\Facades\Cache;
use crocodicstudio\crudbooster\exceptions\CBValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Facades\Image;
use Request;
use Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Validator;

class CB
{

    public function htmlHelper() {
        return (new HTMLHelper());
    }

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

    public function getDeveloperPath($path = null) {
        $path = ($path)?"/".trim($path,"/"):null;
        return "developer/".getSetting("developer_path").$path;
    }

    public function getDeveloperUrl($path = null) {
        return url($this->getDeveloperPath($path));
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

    public function getAdminPath() {
        return getSetting("ADMIN_PATH","admin");
    }

    public function getAdminUrl($path = null) {
        $path = ($path)?"/".trim($path,"/"):null;
        return url($this->getAdminPath()).$path;
    }

    public function getAppName() {
        return getSetting("APP_NAME", env("APP_NAME","CRUDBOOSTER"));
    }

    /**
     * @param $filename
     * @param $extension
     * @param $file
     * @param null $resize_width
     * @param null $resize_height
     * @return string
     * @throws \Exception
     */
    private function uploadFileProcess($filename, $extension, $file, $encrypt = true, $resize_width = null, $resize_height = null)
    {
        if(in_array($extension,cbConfig("UPLOAD_FILE_EXTENSION_ALLOWED"))) {
            $file_path = cbConfig("UPLOAD_PATH_FORMAT");
            $file_path = str_replace("{Y}",date('Y'), $file_path);
            $file_path = str_replace("{m}", date('m'), $file_path);
            $file_path = str_replace("{d}", date("d"), $file_path);

            //Create Directory Base On Template
            Storage::makeDirectory($file_path);
            Storage::put($file_path."/index.html","&nbsp;","public");
            Storage::put($file_path."/.gitignore","!.gitignore","public");

            if ($encrypt == true) {
                $filename = md5(strRandom(5)).'.'.$extension;
            } else {
                $filename = slug($filename, '_').'.'.$extension;
            }

            if($resize_width || $resize_height) {
                $this->resizeImage($file, $file_path.'/'.$filename, $resize_width, $resize_height);
                return $file_path.'/'.$filename;
            }else{
                if (Storage::putFileAs($file_path, $file, $filename, 'public')) {
                    return $file_path.'/'.$filename;
                } else {
                    throw new \Exception("Something went wrong, file can't upload!");
                }
            }
        }else{
            throw new \Exception("The file format is not allowed!");
        }
    }

    /**
     * @param $base64_value
     * @param bool $encrypt
     * @param null $resize_width
     * @param null $resize_height
     * @throws \Exception
     */
    public function uploadBase64($filename, $base64_value, $encrypt = true, $resize_width = null, $resize_height = null)
    {
        $fileData = base64_decode($base64_value);
        $mime_type = finfo_buffer(finfo_open(), $fileData, FILEINFO_MIME_TYPE);
        if($mime_type) {
            if($mime_type = explode('/', $mime_type)) {
                $ext = $mime_type[1];
                if($filename && $ext) {
                    return $this->uploadFileProcess($filename, $ext, $fileData, $encrypt, $resize_width, $resize_height);
                }
            }else {
                throw new \Exception("Mime type not found");
            }
        }else{
            throw new \Exception("Mime type not found");
        }
    }

    /**
     * @param $name
     * @param bool $encrypt
     * @param int $resize_width
     * @param null $resize_height
     * @return string
     * @throws \Exception
     */
    public function uploadFile($name, $encrypt = true, $resize_width = null, $resize_height = null)
    {
        if (request()->hasFile($name)) {
            $file = request()->file($name);
            $filename = $file->getClientOriginalName();
            $ext = strtolower($file->getClientOriginalExtension());

            if($filename && $ext) {
                return $this->uploadFileProcess($filename, $ext, $file, $encrypt, $resize_width, $resize_height);
            }

        } else {
            throw new \Exception("There is no file send to server!");
        }
    }

    /**
     * @param $file
     * @param $fullFilePath
     * @param null $resize_width
     * @param null $resize_height
     * @param int $qty
     * @param int $thumbQty
     * @throws \Exception
     */
    public function resizeImage($file, $fullFilePath, $resize_width = null, $resize_height = null, $qty = 100, $thumbQty = 75)
    {
        $images_ext = cbConfig("UPLOAD_IMAGE_EXTENSION_ALLOWED");

        $filename = basename($fullFilePath);
        $file_path = trim(str_replace($filename, '', $fullFilePath), '/');
        $ext = pathinfo($fullFilePath, PATHINFO_EXTENSION);

        if (in_array(strtolower($ext), $images_ext)) {

            // Upload file
            $img = Image::make($file);
            $img->encode($ext, $qty);

            if ($resize_width && $resize_height) {
                $img->fit($resize_width, $resize_height);

            } elseif ($resize_width && ! $resize_height) {

                $img->resize($resize_width, null, function ($constraint) {
                    $constraint->aspectRatio();
                });

            } elseif (! $resize_width && $resize_height) {

                $img->resize(null, $resize_height, function ($constraint) {
                    $constraint->aspectRatio();
                });

            } else {

                if ($img->width() > cbConfig("DEFAULT_IMAGE_MAX_WIDTH_RES")) {
                    $img->resize(cbConfig("DEFAULT_IMAGE_MAX_WIDTH_RES"), null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                }
            }

            Storage::put($fullFilePath, $img, 'public');
        }else{
            throw new \Exception("The file format is not allowed!");
        }
    }

    /**
     * @param $table
     * @param $id
     * @param $data
     */
    public function update($table, $id, $data)
    {
        DB::table($table)
            ->where($this->pk($table), $id)
            ->update($data);
    }

    /**
     * @param $table
     * @param $id
     * @param $params
     */
    public function updateCompact($table, $id, $params) {
        $data = [];
        foreach ($params as $param) {
            $data[$param] = request($param);
        }
        $this->update($table, $id, $data);
    }

    /**
     * @param $table
     * @param $id
     */
    public function delete($table, $id)
    {
        DB::table($table)->where($this->pk($table), $id)->delete();
    }

    /**
     * @param $table
     * @param $id
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Query\Builder|mixed|null|object
     */
    public function find($table, $id)
    {
        if (is_array($id)) {
            $idHash = md5("find".$table.serialize($id));
            if(miscellanousSingleton()->hasData($idHash)) return miscellanousSingleton()->getData($idHash);

            $first = DB::table($table);
            foreach ($id as $k => $v) {
                $first->where($k, $v);
            }

            $data = $first->first();
            miscellanousSingleton()->setData($idHash,$data);
            return $data;
        } else {
            $idHash = md5("find".$table.$id);
            if(miscellanousSingleton()->hasData($idHash)) return miscellanousSingleton()->getData($idHash);

            $pk = $this->pk($table);
            $data = DB::table($table)->where($pk, $id)->first();
            miscellanousSingleton()->setData($idHash,$data);
            return $data;
        }
    }

    /**
     * @param $table
     * @param callable|string|null $conditionOrCallback
     * @return \Illuminate\Support\Collection|mixed
     */
    public function findAll($table, $conditionOrCallback = null)
    {
        $data = [];
        $idHash = null;

        if(is_array($conditionOrCallback)) {
            $idHash = md5("findAll".$table.serialize($conditionOrCallback));
            if(miscellanousSingleton()->hasData($idHash)) return miscellanousSingleton()->getData($idHash);

            $data = DB::table($table)->where($conditionOrCallback)->get();
        } elseif (is_callable($conditionOrCallback)) {
            $idHash = "findAll".$table.spl_object_hash($conditionOrCallback);
            if(miscellanousSingleton()->hasData($idHash)) return miscellanousSingleton()->getData($idHash);

            $data = DB::table($table);
            $data = call_user_func($conditionOrCallback, $data);
            $data = $data->get();
        } else {
            $idHash = md5("findAll".$table.$conditionOrCallback);
            if(miscellanousSingleton()->hasData($idHash)) return miscellanousSingleton()->getData($idHash);

            $data = DB::table($table);
            if($conditionOrCallback) {
                $data = $data->whereRaw($conditionOrCallback);
            }
            $data = $data->get();
        }

        if($idHash && $data) {
            miscellanousSingleton()->setData($idHash, $data);
        }

        return $data;
    }

    public function listAllTable()
    {
        $idHash = md5("listAllTable");
        if(miscellanousSingleton()->hasData($idHash)) return miscellanousSingleton()->getData($idHash);
        $data = DB::connection()->getDoctrineSchemaManager()->listTableNames();
        miscellanousSingleton()->setData($idHash, $data);
        return $data;
    }

    public function listAllColumns($table)
    {
        $idHash = md5("listAllColumns".$table);
        if(miscellanousSingleton()->hasData($idHash)) return miscellanousSingleton()->getData($idHash);
        $data = Schema::getColumnListing($table);
        miscellanousSingleton()->setData($idHash, $data);
        return $data;
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
            'prefix' => cb()->getAdminPath(),
            'namespace' => $namespace,
        ], $callback);
    }

    public function routeGroupDeveloper(callable $callback, $namespace = 'crocodicstudio\crudbooster\controllers') {
        Route::group([
            'middleware' => ['web', \crocodicstudio\crudbooster\middlewares\CBDeveloper::class],
            'prefix' => "developer/".getSetting('developer_path'),
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
