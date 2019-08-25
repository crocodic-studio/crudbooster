<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 1/26/2019
 * Time: 6:03 PM
 */

namespace crocodicstudio\crudbooster\controllers\scaffolding\singletons;


use crocodicstudio\crudbooster\models\ColumnModel;

class ColumnSingleton
{

    private $columns;
    private $joins;

    public function newColumns() {
        $this->columns = [];
    }

    public function addJoin($data)
    {
        $this->joins[md5(serialize($data))] = $data;
    }

    public function getJoin()
    {
        return $this->joins;
    }

    /**
     * @return mixed
     */
    public function getColumns()
    {
        return $this->columns;
    }

    public function valueAssignment($data_row = null) {
        foreach ($this->getColumns() as $index=>$column) {

            if (! $column->getName()) {
                continue;
            }

            /** @var ColumnModel $column */
            if($data_row) {
                $value = (isset($data_row->{$column->getField()}))?$data_row->{ $column->getField() }:null;
            }else{
                $value = request($column->getName());

                if(!$value && $column->getDefaultValue()) {
                    $value = $column->getDefaultValue();
                }

                $value = getTypeHook($column->getType())->assignment($value, $column);
            }

            $column->setValue($value);
            $this->setColumn($index, $column);
        }
    }

    public function getIndexColumns()
    {
        $data = $this->columns;
        $newData = [];
        foreach($data as $i=>$item) {
            /** @var ColumnModel $item */
            if($item->getShowIndex()) {
                $newData[] = $item;
            }
        }
        return $newData;
    }

    public function getAddEditColumns()
    {
        $data = $this->columns;
        $newData = [];
        foreach($data as $i=>$item) {
            /** @var ColumnModel $item */
            if($item->getShowAdd() || $item->getShowEdit()) {
                $newData[] = $item;
            }
        }
        return $newData;
    }

    public function getEditColumns()
    {
        $data = $this->columns;
        $newData = [];
        foreach($data as $i=>$item) {
            /** @var ColumnModel $item */
            if($item->getShowEdit()) {
                $newData[] = $item;
            }
        }
        return $newData;
    }

    public function getAddColumns()
    {
        $data = $this->columns;
        $newData = [];
        foreach($data as $i=>$item) {
            /** @var ColumnModel $item */
            if($item->getShowAdd()) {
                $newData[] = $item;
            }
        }
        return $newData;
    }

    public function getFilterableColumns()
    {
        $data = $this->columns;
        $newData = [];
        foreach($data as $i=>$item) {
            /** @var ColumnModel $item */
            if($item->getFilterable()) {
                $newData[] = $item;
            }
        }
        return $newData;
    }

    public function getDetailColumns()
    {
        $data = $this->columns;
        $newData = [];
        foreach($data as $i=>$item) {
            /** @var ColumnModel $item */
            if($item->getShowDetail()) {
                $newData[] = $item;
            }
        }
        return $newData;
    }

    public function getAssignmentData()
    {
        $data = [];
        foreach($this->columns as $column) {
            /** @var ColumnModel $column */

            if(is_array($column->getValue())) {
                foreach($column->getValue() as $key=>$val) {
                    $data[$key] = $val;
                }
            }else{
                $data[$column->getField()] = $column->getValue();
            }
        }
        return $data;
    }

    public function removeColumn($label_or_name)
    {
        $data = $this->getColumns();
        foreach($data as $i=>$d)
        {
            /** @var ColumnModel $d */
            if($d->getLabel() == $label_or_name || $d->getName() == $label_or_name) {
                unset($data[$i]);
            }
        }
        $this->columns = $data;
    }

    public function getColumnNameOnly()
    {
        $result = [];
        foreach($this->columns as $column) {
            /** @var ColumnModel $column */
            $result[] = $column->getName();
        }
        return $result;
    }

    /**
     * @param int $index
     * @param ColumnModel $value
     */
    public function setColumn($index, ColumnModel $value)
    {
        $this->columns[$index] = $value;
    }

    /**
     * @param int $index
     * @return ColumnModel
     */
    public function getColumn($index) {
        return $this->columns[$index];
    }

    public function setColumnArray($index, $key, $values)
    {
        $this->columns[$index][$key][] = $values;
    }
}