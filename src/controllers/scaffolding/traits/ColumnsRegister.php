<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 1/26/2019
 * Time: 5:40 PM
 */

namespace crocodicstudio\crudbooster\controllers\scaffolding\traits;

use crocodicstudio\crudbooster\models\ColumnModel;
use crocodicstudio\crudbooster\types\Checkbox;
use crocodicstudio\crudbooster\types\checkbox\CheckboxModel;
use crocodicstudio\crudbooster\types\Image;
use crocodicstudio\crudbooster\types\image\ImageModel;
use crocodicstudio\crudbooster\types\Password;
use crocodicstudio\crudbooster\types\password\PasswordModel;
use crocodicstudio\crudbooster\types\text\TextAreaModel;
use crocodicstudio\crudbooster\types\TextArea;
use crocodicstudio\crudbooster\types\upload_image\UploadImageModel;
use crocodicstudio\crudbooster\types\UploadImage;

trait ColumnsRegister
{
    use ColumnsBasic;

    public function addText($label, $name = null, $field_to_save = null)
    {
        $this->index++;

        $data = new ColumnModel();
        $data = $this->setDefaultModelValue($data);
        $data->setLabel($label);
        $data->setName($this->name($label,$name));
        $data->setField($field_to_save?:$this->name($label, $name));
        $data->setType("text");

        columnSingleton()->setColumn($this->index, $data);

        return (new Text($this->index));
    }

    public function addCheckbox($label, $name = null, $field_to_save = null)
    {
        $this->index++;

        $data = new CheckboxModel();
        $data = $this->setDefaultModelValue($data);
        $data->setLabel($label);
        $data->setName($this->name($label,$name));
        $data->setField($field_to_save?:$this->name($label, $name));
        $data->setType("checkbox");

        columnSingleton()->setColumn($this->index, $data);

        return (new Checkbox($this->index));
    }

    public function addPassword($label, $name = null, $field_to_save = null)
    {
        $this->index++;

        $data = new PasswordModel();
        $data = $this->setDefaultModelValue($data);
        $data->setLabel($label);
        $data->setName($this->name($label,$name));
        $data->setField($field_to_save?:$this->name($label, $name));
        $data->setType("password");
        $data->setShowDetail(false);
        $data->setShowIndex(false);

        columnSingleton()->setColumn($this->index, $data);

        return (new Password($this->index));
    }

    public function addImage($label, $name = null, $field_to_save = null)
    {
        $this->index++;

        $data = new ImageModel();
        $data = $this->setDefaultModelValue($data);
        $data->setLabel($label);
        $data->setName($this->name($label,$name));
        $data->setField($field_to_save?:$this->name($label, $name));
        $data->setType("image");

        columnSingleton()->setColumn($this->index, $data);

        return (new Image($this->index));
    }

    public function addTextArea($label, $name = null, $field_to_save = null)
    {
        $this->index++;

        $data = new TextAreaModel();
        $data = $this->setDefaultModelValue($data);
        $data->setLabel($label);
        $data->setName($this->name($label,$name));
        $data->setField($field_to_save?:$this->name($label, $name));
        $data->setType("text_area");

        columnSingleton()->setColumn($this->index, $data);

        return (new TextArea($this->index));
    }

    public function addSelect($label, $name = null, $field_to_save = null)
    {
        $this->index++;

        $data = new ColumnModel();
        $data = $this->setDefaultModelValue($data);
        $data->setLabel($label);
        $data->setName($this->name($label,$name));
        $data->setField($field_to_save?:$this->name($label, $name));
        $data->setType("select");

        columnSingleton()->setColumn($this->index, $data);

        return (new Select($this->index));
    }


}