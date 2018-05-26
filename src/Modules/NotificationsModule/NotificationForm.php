<?php

namespace crocodicstudio\crudbooster\Modules\NotificationsModule;

class NotificationForm
{
    public static function makeForm()
    {
        //todo: translate
        return [
            ['label' => "Content", 'name' => "content", 'type' => "text"],
            ['label' => "Icon", 'name' => "icon", 'type' => "text"],
            ['label' => "Notification Command", 'name' => "notification_command", 'type' => "textarea"],
            ['label' => "Is Read", 'name' => "is_read", 'type' => "text"],
        ];
    }
}