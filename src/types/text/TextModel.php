<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 4/21/2019
 * Time: 10:51 PM
 */

namespace crocodicstudio\crudbooster\types\text;

use crocodicstudio\crudbooster\models\ColumnModel;

class TextModel extends ColumnModel
{

    private $max_length;
    private $min_length;
    private $limit;

    /**
     * @return mixed
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param mixed $limit
     */
    public function setLimit($limit): void
    {
        $this->limit = $limit;
    }


    /**
     * @return mixed
     */
    public function getMaxLength()
    {
        return $this->max_length;
    }

    /**
     * @param mixed $max_length
     * @return TextModel
     */
    public function setMaxLength($max_length)
    {
        $this->max_length = $max_length;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMinLength()
    {
        return $this->min_length;
    }

    /**
     * @param mixed $min_length
     * @return TextModel
     */
    public function setMinLength($min_length)
    {
        $this->min_length = $min_length;
        return $this;
    }

}