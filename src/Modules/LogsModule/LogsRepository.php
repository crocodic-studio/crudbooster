<?php

namespace crocodicstudio\crudbooster\Modules\LogsModule;

class LogsRepository
{
    public static function insertLog($description, $uid, $ip = null)
    {
        $log = [
            'created_at' => YmdHis(),
            'ipaddress' => $ip ?: request()->ip(),
            'useragent' => request()->userAgent(),
            'url' => request()->url(),
            'cms_users_id' => $uid,
            'description' => $description
        ];
        \DB::table('cms_logs')->insert($log);
    }
}