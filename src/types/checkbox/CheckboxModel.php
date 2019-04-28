<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 4/21/2019
 * Time: 10:51 PM
 */

namespace crocodicstudio\crudbooster\types\checkbox;

use crocodicstudio\crudbooster\models\ColumnModel;

class CheckboxModel extends ColumnModel
{

    private $checkbox_options;

    /**
     * @return mixed
     */
    public function getCheckboxOptions()
    {
        return $this->checkbox_options;
    }

    /**
     * @param mixed $checkbox_options
     * @return CheckboxModel
     */
    public function setCheckboxOptions($checkbox_options)
    {
        $this->checkbox_options = $checkbox_options;
        return $this;
    }

}