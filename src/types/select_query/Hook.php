<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 2/13/2019
 * Time: 5:43 PM
 */

namespace crocodicstudio\crudbooster\types\select_query;

use crocodicstudio\crudbooster\types\select_query\SelectQueryModel;
use crocodicstudio\crudbooster\types\TypesHook;

class Hook extends TypesHook
{
    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @param SelectQueryModel $column
     * @return mixed|void
     */
    public function query($query, $column)
    {

        return $query;
    }

    /**
     * @param $row
     * @param $column SelectQueryModel
     */
    public function indexRender($row, $column)
    {
        $option = $column->getOptions();
        $key = $row->{ $column->getField() };
        return @$option[ $key ];
    }

    public function detailRender($row, $column)
    {
        return $this->indexRender($row, $column);
    }

}