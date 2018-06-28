<?php

namespace Crocodicstudio\Crudbooster\Modules\SettingModule;

class SettingRepo
{
    public static function getSetting($name)
    {
        return cache()->rememberForever('crudbooster_setting_'.$name, function () use($name) {
            return self::table()->where('name', $name)->first()->content;
        });
    }

    public static function resetSettings($data)
    {
        foreach ($data as $row) {
            $count = self::table()->where('name', $row['name'])->count();
            if (! $count) {
                self::table()->insert($row);
                continue;
            }
            if ($count > 1) {
                $rowId = self::table()->where('name', $row['name'])->orderby('id', 'asc')->first()->id;
                self::table()->where('name', $row['name'])->where('id', '!=', $rowId)->delete();
            }
        }
    }

    /**
     * @return mixed
     */
    private static function table()
    {
        return \DB::table('cms_settings');
    }
}