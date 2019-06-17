<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 1/26/2019
 * Time: 6:00 PM
 */

namespace crocodicstudio\crudbooster\types;

use Crocodicstudio\Cbmodel\Core\Model;
use crocodicstudio\crudbooster\controllers\scaffolding\traits\DefaultOption;
use crocodicstudio\crudbooster\controllers\scaffolding\traits\Join;
use crocodicstudio\crudbooster\types\checkbox\CheckboxModel;
use Illuminate\Support\Facades\DB;

class Checkbox
{
    use DefaultOption, Join;

    /**
     * @param array $options Array containing key and value
     * @return $this
     * @example options(['foo'=>'bar'])
     */
    public function options($options) {
        /** @var CheckboxModel $data */
        $data = columnSingleton()->getColumn($this->index);
        $data->setCheckboxOptions($options);

        columnSingleton()->setColumn($this->index, $data);

        return $this;
    }

    /**
     * @param $table string|Model
     * @param $key_field string
     * @param $display_field string
     * @param $SQLCondition string|callable
     */
    public function optionsFromTable($table, $key_field, $display_field, $SQLCondition = null) {
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
            $options[ $d->$key_field ] = $d->$display_field;
        }
        $this->options($options);
    }
}