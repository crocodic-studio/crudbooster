<?php

namespace crocodicstudio\crudbooster\Modules\LogsModule\Listeners;

use crocodicstudio\crudbooster\CBCoreModule\CbUser;
use crocodicstudio\crudbooster\Modules\LogsModule\LogsRepository;

class CRUD
{
    public static function registerListeners()
    {
        self::delete();
        self::update();
        self::create();
        self::deleteImage();
    }

    private static function delete()
    {
        Event::listen('cb.dataDeleted', function (string $table, array $ids, string $time, CbUser $user) {
            $ids = implode(', ', $ids);
            self::insertLog("Data deleted from $table table with Ids: $ids at $time", $user->id);
        });
    }

    private static function insertLog($description, $userId = null)
    {
        LogsRepository::insertLog('crudbooster: '.$description, $userId ?: auth('cbAdmin')->id());
    }

    private static function create()
    {
        Event::listen('cb.dataInserted', function (string $table, int $id, string $time, CbUser $user) {
            self::insertLog("Data inserted into $table table with Id: $id at $time", $user->id);
        });
    }

    private static function update()
    {
        Event::listen('cb.dataUpdated', function (string $table, int $id, string $time, CbUser $user) {
            self::insertLog("Data updated within $table table with Id: $id at $time", $user->id);
        });
    }

    private static function deleteImage()
    {
        Event::listen('cb.imageDeleted', function (string $table, array $prop, string $time, CbUser $user) {
            $file = $prop['file'];
            $id = $prop['id'];
            self::insertLog("($file) Image deleted from $table table with Id: $id at $time", $user->id);
        });
    }
}