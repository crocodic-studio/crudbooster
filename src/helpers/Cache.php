<?php

namespace crocodicstudio\crudbooster\helpers;

use Cache as CacheFacade;

class Cache
{
    public static function get($section, $cacheName)
    {
        if (! CacheFacade::has($section)) {
            return false;
        }
        $cacheOpen = CacheFacade::get($section);

        return $cacheOpen[$cacheName];
    }

    public static function put($section, $cacheName, $cacheValue)
    {
        if (CacheFacade::has($section)) {
            $cacheOpen = CacheFacade::get($section);
        } else {
            CacheFacade::forever($section, []);
            $cacheOpen = CacheFacade::get($section);
        }
        $cacheOpen[$cacheName] = $cacheValue;
        CacheFacade::forever($section, $cacheOpen);

        return true;
    }

    public static function forgetCache($section, $cache_name)
    {
        if (! CacheFacade::has($section)) {
            return false;
        }
        $open = CacheFacade::get($section);
        unset($open[$cache_name]);
        CacheFacade::forever($section, $open);

        return true;
    }


}