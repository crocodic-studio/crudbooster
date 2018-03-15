<?php

namespace crocodicstudio\crudbooster\helpers;

class Cache
{
    public static function get($section, $cacheName)
    {
        if (! Cache::has($section)) {
            return false;
        }
        $cacheOpen = Cache::get($section);

        return $cacheOpen[$cacheName];
    }

    public static function put($section, $cache_name, $cacheValue)
    {
        if (Cache::has($section)) {
            $cacheOpen = Cache::get($section);
        } else {
            Cache::forever($section, []);
            $cacheOpen = Cache::get($section);
        }
        $cacheOpen[$cache_name] = $cacheValue;
        Cache::forever($section, $cacheOpen);

        return true;
    }


}