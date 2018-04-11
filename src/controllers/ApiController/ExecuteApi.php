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
        list($type_except, $input_validator) = $this->validateParams($parameters, $table);

        @$responses = unserialize($row_api->responses);
        $responses_fields = $this->prepareResponses($responses);

        $posts = request()->all();
        $this->ctrl->hookBefore($posts);


        unset($posts['limit'], $posts['offset'], $posts['orderby']);

        if (in_array($actionType, ['list', 'detail', 'delete'])) {
            $data = $this->fetchDataFromDB($table, $responses, $responses_fields, $parameters, $posts);

            $this->filterRows($data, $parameters, $posts, $table, $type_except);

            //IF SQL WHERE IS NOT NULL
            if ($row_api->sql_where) {
                $data->whereraw($row_api->sql_where);
            }

            $this->ctrl->hookQuery($data);
            $result = [];
            if ($actionType == 'list') {
                $result = $this->handleListAction($table, $data, $responses_fields);
            } elseif ($actionType == 'detail') {
                $result = $this->handleDetailsAction($data, $parameters, $posts, $responses_fields);
            } elseif ($actionType == 'delete') {
                $result = $this->handleDeleteAction($table, $data);
            }
            $this->show($result, $posts);
        } elseif (in_array($actionType, ['save_add', 'save_edit'])) {
            $rowAssign = array_filter($input_validator, function ($column) use ($table) {
                return Schema::hasColumn($table, $column);
            }, ARRAY_FILTER_USE_KEY);

            $this->handleAddEdit($parameters, $posts, $rowAssign);
        }

    }

    /**
     * @param $result
     * @param $posts
     * @return mixed
     */
    private function show($result, $posts)
    {
        $this->ctrl->hookAfter($posts, $result);
        $result['api_status'] = $this->ctrl->hook_api_status ?: $result['api_status'];
        $result['api_message'] = $this->ctrl->hook_api_message ?: $result['api_message'];

        if (cbGetsetting('api_debug_mode') == 'true') {
            $result['api_authorization'] = 'You are in debug mode !';
        }
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
     * @param $data
     * @param $responsesFields
     * @return array
     */
    private function handleListAction($table, $data, $responsesFields)
    {
        $orderBy = request('orderby', $table.'.id,desc');

        list($orderByCol, $orderByVal) = explode(',', $orderBy);

        $rows = $data->orderby($orderByCol, $orderByVal)->get();
        if (! $rows) {
            $result = $this->makeResult(0, 'There is no data found !');
            $result['data'] = [];

            return $result;
        }

        return $this->handleRows($responsesFields, $rows);
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

        return array_merge($result, (array)$rows);
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
                $method = 'orWhere';
                if ($k == 0) {
                    $method = 'where';
                }
                $w->$method($field, "like", "%$value%");
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
        $joinTable = DbInspector::getTableForeignKey($name);
        $data->leftjoin($joinTable, $joinTable.'.id', '=', $table.'.'.$name);
        foreach (\Schema::getColumnListing($joinTable) as $jf) {
            $jfAlias = $joinTable.'_'.$jf;
            if (in_array($jfAlias, $responsesFields)) {
                $data->addselect($joinTable.'.'.$jf.' as '.$jfAlias);
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
     * @param $inputValidator
     * @param $dataValidation
     * @param $posts
     * @return mixed
     */
    private function doValidation($inputValidator, $dataValidation, $posts)
    {
        $validator = Validator::make($inputValidator, $dataValidation);
        if (! $validator->fails()) {
            return true;
        }
        $message = $validator->errors()->all();
        $message = implode(', ', $message);
        $result = $this->makeResult(0, $message);

        $this->show($result, $posts);
    }

    /**
     * @param $data
     * @param $parameters
     * @param $posts
     * @param $responsesFields
     * @return array
     */
    private function handleDetailsAction($data, $parameters, $posts, $responsesFields)
    {
        $row = $data->first();

        if (! $row) {
            return $this->makeResult(0, 'There is no data found !');
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
            if (Hash::check($value, $row->{$name})) {
                continue;
            }

            if ($required && $type == 'password') {
                $this->passwordError($posts);
            }

            if (! $required && $used && $value) {
                $this->passwordError($posts);
            }
        }

        $this->handleFile($row, $responsesFields, $row);

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
        if (! $parameters) {
            return ['', ''];
        }
        $typeExcept = ['password', 'ref', 'base64_file', 'custom', 'search'];
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
            $dataValidation[$name] = app(ValidationRules::class)->make($param, $typeExcept, $table);
        }

        $this->doValidation($inputValidator, $dataValidation, $posts);

        return [$typeExcept, $inputValidator];
    }

    /**
     * @param $table
     * @param $responses
     * @param $responsesFields
     * @param $parameters
     * @param $posts
     * @return array
     */
    private function fetchDataFromDB($table, $responses, $responsesFields, $parameters, $posts)
    {
        $data = DB::table($table);
        $data->skip(request('offset', 0));
        $data->take(request('limit', 20));
        $data = $this->responses($table, $data, $responses, $responsesFields); //End Responses

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
        $result = [
            'api_status'=> $status,
            'api_message'=> $msg,
        ];

        if (cbGetsetting('api_debug_mode') == 'true') {
            $result['api_authorization'] = 'You are in debug mode !';
        }

        return $result;
    }
}