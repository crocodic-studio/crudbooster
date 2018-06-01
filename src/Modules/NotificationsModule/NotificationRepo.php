<?php

namespace crocodicstudio\crudbooster\Modules\NotificationsModule;


class NotificationRepo
{
    public static function sendNotification(array $config = [])
    {
        $userIds = ($config['cms_users_id']) ?: [auth('cbAdmin')->id()];
        $notifications = [];
        foreach ($userIds as $userId) {
            $notifications[] = [
                'created_at' => YmdHis(),
                'cms_users_id' => $userId,
                'content' => $config['content'],
                'is_read' => 0,
                'url' => $config['to'],
            ];
        }
        \DB::table('cms_notifications')->insert($notifications);

        return true;
    }
}