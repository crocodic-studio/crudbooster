<?php

namespace crocodicstudio\crudbooster\Modules\SettingModule;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SettingRepo
{
    public static function getSetting($name)
    {
        $cacheKey = 'setting_'.$name;

        if (Cache::has($cacheKey)) {
            return Cache::get('setting_'.$name);
        }

        $content = DB::table('cms_settings')->where('name', $name)->first()->content;
        Cache::forever('setting_'.$name, $content);

        return $content;
    }
}