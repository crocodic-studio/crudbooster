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

    /**
     * @param array $registration_ids
     * @param string $title
     * @param string $message
     * @param array $data
     * @return string
     */
    public function sendToIOS($registration_ids, $title, $message, $data) {
        $data['title'] = $title;
        $data['message'] = $message;
        $fields = [
            'registration_ids' => $registration_ids,
            'data' => $data,
            'content_available' => true,
            'notification' => [
                'sound' => 'default',
                'badge' => 0,
                'title' => trim(strip_tags($title)),
                'body' => trim(strip_tags($message)),
            ],
            'priority' => 'high',
        ];
        return $this->run($fields);
    }

    /**
     * @param array $registration_ids
     * @param string $title
     * @param string $message
     * @param array $data
     * @return string
     */
    public function sendToAndroid($registration_ids, $title, $message, $data) {
        $data['title'] = $title;
        $data['message'] = $message;
        $fields = [
            'registration_ids' => $registration_ids,
            'data' => $data,
            'content_available' => true,
            'priority' => 'high',
        ];
        return $this->run($fields);
    }

    private function run($fields) {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $headers = [
            'Authorization:key='.cbConfig('GOOGLE_FCM_SERVER_KEY'),
            'Content-Type:application/json',
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

}