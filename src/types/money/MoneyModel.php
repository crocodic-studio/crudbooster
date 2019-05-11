<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 4/21/2019
 * Time: 10:51 PM
 */

namespace crocodicstudio\crudbooster\types\money;

use crocodicstudio\crudbooster\models\ColumnModel;

class MoneyModel extends ColumnModel
{

    private $precision;
    private $thousands;
    private $decimal;
    private $prefix;

    /**
     * @return mixed
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @param mixed $prefix
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }


    /**
     * @return mixed
     */
    public function getPrecision()
    {
        return $this->precision;
    }

    /**
     * @param mixed $precision
     */
    public function setPrecision($precision)
    {
        $this->precision = $precision;
    }

    /**
     * @return mixed
     */
    public function getThousands()
    {
        return $this->thousands;
    }

    /**
     * @param mixed $thousands
     */
    public function setThousands($thousands)
    {
        $this->thousands = $thousands;
    }

    /**
     * @return mixed
     */
    public function getDecimal()
    {
        return $this->decimal;
    }

    /**
     * @param mixed $decimal
     */
    public function setDecimal($decimal)
    {
        $this->decimal = $decimal;
    }



}