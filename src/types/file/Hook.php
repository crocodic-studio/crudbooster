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
     * @param ImageModel $column
     * @return mixed|string
     * @throws \Exception
     */
    public function assignment($value, $column)
    {
        return $value;
    }

    public function detailRender($row, $column)
    {
        $column->setValue($row->{ $column->getField() });
        return view("types::file.detail",[
            'row'=>$row,
            'column'=>$column
        ]);
    }

    public function indexRender($row, $column)
    {
        return $this->detailRender($row, $column);
    }

}