<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 4/21/2019
 * Time: 10:51 PM
 */

namespace crocodicstudio\crudbooster\types\select;

use crocodicstudio\crudbooster\models\ColumnModel;

class SelectModel extends ColumnModel
{

    private $options;
    private $options_from_table;

    /**
     * @return mixed
     */
    public function getOptionsFromTable()
    {
        return $this->options_from_table;
    }

    /**
     * @param mixed $options_from_table
     */
    public function setOptionsFromTable($options_from_table)
    {
        $this->options_from_table = $options_from_table;
    }



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
    public function setOptions($options): void
    {
        $this->options = $options;
    }


}