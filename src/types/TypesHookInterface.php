<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 2/13/2019
 * Time: 5:44 PM
 */

namespace crocodicstudio\crudbooster\types;


use crocodicstudio\crudbooster\models\ColumnModel;
use Illuminate\Database\Query\Builder;

interface TypesHookInterface
{

    /**
     * @param $value
     * @param mixed|ColumnModel $column
     * @return mixed
     */
    public function assignment($value, $column);

    public function indexRender($row, $column);

    public function detailRender($row, $column);

    /**
     * @param $query Builder
     * @param mixed|ColumnModel $column
     * @return mixed
     */
    public function query($query, $column);
}