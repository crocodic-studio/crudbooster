<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 4/21/2019
 * Time: 10:51 PM
 */

namespace crocodicstudio\crudbooster\types\select_option;

use crocodicstudio\crudbooster\models\ColumnModel;

class SelectOptionModel extends ColumnModel
{

    private $options;

    /**
     * @return mixed
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param mixed $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }


}