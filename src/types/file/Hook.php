<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 2/13/2019
 * Time: 5:43 PM
 */

namespace crocodicstudio\crudbooster\types\file;

use crocodicstudio\crudbooster\models\ColumnModel;
use crocodicstudio\crudbooster\types\TypesHook;

class Hook extends TypesHook
{
    /**
     * @param $value
     * @param ColumnModel $column
     * @return mixed|string
     * @throws \Exception
     */
    public function assignment($value, ColumnModel $column)
    {
        if(request()->hasFile($column->getName())) {
            return cb()->uploadFile($column->getName());
        }else{
            return null;
        }
    }

    public function detailRender($row, ColumnModel $column)
    {
        return view("types::file.detail",[
            'row'=>$row,
            'column'=>$column
        ]);
    }

    public function indexRender($row, ColumnModel $column)
    {
        return view("types::file.detail",[
            'row'=>$row,
            'column'=>$column
        ]);
    }

}