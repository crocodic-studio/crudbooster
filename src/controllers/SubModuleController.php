<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 8/15/2019
 * Time: 11:47 PM
 */

namespace crocodicstudio\crudbooster\controllers;

use Illuminate\Support\Facades\Cache;

trait SubModuleController
{

    public function getSubModule($subModuleKey) {
        if(!module()->canBrowse()) return cb()->redirect(cb()->getAdminUrl(),cbLang("you_dont_have_privilege_to_this_area"));
        if(!verifyReferalUrl()) return cb()->redirect(module()->url(),"It looks like your url is incorrect");

        if($subModule = Cache::get("subModule".$subModuleKey)) {
            $foreignKey = $subModule['foreignKey'];
            $foreignValue = $subModule['foreignValue'];

            $query = $this->repository();
            $query->where($foreignKey, $foreignValue);
            $result = $query->paginate( request("limit")?:$this->data["limit"] );
            $data['result'] = $result;

            $data['additionalHeaderTitle'] = $subModule['parentTitle'];
            $data['additionalHeaderContent'] = $subModule['info'];
            $data['subModuleKey'] = $subModuleKey;

            return view("crudbooster::module.index.index", array_merge($data, $this->data));
        }

        return cb()->redirect(module()->url(),"It looks like your url is incorrect");
    }

}