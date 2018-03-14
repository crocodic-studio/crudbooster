<?php

namespace crocodicstudio\crudbooster\helpers;

class GoogleFCM
{
    public function send($regID, $data)
    {
        if (! $data['title'] || ! $data['content']) {
            return 'title , content null !';
        }

        $apikey = SettingRepo::getSetting('google_fcm_key');
        $url = 'https://fcm.googleapis.com/fcm/send';
        $fields = [
            'registration_ids' => $regID,
            'data' => $data,
            'content_available' => true,
            'notification' => [
                'sound' => 'default',
                'badge' => 0,
                'title' => trim(strip_tags($data['title'])),
                'body' => trim(strip_tags($data['content'])),
            ],
            'priority' => 'high',
        ];
        $headers = [
            'Authorization:key='.$apikey,
            'Content-Type:application/json',
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $chresult = curl_exec($ch);
        curl_close($ch);

        return $chresult;
    }
}