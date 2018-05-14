<?php

namespace crocodicstudio\crudbooster\CBCoreModule;

class CbUsersRepo
{
    public function find($id)
    {
        return $this->where(['id' => $id])->first();
    }

    public function where($conditions)
    {
        return $this->table()->where($conditions);
    }

    public function table()
    {
        return \DB::table(cbConfig('USER_TABLE'));
    }

    public function findByMail($email)
    {
        return $this->where(['email' => $email])->first();
    }

    public function updateByMail($email, $data)
    {
        return $this->where(['email' => $email])->update($data);
    }

    public function insert($data)
    {
        return $this->table()->insert($data);
    }
}