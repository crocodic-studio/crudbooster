<?php

namespace crocodicstudio\crudbooster\Modules\ApiGeneratorModule;

use Illuminate\Support\Facades\DB;

class ApiKeysRepository
{
    public function incrementHit($serverSecret)
    {
        return $this->where(['secretkey' => $serverSecret])->increment('hit');
    }

    public function where($where)
    {
        return $this->table()->where($where);
    }

    private static function table()
    {
        return DB::table('cms_apikey');
    }

    public function getSecretKeys()
    {
        return $this->where(['status' => 'active'])->pluck('secretkey');
    }

    public function get()
    {
        return $this->table()->get();
    }

    public function deleteById($id)
    {
        return $this->where(['id' => $id])->delete();
    }

    public function updateById($status, $id)
    {
        return $this->where(['id' => $id])->update(['status' => $status]);
    }

    public function insertGetId($token)
    {
        return $this->table()->insertGetId([
            'secretkey' => $token,
            'created_at' => YmdHis(),
            'status' => 'active',
            'hit' => 0,
        ]);
    }
}