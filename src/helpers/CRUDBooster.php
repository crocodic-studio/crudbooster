<?php

namespace crocodicstudio\crudbooster\helpers;

use crocodicstudio\crudbooster\CBCoreModule\CbUsersRepo;
use crocodicstudio\crudbooster\Modules\LogsModule\LogsRepository;
use crocodicstudio\crudbooster\Modules\PrivilegeModule\PrivilegeHelpers;
use Session;
use Request;
use Schema;
use Cache;
use DB;
use Route;
use Config;
use Validator;

class CRUDBooster
{
    use PrivilegeHelpers;

    public static function get($table, $string_conditions = null, $orderby = null, $limit = null, $skip = null)
    {
        $table = self::parseSqlTable($table);
        $table = $table['table'];
        $query = DB::table($table);
        if ($string_conditions) {
            $query->whereraw($string_conditions);
        }
        if ($orderby) {
            $query->orderbyraw($orderby);
        }
        if ($limit) {
            $query->take($limit);
        }
        if ($skip) {
            $query->skip($skip);
        }

        return $query->get();
    }

    public static function parseSqlTable($table)
    {
        $f = explode('.', $table);

        if (count($f) == 1) {
            return ["table" => $f[0], "database" => cbConfig('MAIN_DB_DATABASE')];
        }
        if (count($f) == 2) {
            return ["database" => $f[0], "table" => $f[1]];
        }

        if (count($f) == 3) {
            return ["table" => $f[0], "schema" => $f[1], "table" => $f[2]];
        }

        return false;
    }

    public static function me()
    {
        return CbUsersRepo::find(session('admin_id'));
    }

    public static function myName()
    {
        return session('admin_name');
    }

    public static function myPhoto()
    {
        return session('admin_photo');
    }

    public static function isLocked()
    {
        return session('admin_lock');
    }

    public static function getCurrentModule()
    {
        return GetCurrentX::getCurrentModule();
    }

    public static function getCurrentMenuId()
    {
        return GetCurrentX::getCurrentMenuId();
    }

    public static function adminPath($path = null)
    {
        return url(cbAdminPath().'/'.$path);
    }

