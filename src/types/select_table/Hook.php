<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 2/13/2019
 * Time: 5:43 PM
 */

namespace crocodicstudio\crudbooster\types\select_table;

use crocodicstudio\crudbooster\types\TypesHook;

class Hook extends TypesHook
{
    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @param SelectTableModel $column
     * @return mixed|void
     */
    public function query($query, $column)
    {
        if($option = $column->getOptionsFromTable()) {
            $query->leftjoin($option["table"],$option["table"].'.'.$option["primary_key"],"=", $column->getField());
            $query->addSelect($option['table'].'.'.$option['display_field'].' as '.$option['table'].'_'.$option['display_field']);
        }
        return $query;
    }

    /**
     * @param $row
     * @param $column SelectModel
     */
    public function indexRender($row, $column)
    {
        if($column->getOptionsFromTable()) {
            $option = $column->getOptionsFromTable();
            return $row->{ $option['table'].'_'.$option['display_field'] };
        }else{
            $option = $column->getOptions();
            $key = $row->{ $column->getField() };
            return @$option[ $key ];
        }
    }

    public function detailRender($row, $column)
    {
        return $this->indexRender($row, $column);
    }

}