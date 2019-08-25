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
use Illuminate\Database\Query\Builder;
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
     * @param string $sql_condition
     * @return $this
     */
    public function optionsFromTable($table, $value_option, $display_option, $sql_condition = null) {

        if(strpos($table,"App\Models")!==false) {
            $table = new $table();
            $table = $table::$tableName;
        }

        $table_primary_key = cb()->findPrimaryKey($table);

        $data = cb()->findAll($table, $sql_condition);

        $options = [];
        foreach ($data as $d) {
            $options[ $d->$value_option ] = $d->$display_option;
        }
        $data = columnSingleton()->getColumn($this->index);
        /** @var $data SelectTableModel */
        $data->setOptionsFromTable([
            "table"=>$table,
            "primary_key"=>$table_primary_key,
            "key_field"=>$value_option,
            "display_field"=>$display_option,
            "sql_condition"=>$sql_condition
        ]);
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