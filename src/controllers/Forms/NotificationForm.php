<?php

namespace crocodicstudio\crudbooster\controllers\Forms;

class NotificationForm
{
    public static function makeForm()
    {
        $form = [];
        $form[] = ["label" => "Content", "name" => "content", "type" => "text"];
        $form[] = ["label" => "Icon", "name" => "icon", "type" => "text"];
        $form[] = ["label" => "Notification Command", "name" => "notification_command", "type" => "textarea"];
        $form[] = ["label" => "Is Read", "name" => "is_read", "type" => "text"];
        return $form;
    }
}