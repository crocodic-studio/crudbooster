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
use crocodicstudio\crudbooster\types\image\ImageModel;

class Image
{
    use DefaultOption, Join;

    public function encrypt($boolean)
    {
        $data = columnSingleton()->getColumn($this->index);
        $data->setEncrypt($boolean);
        columnSingleton()->setColumn($this->index, $data);
        return $this;
    }

    public function resize($width, $height = null)
    {
        $data = columnSingleton()->getColumn($this->index);
        /** @var ImageModel $data */
        $data->setResizeWidth($width);
        $data->setResizeHeight($height);
        $data->setHelp("File format support jpg,png,gif. Image resolution $width x $height px");
        columnSingleton()->setColumn($this->index, $data);
        return $this;
    }

}