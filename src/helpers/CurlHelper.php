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
    private $timeout;
    private $user_agent;
    private $referer;
    private $cookie;

    public function __construct($url, $type = "POST")
    {
        $this->url = $url;
        $this->type = $type;
        $this->timeout = 30;
        $this->user_agent = 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13';
        $this->referer = url("/");
        $this->cookie = false;
    }

    public function cookie($enable) {
        $this->cookie = $enable;
    }

    public function referer($referer) {
        $this->referer = $referer;
    }

    public function userAgent($user_agent) {
        $this->user_agent = $user_agent;
    }

    public function timeout($seconds) {
        $this->timeout = $seconds;
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
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($ch,CURLOPT_USERAGENT,$this->user_agent);
        curl_setopt($ch, CURLOPT_REFERER, $this->referer);
        if($this->cookie === true) {
            $cookie_dir = storage_path("cookies");
            if(!file_exists($cookie_dir)) mkdir($cookie_dir);
            $cookie_file = $cookie_dir."/".md5(request()->ip()).".txt";
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
        }

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