    public static function deleteConfirm($redirectTo)
    {
        echo 'swal({   
				title: "'.cbTrans('delete_title_confirm').'",   
				text: "'.cbTrans('delete_description_confirm').'",   
				type: "warning",   
				showCancelButton: true,   
				confirmButtonColor: "#ff0000",   
				confirmButtonText: "'.cbTrans('confirmation_yes').'",  
				cancelButtonText: "'.cbTrans('confirmation_no').'",  
				closeOnConfirm: false }, 
				function(){  location.href="'.$redirectTo.'" });';
    }

    public static function getCurrentId()
    {
        return GetCurrentX::getCurrentId();
    }

    public static function getCurrentMethod()
    {
        return GetCurrentX::getCurrentMethod();
    }

    public static function isColumnNULL($table, $field)
    {
        return DbInspector::isColNull($table, $field);
    }

    public static function getValueFilter($field)
    {
        $filter = request('filter_column');
        if ($filter[$field]) {
            return $filter[$field]['value'];
        }
    }

    private static function getFilter($field, $index)
    {
        $filter = request('filter_column');
        if ($filter[$field]) {
            return $filter[$field][$index];
        }
    }

    public static function getSortingFilter($field)
    {
        return self::getFilter($field, 'sorting');
    }

    public static function getTypeFilter($field)
    {
        return self::getFilter($field, 'type');
    }

    public static function stringBetween($string, $start, $end)
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

    public static function first($table, $id)
    {
        $table = self::parseSqlTable($table)['table'];
        if (! is_array($id)) {
            $pk = self::pk($table);

            return DB::table($table)->where($pk, $id)->first();
        }

        $first = DB::table($table);
        foreach ($id as $k => $v) {
            $first->where($k, $v);
        }

        return $first->first();
    }

    public static function pk($table)
    {
        return DbInspector::findPK($table);
    }

    public static function valid($rules = [], $type = 'json')
    {
        $validator = Validator::make(request()->all(), $rules);

        if (!$validator->fails()) {
            return true;
        }

        $message = $validator->errors()->all();

        if ($type == 'json') {
            $result = [];
            $result['api_status'] = 0;
            $result['api_message'] = implode(', ', $message);
            sendAndTerminate(response()->json($result, 200));
        }

        $res = redirect()->back()->with(['message' => implode('<br/>', $message), 'message_type' => 'warning'])->withInput();
        sendAndTerminate($res);
    }

    public static function getForeignKey($parent_table, $child_table)
    {
        $parent_table = CRUDBooster::parseSqlTable($parent_table)['table'];
        $child_table = CRUDBooster::parseSqlTable($child_table)['table'];

        if (\Schema::hasColumn($child_table, 'id_'.$parent_table)) {
            return 'id_'.$parent_table;
        }
        return $parent_table.'_id';
    }

    public static function getTableForeignKey($fieldName)
    {
        if (substr($fieldName, 0, 3) == 'id_' || substr($fieldName, -3) == '_id') {
            return str_replace(['_id', 'id_'], '', $fieldName);
        }
    }

    public static function isForeignKey($fieldName)
    {
        return DbInspector::isForeignKeey($fieldName);
    }

    public static function urlFilterColumn($key, $type, $value = '', $singleSorting = true)
    {
        $params = request()->all();
        $mainpath = trim(self::mainpath(), '/');

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
        }
        return $mainpath.'?filter_column['.$key.']['.$type.']='.$value;

    }

    public static function mainpath($path = null)
    {
        $controllerName = strtok(Route::currentRouteAction(), '@');
        // $controllerName = array_pop(explode('\\', $controllerName));
        $controllerName = basename($controllerName);
        $route_url = route($controllerName.'GetIndex');

        if (! $path) {
            return trim($route_url, '/');
        }

        if (substr($path, 0, 1) == '?') {
            return trim($route_url, '/').$path;
        }

        return $route_url.'/'.$path;
    }

    public static function insertLog($description)
    {
        LogsRepository::insertLog('crudbooster: '.$description, self::myId());
    }

    public static function insertTryLog($action, $name = '')
    {
        self::insertLog(trans("logging.log_try_".$action, ['name' => $name, 'module' => self::getCurrentModule()]));
    }

    public static function myId()
    {
        return session('admin_id');
    }

    public static function referer()
    {
        return Request::server('HTTP_REFERER');
    }

    public static function listTables()
    {
        return DbInspector::listTables();
    }

    public static function listCbTables()
    {
        $tablesList = [];
        foreach (self::listTables() as $tableObj) {

            $tableName = $tableObj->TABLE_NAME;
            if ($tableName == config('database.migrations')) {
                continue;
            }
            if (substr($tableName, 0, 4) == 'cms_' && $tableName != 'cms_users') {
                continue;
            }

            $tablesList[] = $tableName;
        }

        return $tablesList;
    }

    public static function getUrlParameters($exception = null)
    {
        @$get = $_GET;
        $inputhtml = '';

        if (! $get) {
            return $inputhtml;
        }
        if (is_array($exception)) {
            foreach ($exception as $e) {
                unset($get[$e]);
            }
        }

        $string_parameters = http_build_query($get);
        foreach (explode('&', $string_parameters) as $s) {
            $part = explode('=', $s);
            $name = urldecode($part[0]);
            $value = urldecode($part[1]);
            $inputhtml .= "<input type='hidden' name='$name' value='$value'/>";
        }

        return $inputhtml;
    }

    public static function isExistsController($table)
    {
        $ctrlName = ucwords(str_replace('_', ' ', $table));
        $ctrlName = str_replace(' ', '', $ctrlName).'Controller.php';
        $path = base_path(controllers_dir());
        $path2 = base_path(controllers_dir()."ControllerMaster/");

        if (file_exists($path.'Admin'.$ctrlName) || file_exists($path2.'Admin'.$ctrlName) || file_exists($path2.$ctrlName)) {
            return true;
        }

        return false;
    }

    public static function getTableColumns($table)
    {
        return DbInspector::getTableCols($table);
    }

    public static function getNameTable($columns)
    {
        return DbInspector::colName($columns);
    }

    public static function routeController($prefix, $controller, $namespace = null)
    {
        $prefix = trim($prefix, '/').'/';

        $namespace = ($namespace) ?: ctrlNamespace();

        try {
            Route::get($prefix, ['uses' => $controller.'@getIndex', 'as' => $controller.'GetIndex']);

            $controller_class = new \ReflectionClass($namespace.'\\'.$controller);
            $controller_methods = $controller_class->getMethods(\ReflectionMethod::IS_PUBLIC);
            $wildcards = '/{one?}/{two?}/{three?}/{four?}/{five?}';
            foreach ($controller_methods as $method) {

                if ($method->class == 'Illuminate\Routing\Controller' || $method->name == 'getIndex') {
                    continue;
                }
                if (substr($method->name, 0, 3) == 'get') {
                    $method_name = substr($method->name, 3);
                    $slug = array_filter(preg_split('/(?=[A-Z])/', $method_name));
                    $slug = strtolower(implode('-', $slug));
                    $slug = ($slug == 'index') ? '' : $slug;
                    Route::get($prefix.$slug.$wildcards, ['uses' => $controller.'@'.$method->name, 'as' => $controller.'Get'.$method_name]);
                } elseif (substr($method->name, 0, 4) == 'post') {
                    $method_name = substr($method->name, 4);
                    $slug = array_filter(preg_split('/(?=[A-Z])/', $method_name));
                    Route::post($prefix.strtolower(implode('-', $slug)).$wildcards, [
                            'uses' => $controller.'@'.$method->name,
                            'as' => $controller.'Post'.$method_name,
                        ]);
                }
            }
        } catch (\Exception $e) {

        }
    }

    /*
    | -------------------------------------------------------------
    | Alternate route for Laravel Route::controller
    | -------------------------------------------------------------
    | $prefix       = path of route
    | $controller   = controller name
    | $namespace    = namespace of controller (optional)
    |
    */

    public static function denyAccess()
    {
        static::redirect(static::adminPath(), cbTrans('denied_access'));
    }

    public static function redirect($to, $message, $type = 'warning')
    {
        if (Request::ajax()) {
            sendAndTerminate(response()->json(['message' => $message, 'message_type' => $type, 'redirect_url' => $to]));
        }

        sendAndTerminate(redirect($to)->with(['message' => $message, 'message_type' => $type]));
    }

    public static function icon($icon)
    {
        return '<i class=\'fa fa-'.$icon.'\'></i>';
    }

    public static function componentsPath($type = '')
    {
        $componentPath = implode(DIRECTORY_SEPARATOR, ['vendor', 'crocodicstudio', 'crudbooster', 'src', 'views', 'default', 'type_components', $type]);
        return base_path($componentPath);

    }

    public static function PublishedComponentsPath($type = '')
    {
        $Path = implode(DIRECTORY_SEPARATOR, ['views', 'vendor', 'crudbooster', 'type_components', $type]);
        return resource_path($Path);
    }
}
