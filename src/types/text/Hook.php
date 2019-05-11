<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 2/13/2019
 * Time: 5:43 PM
 */

namespace crocodicstudio\crudbooster\types\text;

use crocodicstudio\crudbooster\types\TypesHook;

class Hook extends TypesHook
{

    /**
     * @param $row
     * @param $column TextModel
     * @return string
     */
    public function indexRender($row, $column)
    {
        $value = trim(strip_tags($row->{ $column->getField() }));
        if($column->getLimit()) {
            $value = Str::limit($value, $column->getLimit());
        }
        return $value;
    }

}