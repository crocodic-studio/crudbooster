<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 8/13/2019
 * Time: 10:33 PM
 */

namespace crocodicstudio\crudbooster\helpers;


class CurlHelper
{

    private $url;
    private $headers;
    private $data;
    private $type;

    public function __construct($url, $type = "POST")
    {
        $this->url = $url;
        $this->type = $type;
    }

    public function headers(array $headers) {
        $newHeaders = [];
        foreach($headers as $key=>$val) {
            $newHeaders[] = $key.": ".$val;
        }
        $this->headers = $newHeaders;
    }

    public function data(array $data) {
        $this->data = $data;
    }

    public function send() {
        $ch = curl_init($this->url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->type);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if($err) {
            return $err;
        }else {
            return $response;
        }
    }
}