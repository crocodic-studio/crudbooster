<?php

namespace crocodicstudio\crudbooster\CBCoreModule;

class CbUsersRepo
{
    public static function find($id)
    {
        return self::where(['id' => $id])->first();
    }

    public static function where($conditions)
    {
        return self::table()->where($conditions);
    }

    public static function table()
    {
        return \DB::table(cbConfig('USER_TABLE'));
    }

    public static function findByMail($email)
    {
        return self::where(['email' => $email])->first();
    }

    public static function updateByMail($email, $data)
    {
        return self::where(['email' => $email])->update($data);
    }
}