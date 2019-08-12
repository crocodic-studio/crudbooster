<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 4/21/2019
 * Time: 10:51 PM
 */

namespace crocodicstudio\crudbooster\types\file;

use crocodicstudio\crudbooster\models\ColumnModel;

class FileModel extends ColumnModel
{


    private $encrypt;

    /**
     * @return mixed
     */
    public function getEncrypt()
    {
        return $this->encrypt;
    }

    /**
     * @param boolean $encrypt
     */
    public function setEncrypt($encrypt): void
    {
        $this->encrypt = $encrypt;
    }


}