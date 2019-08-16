<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 8/15/2019
 * Time: 11:48 PM
 */

namespace crocodicstudio\crudbooster\controllers\traits;

use Illuminate\Support\Facades\Cache;

trait ColumnIntervention
{
    /**
     * ColumnIntervention method is to make additional custom column.
     * We can change the user column after cbInit() called.
     */
    public function columnIntervention()
    {
        if(request("sub_module") && Cache::has("subModule".request("sub_module")) && cb()->getCurrentMethod() != "getDetail") {
            /*
             * If there is sub module, the column that has same name with foreign key should be remove
             * And change to hidden. So we can save the foreign Key id from the parent module.
             */
            $subModule = Cache::get("subModule".request("sub_module"));
            $this->removeColumn($subModule["foreignKey"])
                ->addHidden($subModule["foreignKey"],$subModule['foreignKey'])
                ->defaultValue($subModule['foreignValue']);
        }
    }

}