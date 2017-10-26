<?php namespace crocodicstudio\crudbooster\controllers;

use crocodicstudio\crudbooster\controllers\Controller;
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
use CRUDBooster;

class ApiController extends Controller
{
    var $method_type;

    var $permalink;

    var $hook_api_status;

    var $hook_api_message;

    var $validate = false;

    var $last_id_tmp = [];

    public function hook_before(&$postdata)
    {

    }

    public function hook_after($postdata, &$result)
    {

    }

    public function hook_validate(&$postdata)
    {

    }

    public function hook_query(&$query)
    {

    }

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

        // DB::enableQueryLog();

        $posts = Request::all();
        $posts_keys = array_keys($posts);
        $posts_values = array_values($posts);

        $row_api = DB::table('cms_apicustom')->where('permalink', $this->permalink)->first();

        $action_type = $row_api->aksi;
        $table = $row_api->tabel;

        $debug_mode_message = 'You are in debug mode !';

        /* 
        | ----------------------------------------------
        | Do some custome pre-checking for posted data, if failed discard API execution
        | ----------------------------------------------
        |
        */
        $this->hook_validate($posts);
        if ($this->validate) { // hook have to return true
            $result['api_status'] = 0;
            $result['api_message'] = "Failed to execute API !";
            goto show;
        }

