<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 4/28/2019
 * Time: 10:30 PM
 */

namespace crocodicstudio\crudbooster\helpers;


class FCM
{
    private static $title;
    private $message;
    private $data;
    private $fields;

    public static function title($title) {
        static::$title = $title;
        return new static;
    }

    public function message($message) {
        $this->message = $message;
        return $this;
    }

    public function data(array $data) {
        $this->data = $data;
        return $this;
    }

    public function tokenIOS(array $tokens) {
        $data['title'] = static::$title;
        $data['message'] = $this->message;
        $this->fields = [
            'registration_ids' => $tokens,
            'data' => $this->data,
            'content_available' => true,
            'notification' => [
                'sound' => 'default',
                'badge' => 0,
                'title' => trim(strip_tags(static::$title)),
                'body' => trim(strip_tags($this->message)),
            ],
            'priority' => 'high',
        ];
        return $this;
    }

    public function tokenAndroid(array $tokens) {
        $data['title'] = static::$title;
        $data['message'] = $this->message;
        $this->fields = [
            'registration_ids' => $tokens,
            'data' => $this->data,
            'content_available' => true,
            'priority' => 'high',
        ];
        return $this;
    }

    public function send() {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $headers = [
            'Authorization:key='.cbConfig('GOOGLE_FCM_SERVER_KEY'),
            'Content-Type:application/json',
        ];

        if(static::$title && $this->message) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->fields));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            curl_close($ch);
            return $result;
        }
        
        return null;
    }

}