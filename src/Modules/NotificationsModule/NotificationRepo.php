<?php

namespace crocodicstudio\crudbooster\Modules\NotificationsModule;

use crocodicstudio\crudbooster\helpers\CRUDBooster;
use Illuminate\Support\Facades\DB;

class NotificationRepo
{
    public static function sendNotification($config = [])
    {
        $content = $config['content'];
        $to = $config['to'];
        $cms_users_id = $config['cms_users_id'];
        $cms_users_id = ($cms_users_id) ?: [auth('cbAdmin')->id()];
        foreach ($cms_users_id as $id) {
            $notif = [
                'created_at' => YmdHis(),
                'cms_users_id' => $id,
                'content' => $content,
                'is_read' => 0,
                'url' => $to,
            ];
            DB::table('cms_notifications')->insert($notif);
        }

        return true;
    }
}