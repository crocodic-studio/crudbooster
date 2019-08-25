<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 2/13/2019
 * Time: 5:43 PM
 */

namespace crocodicstudio\crudbooster\types\money;

use crocodicstudio\crudbooster\types\TypesHook;


class Hook extends TypesHook
{

    /**
     * @param $value
     * @param $column MoneyModel
     * @return mixed|void
     */
    public function assignment($value, $column)
    {
        $value = str_replace($column->getThousands()?:",","", $value);
        $value = str_replace($column->getDecimal()?:".",".",$value);
        return $value;
    }

    public function indexRender($row, $column)
    {
        $value = $row->{$column->getField()};
        $prefix = ($column->getPrefix())?:"";
        return $prefix.number_format($value, $column->getPrecision()?:0, $column->getDecimal()?:".", $column->getThousands()?:",");
    }

    public function filterQuery($query, $column, $value)
    {
        $start = sanitizeXSS($value['start']);
        $end = sanitizeXSS($value['end']);
        if($start && $end) {
            $query->whereBetween($column->getFilterColumn(), [$start, $end]);
        }
        return $query;
    }

    public function detailRender($row, $column)
    {
        return $this->indexRender($row, $column);
    }
}