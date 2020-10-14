<?php


namespace crocodicstudio\crudbooster\controllers;


use crocodicstudio\crudbooster\helpers\CB;
use Illuminate\Support\Facades\Cache;

class ApiAuthorizationController extends Controller
{
    private $ttl = 2880;
    private $token_length = 16;

    public function postGetToken() {
        CB::valid(['secret'=>'required'],'json');

        $exists = db("cms_apikey")
            ->where("screetkey", g("secret"))
            ->where("status","active")
            ->count();
        if($exists) {
            $accessToken = str_random($this->token_length);;
            Cache::put("api_token_".$accessToken,[
                "ip"=> $_SERVER['REMOTE_ADDR'],
                "user_agent"=> $_SERVER['HTTP_USER_AGENT']
            ], $this->ttl);

            return response()->json([
                'api_status'=>1,
                'api_message'=>'success',
                'data'=>[
                    'access_token'=>$accessToken,
                    'expiry'=> strtotime("+".$this->ttl." minutes")
                ]
            ]);
        } else {
            return response()->json([
                'api_status'=>0,
                'api_message'=>'Credential invalid!'
            ], 400);
        }
    }
}