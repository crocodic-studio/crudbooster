<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 4/21/2019
 * Time: 10:51 PM
 */

namespace crocodicstudio\crudbooster\types\email;

use crocodicstudio\crudbooster\models\ColumnModel;

class EmailModel extends ColumnModel
{

    private $max_length;

    /**
     * @return mixed
     */
    public function getMaxLength()
    {
        return $this->max_length;
    }

    /**
     * @param mixed $max_length
     */
    public function setMaxLength($max_length): void
    {
        $this->max_length = $max_length;
    }


}