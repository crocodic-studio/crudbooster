<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 2/13/2019
 * Time: 5:44 PM
 */

namespace crocodicstudio\crudbooster\types;


use crocodicstudio\crudbooster\models\ColumnModel;

class TypesHook implements TypesHookInterface
{

    /**
     * @param $value
     * @param mixed|ColumnModel $column
     * @return mixed
     */
    public function assignment($value, $column)
    {
        return $value;
    }

    public function indexRender($row, $column)
    {
        return (isset($row->{$column->getField()}))?$row->{ $column->getField() }:null;
    }

    public function detailRender($row, $column)
    {
        return (isset($row->{$column->getField()}))?$row->{ $column->getField() }:null;
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @param mixed|ColumnModel $column
     * @return mixed|void
     */
    public function query($query, $column)
    {
        return $query;
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @param mixed|ColumnModel $column
     * @param string $value
     * @return mixed|void
     */
    public function filterQuery($query, $column, $value) {
        $query->where($column->getFilterColumn(), $value);
        return $query;
    }
}