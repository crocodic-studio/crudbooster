<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 2/13/2019
 * Time: 5:43 PM
 */

namespace crocodicstudio\crudbooster\types\password;

use crocodicstudio\crudbooster\models\ColumnModel;
use crocodicstudio\crudbooster\types\TypesHook;
use Illuminate\Support\Facades\Hash;

class Hook extends TypesHook
{
    public function assignment($value, $column)
    {
        return Hash::make($value);
    }

    public function indexRender($row, $column)
    {
        return "*****";
    }

    public function detailRender($row, $column)
    {
        return "*****";
    }

}