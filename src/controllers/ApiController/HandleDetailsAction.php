<?php

namespace crocodicstudio\crudbooster\controllers\ApiController;

use Illuminate\Support\Facades\Hash;

class HandleDetailsAction
{
    /**
     * @param $data
     * @param $parameters
     * @param $posts
     * @param $responsesFields
     * @param $ctrl
     * @return array
     */
    public static function handle($data, $parameters, $posts, $responsesFields, $ctrl)
    {
        $row = $data->first();

        if (! $row) {
            return ApiResponder::makeResult(0, 'There is no data found !');
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
                self::passwordError($posts, $ctrl);
            }

            if (! $required && $used && $value) {
                self::passwordError($posts, $ctrl);
            }
        }

        self::handleFile($row, $responsesFields);

        return self::success($row);
    }

    /**
     * @param $posts
     * @return mixed
     */
    private static function passwordError($posts, $ctrl)
    {
        $result = ApiResponder::makeResult(0, cbTrans('alert_password_wrong'));

        ApiResponder::send($result, $posts, $ctrl);
    }

    /**
     * @param $rows
     * @return array
     */
    private static function success($rows)
    {
        $result = ApiResponder::makeResult(1, 'success');

        return array_merge($result, (array)$rows);
    }

    /**
     * @param $rows
     * @param $responsesFields
     */
    public function handleFile($rows, $responsesFields)
    {
        foreach ($rows as $k => $v) {
            if (FieldDetector::isUploadField(\File::extension($v))) {
                $rows->$k = asset($v);
            }

            if (! in_array($k, $responsesFields)) {
                unset($rows->$k);
            }
        }
    }

}