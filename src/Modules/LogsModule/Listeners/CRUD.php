<?php

namespace crocodicstudio\crudbooster\Modules\LogsModule\Listeners;

use crocodicstudio\crudbooster\CBCoreModule\CbUser;
use crocodicstudio\crudbooster\Modules\LogsModule\LogsRepository;

class CRUD
{
    public static function registerListeners()
    {
        self::delete();
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
}