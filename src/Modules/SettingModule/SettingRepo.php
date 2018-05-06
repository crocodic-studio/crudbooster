<?php

namespace crocodicstudio\crudbooster\Modules\SettingModule;

class SettingRepo
{
    public static function getSetting($name)
    {
        return cache()->rememberForever('crudbooster_setting_'.$name, function () use($name) {
            return \DB::table('cms_settings')->where('name', $name)->first()->content;
        });
    }
}