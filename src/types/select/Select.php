<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 1/26/2019
 * Time: 6:00 PM
 */

namespace crocodicstudio\crudbooster\types;

use crocodicstudio\crudbooster\controllers\scaffolding\traits\DefaultOption;
use crocodicstudio\crudbooster\controllers\scaffolding\traits\Join;
use crocodicstudio\crudbooster\models\ColumnModel;
use crocodicstudio\crudbooster\types\select\SelectModel;
use Illuminate\Support\Facades\DB;

class Select
{
    use DefaultOption, Join;

    /**
     * @param string $field_name
     * @return $this
     */
    public function foreignKey($field_name)
    {
        $data = columnSingleton()->getColumn($this->index);
        /** @var SelectModel $data */
        $data->setForeignKey($field_name);
        columnSingleton()->setColumn($this->index, $data);

        return $this;
    }

    /**
     * @param $table string
     * @param $key_field string
     * @param $display_field string
     * @param $SQLCondition string|callable
     * @return $this
     */
    public function optionsFromTable($table, $key_field, $display_field, $SQLCondition = null) {
        $data = DB::table($table);
        if($SQLCondition && is_callable($SQLCondition)) {
            $data = call_user_func($SQLCondition, $data);
        }elseif ($SQLCondition && is_string($SQLCondition)) {
            $data->whereRaw($SQLCondition);
        }
        $data = $data->get();
        $options = [];
        foreach ($data as $d) {
            $options[ $d->$key_field ] = $d->$display_field;
        }
        $data = columnSingleton()->getColumn($this->index);
        /** @var $data SelectModel */
        $data->setOptionsFromTable(["table"=>$table,"key_field"=>$key_field,"display_field"=>$display_field,"sql_condition"=>$SQLCondition]);
        columnSingleton()->setColumn($this->index, $data);

        $this->options($options);

        return $this;
    }

    public function options($data_options) {
        $data = columnSingleton()->getColumn($this->index);
        /** @var $data SelectModel */
        $data->setOptions($data_options);

        columnSingleton()->setColumn($this->index, $data);

        return $this;
    }
}