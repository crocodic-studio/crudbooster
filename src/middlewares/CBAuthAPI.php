<?php

namespace crocodicstudio\crudbooster\middlewares;

use Closure;
use Config;
use crocodicstudio\crudbooster\Modules\ApiGeneratorModule\ApiKeysRepository;
use crocodicstudio\crudbooster\Modules\SettingModule\SettingRepo;
use DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Schema;
use Session;

class CBAuthAPI
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (SettingRepo::getSetting('api_debug_mode') !== 'false') {
            return;
        }

        $this->validateRequest();

        list($userAgent, $serverToken, $server_token_Secret) = $this->getTokens();

        $senderToken = Request::header('X-Authorization-Token');

        $this->tokenMissMatchResponse($senderToken, $serverToken);

        $this->tokenMissMatchDevice($senderToken, $userAgent, $serverToken);

        $id = array_search($senderToken, $serverToken);
        $serverSecret = $server_token_Secret[$id];
        ApiKeysRepository::incrementHit($serverSecret);

        $expiredToken = date('Y-m-d H:i:s', strtotime('+5 seconds'));
        Cache::put($senderToken, $userAgent, $expiredToken);

        return $next($request);
    }

    /**
     * @return null
     */
    private function validateRequest()
    {
        $validator = Validator::make([
            'X-Authorization-Token' => Request::header('X-Authorization-Token'),
            'X-Authorization-Time' => Request::header('X-Authorization-Time'),
            'useragent' => Request::header('User-Agent'),
        ], [
            'X-Authorization-Token' => 'required',
            'X-Authorization-Time' => 'required',
            'useragent' => 'required',
        ]);
        if (!$validator->fails()) {
            return;
        }
        $result = [
            'api_status' => 0,
            'api_message' => implode(', ', $validator->errors()->all()),
        ];
        sendAndTerminate(response()->json($result, 200));
    }

    /**
     * @return array
     */
    private function getTokens()
    {
        $userAgent = Request::header('User-Agent');
        $time = Request::header('X-Authorization-Time');

        $keys = ApiKeysRepository::getSecretKeys();
        $serverToken = [];
        $serverTokenSecret = [];
        foreach ($keys as $key) {
            $serverToken[] = md5($key.$time.$userAgent);
            $serverTokenSecret[] = $key;
        }

        return [$userAgent, $serverToken, $serverTokenSecret];
    }

    /**
     * @param $sender_token
     * @param $server_token
     * @return mixed
     */
    private function tokenMissMatchResponse($sender_token, $server_token)
    {
        if (Cache::has($sender_token) || in_array($sender_token, $server_token)) {
            return;
        }
        $result = [
            'api_status' => false,
            'api_message' => "THE TOKEN IS NOT MATCH WITH SERVER TOKEN",
            'sender_token' => $sender_token,
            'server_token' => $server_token,
        ];
        sendAndTerminate(response()->json($result, 200));
    }

    /**
     * @param $senderToken
     * @param $userAgent
     * @param $serverToken
     */
    private function tokenMissMatchDevice($senderToken, $userAgent, $serverToken)
    {
        if (! Cache::has($senderToken) || Cache::get($senderToken) == $userAgent) {
            return;
        }
        $result = [
            'api_status' => false,
            'api_message' => "THE TOKEN IS ALREADY BUT NOT MATCH WITH YOUR DEVICE",
            'sender_token' => $senderToken,
            'server_token' => $serverToken,
        ];
        sendAndTerminate(response()->json($result, 200));
    }
}
