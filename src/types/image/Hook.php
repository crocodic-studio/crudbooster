<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 2/13/2019
 * Time: 5:43 PM
 */

namespace crocodicstudio\crudbooster\types\image;

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
        // Direct return value because its been uploaded on client side
        $ext = strtolower(pathinfo($value, PATHINFO_EXTENSION));
        if(in_array($ext, cbConfig("UPLOAD_IMAGE_EXTENSION_ALLOWED"))) {
            return $value;
        }
    }

    public function detailRender($row, $column)
    {
        $column->setValue($row->{ $column->getField() });
        return view("types::image.detail",[
            'row'=>$row,
            'column'=>$column
        ]);
    }

    public function indexRender($row, $column)
    {
        $column->setValue($row->{ $column->getField() });
        return view("types::image.index",[
            'row'=>$row,
            'column'=>$column
        ]);
    }

}