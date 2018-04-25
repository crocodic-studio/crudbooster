<?php

namespace crocodicstudio\crudbooster\CBCoreModule\Index;

class FilterIndexRows
{
    /**
     * @param $result
     * @param $filterColumn
     * @return bool
     */
    public function filterIndexRows($result, $filterColumn)
    {
        $filterIsOrderby = false;
        $this->applyWhere($result, $filterColumn);

        foreach ($filterColumn as $key => $fc) {
            $value = @$fc['value'];
            $type = @$fc['type'];
            $sorting = @$fc['sorting'];

            $filterIsOrderby = $this->applySorting($result, $sorting, $key);

            $this->applyBetween($result, $type, $key, $value);
        }

        return $filterIsOrderby;
    }

    /**
     * @param $result
     * @param $filterColumn
     */
    private function applyWhere($result, $filterColumn)
    {
        $filterColumn = $this->filterFalsyValues($filterColumn);

        $result->where(function ($query) use ($filterColumn) {
            foreach ($filterColumn as $key => $fc) {

                $value = @$fc['value'];
                $type = @$fc['type'];

                if ($type == 'empty') {
                    $query->whereNull($key)->orWhere($key, '');
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
                        if (! empty($value)) {
                            $query->whereIn($key, $value);
                        }
                        break;
                }
            }
        });
    }

    /**
     * @param $result
     * @param $sorting
     * @param $key
     * @return bool
     */
    private function applySorting($result, $sorting, $key)
    {
        $filter_is_orderby = false;
        if ($sorting != '' && $key) {
            $result->orderby($key, $sorting);
            $filter_is_orderby = true;
        }

        return $filter_is_orderby;
    }

    /**
     * @param $result
     * @param $type
     * @param $key
     * @param $value
     */
    private function applyBetween($result, $type, $key, $value)
    {
        if ($type == 'between' && $key && $value) {
            $result->whereBetween($key, $value);
        }
    }

    /**
     * @param $filterColumn
     * @return array
     */
    private function filterFalsyValues($filterColumn)
    {
        return array_filter($filterColumn, function ($fc) {
            $value = @$fc['value'];
            $type = @$fc['type'];

            if (($type == 'between') || ! $value || ! $type) {
                return false;
            }

            return true;
        });
    }
}