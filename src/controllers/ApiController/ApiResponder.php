<?php

namespace crocodicstudio\crudbooster\controllers\ApiController;

class ApiResponder
{
    /**
     * @param $status
     * @param $msg
     * @return array
     */
    public static function makeResult($status, $msg)
    {
        $result = [
            'api_status'=> $status,
            'api_message'=> $msg,
        ];

        if (cbGetsetting('api_debug_mode') == 'true') {
            $result['api_authorization'] = 'You are in debug mode !';
        }

        return $result;
    }

    /**
     * @param $result
     * @param $posts
     * @param $ctrl
     * @return mixed
     */
    public static function send($result, $posts, $ctrl)
    {
        $ctrl->hookAfter($posts, $result);
        $result['api_status'] = $ctrl->hook_api_status ?: $result['api_status'];
        $result['api_message'] = $ctrl->hook_api_message ?: $result['api_message'];

        if (cbGetsetting('api_debug_mode') == 'true') {
            $result['api_authorization'] = 'You are in debug mode !';
        }
        sendAndTerminate(response()->json($result));
    }

}