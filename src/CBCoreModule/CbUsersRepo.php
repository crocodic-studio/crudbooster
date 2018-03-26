<?php

namespace crocodicstudio\crudbooster\CBCoreModule;

class CbUsersRepo
{
    public static function table()
    {
        return \DB::table(cbConfig('USER_TABLE'));
    }

    public static function find($id)
    {
        return self::table()->where('id', $id)->first();
    }

    public static function findByMail($email)
    {
        return self::table()->where('email', $email)->first();
    }

    public static function updateByMail($email, $data)
    {
        return self::table()->where('email', $email)->update($data);
    }
}