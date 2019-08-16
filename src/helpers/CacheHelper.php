<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 8/16/2019
 * Time: 1:29 AM
 */

namespace crocodicstudio\crudbooster\helpers;


use Illuminate\Support\Facades\Cache;

class CacheHelper
{
    public static function putInGroup($key, $value, $group, $duration) {
        $key = md5($key);
        $exist = (Cache::get($group))?:[];
        $exist[ $key ] = ['value'=>$value,'duration'=>$duration];

        if($duration == -1) {
            Cache::forever($group, $exist);
        } else {
            Cache::put($group, $exist, $duration);
        }
    }

    public static function getItemInGroup($key, $group) {
        $groupdata = Cache::get($group);
        $key = md5($key);
        if(isset($groupdata[$key])) {
            return $groupdata[$key]['value'];
        }
        return null;
    }

    public static function forgetGroup($group) {
        Cache::forget($group);
    }

    public static function forgetInGroup($key,$group) {
        $key = md5($key);
        $data = Cache::get($group);
        $duration = null;
        if(isset($data[$key])) {
            $item = $data[$key];
            unset($data[$key]);
        }
        Cache::put($group, $exist, $item['duration']);
        return true;
    }

}