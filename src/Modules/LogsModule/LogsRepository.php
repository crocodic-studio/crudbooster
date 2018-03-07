<?php

namespace crocodicstudio\crudbooster\Modules\LogsModule;

class LogsRepository
{
    public static function insertLog($description, $uid)
    {
        $log = [
            'created_at' => date('Y-m-d H:i:s'),
            'ipaddress' => $_SERVER['REMOTE_ADDR'],
            'useragent' => $_SERVER['HTTP_USER_AGENT'],
            'url' => \Request::url(),
            'description' => $description,
            'id_cms_users' => $uid,
        ];
        \DB::table('cms_logs')->insert($log);
    }
}