        /* 
        | ----------------------------------------------
        | Method Type validation
        | ----------------------------------------------
        |
        */
        if ($row_api->method_type) {
            $method_type = $row_api->method_type;
            if ($method_type && ! Request::isMethod($method_type)) {
                $result['api_status'] = 0;
                $result['api_message'] = "The request method is not allowed !";
                goto show;
            }

            /* 
            | ----------------------------------------------
            | Check the row is exists or not
            | ----------------------------------------------
            |
            */
            if (! $row_api) {
                $result['api_status'] = 0;
                $result['api_message'] = 'Sorry this API is no longer available, maybe has changed by admin, or please make sure api url is correct.';
                goto show;
            }

            @$parameters = unserialize($row_api->parameters);
            @$responses = unserialize($row_api->responses);

            /* 
            | ----------------------------------------------
            | User Data Validation
            | ----------------------------------------------
            |
            */
            if ($parameters) {
                $type_except = ['password', 'ref', 'base64_file', 'custom', 'search'];
                $input_validator = [];
                $data_validation = [];

                foreach ($parameters as $param) {
                    $name = $param['name'];
                    $value = $posts[$name];
                    $used = $param['used'];

                    if ($used == 0) {
                        continue;
                    }
                    if ($param['config'] && substr($param['config'], 0, 1) != '*') {
                        continue;
                    }

                    $input_validator[$name] = $value;
                    $data_validation[$name] = $this->makeValidationRules($param, $type_except, $table);
                }

                $validator = Validator::make($input_validator, $data_validation);
                if ($validator->fails()) {
                    $message = $validator->errors()->all();
                    $message = implode(', ', $message);
                    $result['api_status'] = 0;
                    $result['api_message'] = $message;
                    goto show;
                }
            }

            $responses_fields = [];
            foreach ($responses as $r) {
                if ($r['used']) {
                    $responses_fields[] = $r['name'];
                }
            }

            $this->hook_before($posts);

            $limit = ($posts['limit']) ?: 20;
            $offset = ($posts['offset']) ?: 0;
            $orderby = ($posts['orderby']) ?: $table.'.id,desc';
            $uploads_format_candidate = explode(',', cbConfig("UPLOAD_TYPES"));
            $uploads_candidate = explode(',', cbConfig('IMAGE_FIELDS_CANDIDATE'));
            $password_candidate = explode(',', cbConfig('PASSWORD_FIELDS_CANDIDATE'));
            $asset = asset('/');

            unset($posts['limit'], $posts['offset'], $posts['orderby']);

            if (in_array($action_type, ['list', 'detail', 'delete'])) {
                $name_tmp = [];
                $data = DB::table($table);
                $data->skip($offset);
                $data->take($limit);

                foreach ($responses as $resp) {
                    $name = $resp['name'];
                    $type = $resp['type'];
                    $subquery = $resp['subquery'];
                    $used = intval($resp['used']);

                    if ($used == 0 && ! CRUDBooster::isForeignKey($name)) {
                        continue;
                    }

                    if (in_array($name, $name_tmp)) {
                        continue;
                    }

                    if ($name == 'ref_id') {
                        continue;
                    }

                    if ($type == 'custom') {
                        continue;
                    }

                    if ($subquery) {
                        $data->addSelect(DB::raw('('.$subquery.') as '.$name));
                        $name_tmp[] = $name;
                        continue;
                    }

                    if ($used) {
                        $data->addSelect($table.'.'.$name);
                    }

                    $name_tmp[] = $name;
                    if (CRUDBooster::isForeignKey($name)) {
                        $jointable = CRUDBooster::getTableForeignKey($name);
                        $jointable_field = CRUDBooster::getTableColumns($jointable);

                        $data->leftjoin($jointable, $jointable.'.id', '=', $table.'.'.$name);
                        foreach ($jointable_field as $jf) {
                            $jf_alias = $jointable.'_'.$jf;
                            if (in_array($jf_alias, $responses_fields)) {
                                $data->addselect($jointable.'.'.$jf.' as '.$jf_alias);
                                $name_tmp[] = $jf_alias;
                            }
                        }
                    }
                } //End Responses

                foreach ($parameters as $param) {
                    $name = $param['name'];
                    $type = $param['type'];
                    $value = $posts[$name];
                    $used = $param['used'];
                    $required = $param['required'];
                    $config = $param['config'];

                    if ($type == 'password') {
                        $data->addselect($table.'.'.$name);
                    }

                    if ($type !== 'search') {
                        continue;
                    }
                    $search_in = explode(',', $config);

                    if ($required == '1') {
                        $data->where(function ($w) use ($search_in, $value) {
                            foreach ($search_in as $k => $field) {
                                if ($k == 0) {
                                    $w->where($field, "like", "%$value%");
                                } else {
                                    $w->orWhere($field, "like", "%$value%");
                                }
                            }
                        });
                        continue;
                    }
                    if ($used && $value) {
                        $data->where(function ($w) use ($search_in, $value) {
                            foreach ($search_in as $k => $field) {
                                if ($k == 0) {
                                    $w->where($field, "like", "%$value%");
                                } else {
                                    $w->orWhere($field, "like", "%$value%");
                                }
                            }
                        });
                    }
                }

                if (CRUDBooster::isColumnExists($table, 'deleted_at')) {
                    $data->where($table.'.deleted_at', null);
                }

                $data->where(function ($w) use ($parameters, $posts, $table, $type_except) {
                    foreach ($parameters as $param) {
                        $name = $param['name'];
                        $type = $param['type'];
                        $value = $posts[$name];
                        $used = $param['used'];
                        $required = $param['required'];

                        if (in_array($type, $type_except)) {
                            continue;
                        }

                        if ($param['config'] != '' && substr($param['config'], 0, 1) != '*') {
                            $value = $param['config'];
                        }

                        if ($required == '1') {
                            if (CRUDBooster::isColumnExists($table, $name)) {
                                $w->where($table.'.'.$name, $value);
                            } else {
                                $w->having($name, '=', $value);
                            }
                        } else {
                            if ($used && $value) {
                                if (CRUDBooster::isColumnExists($table, $name)) {
                                    $w->where($table.'.'.$name, $value);
                                } else {
                                    $w->having($name, '=', $value);
                                }
                            }
                        }
                    }
                });

                //IF SQL WHERE IS NOT NULL
                if ($row_api->sql_where) {
                    $data->whereraw($row_api->sql_where);
                }

                $this->hook_query($data);

                if ($action_type == 'list') {
                    $orderby_col = $table.'.id';
                    $orderby_val = 'desc';

                    if ($orderby) {
                        $orderby_raw = explode(',', $orderby);
                        $orderby_col = $orderby_raw[0];
                        $orderby_val = $orderby_raw[1];
                    }

                    $rows = $data->orderby($orderby_col, $orderby_val)->get();

                    $result['api_status'] = 0;
                    $result['api_message'] = 'There is no data found !';
                    if (CRUDBooster::getSetting('api_debug_mode') == 'true') {
                        $result['api_authorization'] = $debug_mode_message;
                    }
                    $result['data'] = [];

                    if ($rows) {

                        foreach ($rows as &$row) {
                            foreach ($row as $k => $v) {
                                $ext = \File::extension($v);
                                if (in_array($ext, $uploads_format_candidate)) {
                                    $row->$k = asset($v);
                                }

                                if (! in_array($k, $responses_fields)) {
                                    unset($row[$k]);
                                }
                            }
                        }

                        $result['api_status'] = 1;
                        $result['api_message'] = 'success';
                        if (CRUDBooster::getSetting('api_debug_mode') == 'true') {
                            $result['api_authorization'] = $debug_mode_message;
                        }
                        $result['data'] = $rows;
                    }
                }

                if ($action_type == 'detail') {


                    $result['api_status'] = 0;
                    $result['api_message'] = 'There is no data found !';

                    if (CRUDBooster::getSetting('api_debug_mode') == 'true') {
                        $result['api_authorization'] = $debug_mode_message;
                    }

                    $rows = $data->first();

                    if ($rows) {
                        foreach ($parameters as $param) {
                            $name = $param['name'];
                            $type = $param['type'];
                            $value = $posts[$name];
                            $used = $param['used'];
                            $required = $param['required'];

                            if ($param['config'] != '' && substr($param['config'], 0, 1) != '*') {
                                $value = $param['config'];
                            }

                            if ($required && $type == 'password' && ! Hash::check($value, $rows->{$name})) {
                                $result['api_status'] = 0;
                                $result['api_message'] = 'Your password is wrong !';
                                if (CRUDBooster::getSetting('api_debug_mode') == 'true') {
                                    $result['api_authorization'] = $debug_mode_message;
                                }
                                goto show;
                            }

                            if (! $required && $used && $value && ! Hash::check($value, $row->{$name})) {
                                $result['api_status'] = 0;
                                $result['api_message'] = 'Your password is wrong !';
                                if (CRUDBooster::getSetting('api_debug_mode') == 'true') {
                                    $result['api_authorization'] = $debug_mode_message;
                                }
                                goto show;
                            }
                        }

                        foreach ($rows as $k => $v) {
                            $ext = \File::extension($v);
                            if (in_array($ext, $uploads_format_candidate)) {
                                $rows->$k = asset($v);
                            }

                            if (! in_array($k, $responses_fields)) {
                                unset($row[$k]);
                            }
                        }

                        $result['api_status'] = 1;
                        $result['api_message'] = 'success';
                        if (CRUDBooster::getSetting('api_debug_mode') == 'true') {
                            $result['api_authorization'] = $debug_mode_message;
                        }
                        $rows = (array) $rows;
                        $result = array_merge($result, $rows);
                    }
                }

                if ($action_type == 'delete') {

                    if (CRUDBooster::isColumnExists($table, 'deleted_at')) {
                        $delete = $data->update(['deleted_at' => date('Y-m-d H:i:s')]);
                    } else {
                        $delete = $data->delete();
                    }

                    $result['api_status'] = ($delete) ? 1 : 0;
                    $result['api_message'] = ($delete) ? "success" : "failed";
                    if (CRUDBooster::getSetting('api_debug_mode') == 'true') {
                        $result['api_authorization'] = $debug_mode_message;
                    }
                }
            }

            if (in_array($action_type, ['save_add', 'save_edit'])) {

                foreach ($parameters as $param) {
                    $name = $param['name'];
                    $used = $param['used'];
                    $value = $posts[$name];
                    if ($used == '1' && $value == '') {
                        unset($row_assign[$name]);
                    }
                }
            }

            show:
            $result['api_status'] = $this->hook_api_status ?: $result['api_status'];
            $result['api_message'] = $this->hook_api_message ?: $result['api_message'];

            if (CRUDBooster::getSetting('api_debug_mode') == 'true') {
                $result['api_authorization'] = $debug_mode_message;
            }

            $this->hook_after($posts, $result);

            return response()->json($result);
        }
    }

    /**
     * @param $param
     * @param $type_except
     * @param $table
     * @return array
     * @internal param $required
     * @internal param $type
     * @internal param $config
     * @internal param $name
     */
    private function makeValidationRules($param, $type_except, $table)
    {
        $name = $param['name'];
        $type = $param['type'];
        $required = $param['required'];
        $config = $param['config'];

        $format_validation = [];

        if ($required) {
            $format_validation[] = 'required';
        }

        if (in_array($type, ['unique', 'exists'])) {
            $config = explode(',', $config);
            $table_exist = $config[0];
            $table_exist = CRUDBooster::parseSqlTable($table_exist)['table'];
            $field_exist = $config[1];
            $config = ($field_exist) ? $table_exist.','.$field_exist : $table_exist;
            $format_validation[] = $type.':'.$config;
        } elseif (in_array($type, ['date_format','digits_between', 'in', 'mimes', 'min', 'max', 'not_in'])) {
            $format_validation[] = $type.':'.$config;
        } elseif (! in_array($type, $type_except)) {
            $format_validation[] = $type;
        }

        if ($name == 'id') {
            $table_exist = CRUDBooster::parseSqlTable($table)['table'];
            $format_validation[] = 'exists:'.$table_exist.',id';
        }

        return implode('|', $format_validation);
    }

    /**
     * @param $name
     * @param $row_assign
     * @return array
     */
    private function handleFile($name, $row_assign)
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
    }

    /**
     * @param $value
     * @param $uploads_format_candidate
     * @param $row_assign
     * @param $name
     * @return mixed
     */
    private function handleBase64($value, $uploads_format_candidate, $row_assign, $name)
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
    }
}




