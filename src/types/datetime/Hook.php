<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 2/13/2019
 * Time: 5:43 PM
 */

namespace crocodicstudio\crudbooster\types\datetime;

use crocodicstudio\crudbooster\types\TypesHook;

class Hook extends TypesHook
{

    /**
     * @param $row
     * @param $column DatetimeModel
     * @return mixed
     */
    public function indexRender($row, $column)
    {
        if($column->getFormat()) {
            return date($column->getFormat(), strtotime($row->{$column->getField()}));
        }else{
            return $row->{$column->getField()};
        }
    }

    public function assignment($value, $column)
    {
		if($column->getFormat()) {
			return date_create_from_format($column->getFormat(), $value)->format('Y-m-d H:i:s');
		} else {
			return $value;
		}
    }
    
    public function detailRender($row, $column)
    {
        return $this->indexRender($row, $column);
    }

    public function filterQuery($query, $column, $value)
    {
        $start = sanitizeXSS($value['start']);
        $end = sanitizeXSS($value['end']);
        if($start && $end) {
            $start = date("Y-m-d H:i:s", strtotime($start));
            $end = date("Y-m-d H:i:s", strtotime($end));
            $query->whereBetween($column->getFilterColumn(), [$start, $end]);
        }
        return $query;
    }
}
