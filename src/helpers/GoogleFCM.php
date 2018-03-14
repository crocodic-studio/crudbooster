<?php

namespace crocodicstudio\crudbooster\helpers;

class GoogleFCM
{
    private $url = 'https://fcm.googleapis.com/fcm/send';
    public function send($regID, $data)
    {
        if (! $data['title'] || ! $data['content']) {
            return 'title , content null !';
        }

        $fields = $this->getFields($regID, $data);
        $headers = $this->getHeaders();

        return $this->sendRequest($headers, $fields);
    }

    /**
     * @param $headers
     * @param $fields
     * @return mixed
     */
    private function sendRequest($headers, $fields)
    {
        $ch = curl_init($this->url);
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

    /**
     * @param $regID
     * @param $data
     * @return array
     */
    private function getFields($regID, $data)
    {
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

        return $fields;
    }

    /**
     * @param $apikey
     * @return array
     */
    private function getHeaders($apikey)
    {
        $headers = [
            'Authorization:key='.$apikey,
            'Content-Type:application/json',
        ];

        return $headers;
    }
}