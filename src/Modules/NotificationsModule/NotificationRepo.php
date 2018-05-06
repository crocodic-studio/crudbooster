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
        $id_cms_users = $config['id_cms_users'];
        $id_cms_users = ($id_cms_users) ?: [CRUDBooster::myId()];
        foreach ($id_cms_users as $id) {
            $notif = [
                'created_at' => YmdHis(),
                'id_cms_users' => $id,
                'content' => $content,
                'is_read' => 0,
                'url' => $to,
            ];
            DB::table('cms_notifications')->insert($notif);
        }

        return true;
    }
}