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
use crocodicstudio\crudbooster\types\checkbox\CheckboxModel;

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
}