<?php


namespace crocodicstudio\crudbooster\helpers;


class CBRouter
{

    public static function getCBControllerFiles() {
        $controllers = glob(__DIR__.'/../controllers/*.php');
        $result = [];
        foreach ($controllers as $file) {
            $result[] = str_replace('.php', '', basename($file));
        }
        return $result;
    }

}