<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 2/13/2019
 * Time: 5:43 PM
 */

namespace crocodicstudio\crudbooster\types\wysiwyg;

use crocodicstudio\crudbooster\types\TypesHook;
use Illuminate\Support\Str;

class Hook extends TypesHook
{

    /**
     * @param $row
     * @param $column WysiwygModel
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


    public function filterQuery($query, $column, $value)
    {
        $query->where($column->getFilterColumn(),"like","%".$value."%");
        return $query;
    }

}