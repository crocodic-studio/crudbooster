<?php

namespace crocodicstudio\crudbooster\helpers;

use Illuminate\Support\Facades\Validator;

class CbValidator
{
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
}