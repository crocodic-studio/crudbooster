<?php

namespace crocodicstudio\crudbooster\helpers;

use Illuminate\Support\Facades\Validator;

class CbValidator
{
    public static function valid($rules = [], string $type = 'json')
    {
        $validator = Validator::make(request()->all(), $rules);

        if (!$validator->fails()) {
            return true;
        }

        $message = $validator->errors()->all();

        if ($type != 'json') {
            sendAndTerminate(redirect()->back()->with(['message' => implode('<br/>', $message), 'message_type' => 'warning'])->withInput());
        }
        $result = [
            'api_status' => 0,
            'api_message' => implode(', ', $message),
        ];
        sendAndTerminate(response()->json($result, 200));
    }
}