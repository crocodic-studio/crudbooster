<?php

namespace crocodicstudio\crudbooster\CBCoreModule\Index;

class FilterIndexRows
{
    private $query;

    /**
     * @param $result
     * @param $filterColumn
     * @return bool
     */
    public function filterIndexRows($result, $filterColumn)
    {
        $this->query = $result;
        $filterIsOrderby = false;
        $this->applyWhere($filterColumn);

        foreach ($filterColumn as $key => $fc) {
            $filterIsOrderby = $this->applySorting($key, array_get($fc, 'sorting'));
            $this->applyBetween(array_get($fc, 'type'), $key, array_get($fc, 'value'));
        }

        return $filterIsOrderby;
    }

    /**
     * @param $filterColumn
     */
    private function applyWhere($filterColumn)
    {
        $filterColumn = $this->filterFalsyValues($filterColumn);

        $this->query->where(function ($query) use ($filterColumn) {
            foreach ($filterColumn as $key => $fc) {

                $value = array_get($fc, 'value');
                $type = array_get($fc, 'type');

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
     * @param $sorting
     * @param $key
     * @return bool
     */
    private function applySorting($key, $sorting)
    {
        if ($sorting != '' && $key) {
            $this->query->orderby($key, $sorting);
            return true;
        }

        return false;
    }

    /**
     * @param $type
     * @param $key
     * @param $limits
     */
    private function applyBetween($type, $key, $limits)
    {
        if ($type == 'between' && $key && is_array($limits)) {
            $this->query->whereBetween($key, $limits);
        }
    }

    /**
     * @param $filterColumn
     * @return array
     */
    private function filterFalsyValues($filterColumn)
    {
        return array_filter($filterColumn, function ($fc) {
            $value = array_get($fc, 'value');
            $type = array_get($fc, 'type');

            if (($type == 'between') || ! $value || ! $type) {
                return false;
            }

            return true;
        });
    }
}