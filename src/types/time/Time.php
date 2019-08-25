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

class Time
{
    use DefaultOption, Join;

    public function format($formatString = "H:i") {
        $data = columnSingleton()->getColumn($this->index);
        /**
         * @var TimeModel $data
         */
        $data->setFormat($formatString);
        columnSingleton()->setColumn($this->index, $data);

        return $this;
    }

}