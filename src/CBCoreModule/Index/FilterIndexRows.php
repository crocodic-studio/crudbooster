<?php

namespace crocodicstudio\crudbooster\CBCoreModule\Index;

class FilterIndexRows
{
    /**
     * @param $result
     * @param $filterColumn
     * @return bool
     */
    function filterIndexRows($result, $filterColumn)
    {
        $filter_is_orderby = false;
        $result->where(function ($query) use ($filterColumn) {
            foreach ($filterColumn as $key => $fc) {

                $value = @$fc['value'];
                $type = @$fc['type'];

                if ($type == 'empty') {
                    $query->whereNull($key)->orWhere($key, '');
                    continue;
                }

                if ($type == 'between') {
                    continue;
                }
                if (!$value || !$key || !$type) {
                    continue;
                }
                switch ($type) {
                    default:
                        $query->where($key, $type, $value);
                        break;
                    case 'like':
                    case 'not like':
                        $query->where($key, $type, '%'.$value.'%');
                        break;
                    case 'in':
                    case 'not in':
                        $value = explode(',', $value);
                        if ($value) {
                            $query->whereIn($key, $value);
                        }
                        break;
                }
            }
        });

        foreach ($filterColumn as $key => $fc) {
            $value = @$fc['value'];
            $type = @$fc['type'];
            $sorting = @$fc['sorting'];

            if ($sorting != '' && $key) {
                $result->orderby($key, $sorting);
                $filter_is_orderby = true;
            }

            if ($type == 'between' && $key && $value) {
                $result->whereBetween($key, $value);
            }
        }

        return $filter_is_orderby;
    }
}