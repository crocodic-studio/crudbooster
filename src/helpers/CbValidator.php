<?php

namespace Crocodicstudio\Crudbooster\helpers;

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
            respondWith(redirect()->back()->with(['message' => implode('<br/>', $message), 'message_type' => 'warning'])->withInput());
        }
        $result = [
            'api_status' => 0,
            'api_message' => implode(', ', $message),
        ];
        respondWith()->json($result, 200);
    }
}