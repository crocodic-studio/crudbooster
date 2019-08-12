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
use crocodicstudio\crudbooster\types\select_table\SelectTableModel;
use Illuminate\Support\Facades\DB;

class SelectTable
{
    use DefaultOption, Join;

    /**
     * @param string $field_name
     * @return $this
     */
    public function foreignKey($field_name)
    {
        $data = columnSingleton()->getColumn($this->index);
        /** @var SelectTableModel $data */
        $data->setForeignKey($field_name);
        columnSingleton()->setColumn($this->index, $data);

        return $this;
    }

    /**
     * @param $table string|Model
     * @param $value_option string
     * @param $display_option string
     * @param $SQLCondition string|callable DB Query Builder|SQL Raw
     * @return $this
     */
    public function optionsFromTable($table, $value_option, $display_option, $SQLCondition = null) {

        if(strpos($table,"App\Models")!==false) {
            $table = new $table();
            $table = $table::$tableName;
        }

        $data = DB::table($table);
        if($SQLCondition && is_callable($SQLCondition)) {
            $data = call_user_func($SQLCondition, $data);
        }elseif ($SQLCondition && is_string($SQLCondition)) {
            $data->whereRaw($SQLCondition);
        }
        $data = $data->get();
        $options = [];
        foreach ($data as $d) {
            $options[ $d->$value_option ] = $d->$display_option;
        }
        $data = columnSingleton()->getColumn($this->index);
        /** @var $data SelectTableModel */
        $data->setOptionsFromTable(["table"=>$table,"key_field"=>$value_option,"display_field"=>$display_option,"sql_condition"=>$SQLCondition]);
        columnSingleton()->setColumn($this->index, $data);

        $this->options($options);

        return $this;
    }

    private function options($data_options) {
        $data = columnSingleton()->getColumn($this->index);
        /** @var $data SelectTableModel */
        $data->setOptions($data_options);

        columnSingleton()->setColumn($this->index, $data);

        return $this;
    }
}