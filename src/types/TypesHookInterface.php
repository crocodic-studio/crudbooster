<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 2/13/2019
 * Time: 5:44 PM
 */

namespace crocodicstudio\crudbooster\types;


use crocodicstudio\crudbooster\models\ColumnModel;

interface TypesHookInterface
{

    /**
     * @param $value
     * @param ColumnModel $column
     * @return mixed
     */
    public function assignment($value, ColumnModel $column);

    public function indexRender($row, ColumnModel $column);

    public function detailRender($row, ColumnModel $column);
}