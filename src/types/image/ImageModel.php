<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 4/21/2019
 * Time: 10:51 PM
 */

namespace crocodicstudio\crudbooster\types\image;

use crocodicstudio\crudbooster\models\ColumnModel;

class ImageModel extends ColumnModel
{

    private $encrypt;
    private $resize_width;
    private $resize_height;

    /**
     * @return mixed
     */
    public function getEncrypt()
    {
        return $this->encrypt;
    }

    /**
     * @param mixed $encrypt
     */
    public function setEncrypt($encrypt)
    {
        $this->encrypt = $encrypt;
    }

    /**
     * @return mixed
     */
    public function getResizeWidth()
    {
        return $this->resize_width;
    }

    /**
     * @param mixed $resize_width
     */
    public function setResizeWidth($resize_width)
    {
        $this->resize_width = $resize_width;
    }

    /**
     * @return mixed
     */
    public function getResizeHeight()
    {
        return $this->resize_height;
    }

    /**
     * @param mixed $resize_height
     */
    public function setResizeHeight($resize_height)
    {
        $this->resize_height = $resize_height;
    }



}