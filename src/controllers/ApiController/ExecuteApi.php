<?php

namespace crocodicstudio\crudbooster\controllers\ApiController;

use crocodicstudio\crudbooster\helpers\DbInspector;
use crocodicstudio\crudbooster\Modules\ModuleGenerator\ControllerGenerator\FieldDetector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class ExecuteApi
{
    private $ctrl;

    /**
     * ExecuteApi constructor.
     *
     * @param $ctrl
     */
    public function __construct($ctrl)
    {
        $this->ctrl = $ctrl;
    }

    public function execute()
    {

        //$posts_keys = array_keys($posts);
        //$posts_values = array_values($posts);

        $row_api = DB::table('cms_apicustom')->where('permalink', $this->ctrl->permalink)->first();

        $actionType = $row_api->aksi;
        $table = $row_api->tabel;

        /* Do some custome pre-checking for posted data, if failed discard API execution */
        $this->doCustomePrecheck();

        /* Method Type validation */
        $methodType = $row_api->method_type;
        $this->validateMethodType($methodType);

        /* Check the row is exists or not */
        $this->checkApiDefined($row_api);
        @$parameters = unserialize($row_api->parameters);
        @$responses = unserialize($row_api->responses);

        /*
        | ----------------------------------------------
        | User Data Validation
        | ----------------------------------------------
        |
        */
        list($type_except, $input_validator) = $this->validateParams($parameters, $table);


        $responses_fields = $this->prepareResponses($responses);

        $posts = request()->all();
        $this->ctrl->hookBefore($posts);

        $limit = ($posts['limit']) ?: 20;
        $offset = ($posts['offset']) ?: 0;
        $orderby = ($posts['orderby']) ?: $table.'.id,desc';

        unset($posts['limit'], $posts['offset'], $posts['orderby']);

        if (in_array($actionType, ['list', 'detail', 'delete'])) {
            $data = $this->fetchDataFromDB($table, $offset, $limit, $responses, $responses_fields, $parameters, $posts);

            $this->filterRows($data, $parameters, $posts, $table, $type_except);

            //IF SQL WHERE IS NOT NULL
            if ($row_api->sql_where) {
                $data->whereraw($row_api->sql_where);
            }

            $this->ctrl->hookQuery($data);

            $result = [];
            if ($actionType == 'list') {
                $result = $this->handleListAction($table, $orderby, $data, $responses_fields);
            }elseif ($actionType == 'detail') {
                $result = $this->handleDetailsAction($data, $parameters, $posts, $responses_fields);
            }elseif ($actionType == 'delete') {
                $result = $this->handleDeleteAction($table, $data);
            }
        }elseif (in_array($actionType, ['save_add', 'save_edit'])) {
            $rowAssign = array_filter($input_validator, function ($column) use ($table) {
                return Schema::hasColumn($table, $column);
            }, ARRAY_FILTER_USE_KEY);

            $this->handleAddEdit($parameters, $posts, $rowAssign);
        }

        $this->show($result, $posts);
    }

    /**
     * @param $result
     * @param $posts
     * @return mixed
     */
    private function show($result, $posts)
    {
        $result['api_status'] = $this->ctrl->hook_api_status ?: $result['api_status'];
        $result['api_message'] = $this->ctrl->hook_api_message ?: $result['api_message'];

        if (cbGetsetting('api_debug_mode') == 'true') {
            $result['api_authorization'] = 'You are in debug mode !';
        }

        $this->ctrl->hookAfter($posts, $result);

        sendAndTerminate(response()->json($result));
    }

    /**
     * @param $responses
     * @return array
     */
    private function prepareResponses($responses)
    {
        $responsesFields = [];
        foreach ($responses as $r) {
            if ($r['used']) {
                $responsesFields[] = $r['name'];
            }
        }

        return $responsesFields;
    }

    /**
     * @param $table
     * @param $orderby
     * @param $data
     * @param $responsesFields
     * @return array
     */
    private function handleListAction($table, $orderby, $data, $responsesFields)
    {
        $orderbyCol = $table.'.id';
        $orderbyVal = 'desc';

        if ($orderby) {
            list($orderbyCol, $orderbyVal) = explode(',', $orderby);
        }

        $rows = $data->orderby($orderbyCol, $orderbyVal)->get();

        $result = $this->makeResult(0, 'There is no data found !');

        $result['data'] = [];
        if ($rows) {
            $result = $this->handleRows($responsesFields, $rows);
        }

        return $result;
    }

    /**
     * @param $table
     * @param $data
     * @return mixed
     */
    private function handleDeleteAction($table, $data)
    {
        if (\Schema::hasColumn($table, 'deleted_at')) {
            $delete = $data->update(['deleted_at' => date('Y-m-d H:i:s')]);
        } else {
            $delete = $data->delete();
        }

        $status = ($delete) ? 1 : 0;
        $msg = ($delete) ? "success" : "failed";
        $this->makeResult($status, $msg);

        return $this->makeResult($status, $msg);
    }

    /**
     * @param $data
     * @param $parameters
     * @param $posts
     * @param $table
     * @param $typeExcept
     */
    private function filterRows($data, $parameters, $posts, $table, $typeExcept)
    {
        $data->where(function ($w) use ($parameters, $posts, $table, $typeExcept) {
            foreach ($parameters as $param) {
                $name = $param['name'];
                $type = $param['type'];
                $value = $posts[$name];
                $used = $param['used'];
                $required = $param['required'];

                if (in_array($type, $typeExcept)) {
                    continue;
                }

                if ($param['config'] != '' && substr($param['config'], 0, 1) != '*') {
                    $value = $param['config'];
                }

                if ($required == '1') {
                    $this->applyWhere($w, $table, $name, $value);
                } else {
                    if ($used && $value) {
                        $this->applyWhere($w, $table, $name, $value);
                    }
                }
            }
        });
    }

    /**
     * @param $w
     * @param $table
     * @param $name
     * @param $value
     */
    private function applyWhere($w, $table, $name, $value)
    {
        if (\Schema::hasColumn($table, $name)) {
            $w->where($table.'.'.$name, $value);
        } else {
            $w->having($name, '=', $value);
        }
    }

    /**
     * @param $parameters
     * @param $posts
     * @param $data
     * @param $table
     * @return null
     */
    private function params($parameters, $posts, $data, $table)
    {
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

            if ($required == '1' || ($used && $value)) {
                $this->applyLike($data, $search_in, $value);
            }
        }
    }

    /**
     * @param $responsesFields
     * @param $rows
     * @return array
     */
    private function handleRows($responsesFields, $rows)
    {
        $uploadsFormatCandidate = explode(',', cbConfig("UPLOAD_TYPES"));
        foreach ($rows as &$row) {
            foreach ($row as $k => $v) {
                $ext = \File::extension($v);
                if (in_array($ext, $uploadsFormatCandidate)) {
                    $row->$k = asset($v);
                }

                if (! in_array($k, $responsesFields)) {
                    unset($row[$k]);
                }
            }
        }

        $result = $this->makeResult(1, 'success');
        $result['data'] = $rows;

        return $result;
    }

    /**
     * @param $parameters
     * @param $posts
     * @param $rowAssign
     */
    private function handleAddEdit($parameters, $posts, $rowAssign)
    {
        foreach ($parameters as $param) {
            $name = $param['name'];
            $used = $param['used'];
            $value = $posts[$name];
            if ($used == '1' && $value == '') {
                unset($rowAssign[$name]);
            }
        }
    }

    /**
     * @param $posts
     * @return mixed
     */
    private function passwordError($posts)
    {
        $result = $this->makeResult(0, cbTrans('alert_password_wrong'));

        $this->show($result, $posts);
    }

    /**
     * @param $rows
     * @param $responsesFields
     * @param $row
     */
    private function handleFile($rows, $responsesFields, $row)
    {
        foreach ($rows as $k => $v) {
            $ext = \File::extension($v);
            if (FieldDetector::isUploadField($ext)) {
                $rows->$k = asset($v);
            }

            if (! in_array($k, $responsesFields)) {
                unset($row[$k]);
            }
        }
    }

    /**
     * @param $table
     * @param $data
     * @param $responses
     *
     * @param $responsesFields
     * @return array
     */
    private function responses($table, $data, $responses, $responsesFields)
    {
        $name_tmp = [];

        $responses = $this->filterRedundantResp($responses);

        foreach ($responses as $resp) {
            $name = $resp['name'];
            $subquery = $resp['subquery'];
            $used = intval($resp['used']);

            if (in_array($name, $name_tmp)) {
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
            $name_tmp = $this->joinRelatedTables($table, $responsesFields, $name, $data, $name_tmp);
        }

        return $data;
    }

    /**
     * @param $rows
     * @return array
     */
    private function success($rows)
    {
        $result = $this->makeResult(1, 'success');
        $rows = (array) $rows;
        $result = array_merge($result, $rows);

        return $result;
    }

    /**
     * @param $data
     * @param $search_in
     * @param $value
     */
    private function applyLike($data, $search_in, $value)
    {
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

    /**
     * @param $table
     * @param $responsesFields
     * @param $name
     * @param $data
     * @param $nameTmp
     * @return array
     */
    private function joinRelatedTables($table, $responsesFields, $name, $data, $nameTmp)
    {
        if (! DbInspector::isForeignKey($name)) {
            return $nameTmp;
        }
        $jointable = DbInspector::getTableForeignKey($name);
        $data->leftjoin($jointable, $jointable.'.id', '=', $table.'.'.$name);
        foreach (\Schema::getColumnListing($jointable) as $jf) {
            $jfAlias = $jointable.'_'.$jf;
            if (in_array($jfAlias, $responsesFields)) {
                $data->addselect($jointable.'.'.$jf.' as '.$jfAlias);
                $nameTmp[] = $jfAlias;
            }
        }
        return $nameTmp;
    }

    /**
     * @param $methodType
     * @return mixed
     */
    private function validateMethodType($methodType)
    {
        $posts = request()->all();

        if ($methodType && request()->isMethod($methodType)) {
            return true;
        }

        $result = $this->makeResult(0, "The request method is not allowed !");
        $this->show($result, $posts);
    }

    /**
     * @return mixed
     */
    private function doCustomePrecheck()
    {
        $posts = request()->all();
        $this->ctrl->hookValidate($posts);

        if (! $this->ctrl->validate) {
            return true;
        }  // hook have to return true

        $result = $this->makeResult(0, 'Failed to execute API !');

        $this->show($result, $posts);
    }

    /**
     * @param $rowApi
     * @return mixed
     */
    private function checkApiDefined($rowApi)
    {
        $posts = request()->all();

        if ($rowApi) {
            return true;
        }

        $msg = 'Sorry this API is no longer available, maybe has changed by admin, or please make sure api url is correct.';
        $result = $this->makeResult(0, $msg);
        $this->show($result, $posts);
    }

    /**
     * @param $input_validator
     * @param $data_validation
     * @param $posts
     * @return mixed
     */
    private function doValidation($input_validator, $data_validation, $posts)
    {
        $validator = Validator::make($input_validator, $data_validation);
        if (! $validator->fails()) {
            return true;
        }
        $message = $validator->errors()->all();
        $message = implode(', ', $message);
        $result = [];
        $result['api_status'] = 0;
        $result['api_message'] = $message;

        $this->show($result, $posts);
    }

    /**
     * @param $data
     * @param $parameters
     * @param $posts
     * @param $responses_fields
     * @return array
     */
    private function handleDetailsAction($data, $parameters, $posts, $responses_fields)
    {
        $result = $this->makeResult(0, 'There is no data found !');

        $row = $data->first();

        if (!$row) {
            return $result;
        }

        foreach ($parameters as $param) {
            $name = $param['name'];
            $type = $param['type'];
            $value = $posts[$name];
            $used = $param['used'];
            $required = $param['required'];

            if ($param['config'] != '' && substr($param['config'], 0, 1) != '*') {
                $value = $param['config'];
            }

            if ($required && $type == 'password' && ! Hash::check($value, $row->{$name})) {
                $this->passwordError($posts);
            }

            if (! $required && $used && $value && ! Hash::check($value, $row->{$name})) {
                $this->passwordError($posts);
            }
        }

        $this->handleFile($row, $responses_fields, $row);

        return $this->success($row);
    }

    /**
     * @param $responses
     * @return array
     */
    private function filterRedundantResp($responses)
    {
        $responses = array_filter($responses, function ($resp) {
            return ! ($resp['name'] == 'ref_id' || $resp['type'] == 'custom');
        });

        $responses = array_filter($responses, function ($resp) {
            return (intval($resp['used']) != 0 || DbInspector::isForeignKey($resp['name']));
        });

        return $responses;
    }

    /**
     * @param $parameters
     * @param $posts
     * @param $table
     * @return array
     */
    private function validateParams($parameters, $table)
    {
        $posts = request()->all();
        if (!$parameters) {
            return ['', ''];
        }
        $type_except = ['password', 'ref', 'base64_file', 'custom', 'search'];
        $inputValidator = [];
        $dataValidation = [];

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

            $inputValidator[$name] = $value;
            $dataValidation[$name] = app(ValidationRules::class)->make($param, $type_except, $table);
        }

        $this->doValidation($inputValidator, $dataValidation, $posts);

        return [$type_except, $inputValidator];
    }

    /**
     * @param $table
     * @param $offset
     * @param $limit
     * @param $responses
     * @param $responses_fields
     * @param $parameters
     * @param $posts
     * @return array
     */
    private function fetchDataFromDB($table, $offset, $limit, $responses, $responses_fields, $parameters, $posts)
    {
        $data = DB::table($table);
        $data->skip($offset);
        $data->take($limit);
        $data = $this->responses($table, $data, $responses, $responses_fields); //End Responses

        $this->params($parameters, $posts, $data, $table);

        if (\Schema::hasColumn($table, 'deleted_at')) {
            $data->where($table.'.deleted_at', null);
        }

        return $data;
    }

    /**
     * @param $status
     * @param $msg
     * @return array
     */
    private function makeResult($status, $msg)
    {
        $result = [];
        $result['api_status'] = $status;
        $result['api_message'] = $msg;
        if (cbGetsetting('api_debug_mode') == 'true') {
            $result['api_authorization'] = 'You are in debug mode !';
        }

        return $result;
    }
}