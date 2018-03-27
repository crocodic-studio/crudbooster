<?php

namespace crocodicstudio\crudbooster\Modules\LogsModule;

use Illuminate\Support\Facades\Log;

class LogsRepository
{
    public static function insertLog($description, $uid)
    {
        $log = [
            'created_at' => date('Y-m-d H:i:s'),
            'ipaddress' => $_SERVER['REMOTE_ADDR'],
            'useragent' => $_SERVER['HTTP_USER_AGENT'],
            'url' => request()->url(),
            'user_id' => $uid,
        ];
        Log::info($description, $log);
    }
}