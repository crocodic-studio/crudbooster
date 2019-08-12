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
use crocodicstudio\crudbooster\types\select_option\SelectOptionModel;
use Illuminate\Support\Facades\DB;

class SelectOption
{
    use DefaultOption, Join;

    public function options($data_options) {
        $data = columnSingleton()->getColumn($this->index);

        foreach($data_options as $key=>$option) {
            if(is_int($key)) {
                $data_options[$option] = $option;
            }else{
                $data_options[$key] = $option;
            }
        }

        /** @var $data SelectOptionModel */
        $data->setOptions($data_options);

        columnSingleton()->setColumn($this->index, $data);

        return $this;
    }
}