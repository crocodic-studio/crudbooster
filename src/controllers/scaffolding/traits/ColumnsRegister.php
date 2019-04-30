<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 1/26/2019
 * Time: 5:40 PM
 */

namespace crocodicstudio\crudbooster\controllers\scaffolding\traits;

use crocodicstudio\crudbooster\models\ColumnModel;
use crocodicstudio\crudbooster\types\Custom;
use crocodicstudio\crudbooster\types\custom\CustomModel;
use crocodicstudio\crudbooster\types\Date;
use crocodicstudio\crudbooster\types\date\DateModel;
use crocodicstudio\crudbooster\types\Datetime;
use crocodicstudio\crudbooster\types\datetime\DatetimeModel;
use crocodicstudio\crudbooster\types\Email;
use crocodicstudio\crudbooster\types\email\EmailModel;
use crocodicstudio\crudbooster\types\File;
use crocodicstudio\crudbooster\types\file\FileModel;
use crocodicstudio\crudbooster\types\Hidden;
use crocodicstudio\crudbooster\types\image\HiddenModel;
use crocodicstudio\crudbooster\types\Number;
use crocodicstudio\crudbooster\types\number\NumberModel;
use crocodicstudio\crudbooster\types\Radio;
use crocodicstudio\crudbooster\types\radio\RadioModel;
use crocodicstudio\crudbooster\types\Select;
use crocodicstudio\crudbooster\types\select\SelectModel;
use crocodicstudio\crudbooster\types\Text;
use crocodicstudio\crudbooster\types\Checkbox;
use crocodicstudio\crudbooster\types\checkbox\CheckboxModel;
use crocodicstudio\crudbooster\types\Image;
use crocodicstudio\crudbooster\types\image\ImageModel;
use crocodicstudio\crudbooster\types\Password;
use crocodicstudio\crudbooster\types\password\PasswordModel;
use crocodicstudio\crudbooster\types\text\TextModel;
use crocodicstudio\crudbooster\types\text_area\TextAreaModel;
use crocodicstudio\crudbooster\types\TextArea;
use crocodicstudio\crudbooster\types\UploadImage;
use crocodicstudio\crudbooster\types\Wysiwyg;
use crocodicstudio\crudbooster\types\wysiwyg\WysiwygModel;
use Illuminate\Support\Str;

trait ColumnsRegister
{
    use ColumnsBasic;

    public function addText($label, $name = null, $field_to_save = null)
    {
        $this->index++;

        $data = new TextModel();
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

        $data = new SelectModel();
        $data = $this->setDefaultModelValue($data);
        $data->setLabel($label);
        $data->setName($this->name($label,$name));
        $data->setField($field_to_save?:$this->name($label, $name));
        $data->setType("select");

        columnSingleton()->setColumn($this->index, $data);

        return (new Select($this->index));
    }


    public function addCustom($label, $name = null, $field_to_save = null)
    {
        $this->index++;

        $data = new CustomModel();
        $data = $this->setDefaultModelValue($data);
        $data->setLabel($label);
        $data->setName($this->name($label,$name));
        $data->setField($field_to_save?:$this->name($label, $name));
        $data->setType("custom");

        columnSingleton()->setColumn($this->index, $data);

        return (new Custom($this->index));
    }

    public function addDate($label, $name = null, $field_to_save = null)
    {
        $this->index++;

        $data = new DateModel();
        $data = $this->setDefaultModelValue($data);
        $data->setLabel($label);
        $data->setName($this->name($label,$name));
        $data->setField($field_to_save?:$this->name($label, $name));
        $data->setType("date");

        columnSingleton()->setColumn($this->index, $data);

        return (new Date($this->index));
    }

    public function addDateTime($label, $name = null, $field_to_save = null)
    {
        $this->index++;

        $data = new DatetimeModel();
        $data = $this->setDefaultModelValue($data);
        $data->setLabel($label);
        $data->setName($this->name($label,$name));
        $data->setField($field_to_save?:$this->name($label, $name));
        $data->setType("datetime");

        columnSingleton()->setColumn($this->index, $data);

        return (new Datetime($this->index));
    }

    public function addEmail($label, $name = null, $field_to_save = null)
    {
        $this->index++;

        $data = new EmailModel();
        $data = $this->setDefaultModelValue($data);
        $data->setLabel($label);
        $data->setName($this->name($label,$name));
        $data->setField($field_to_save?:$this->name($label, $name));
        $data->setType("email");

        columnSingleton()->setColumn($this->index, $data);

        return (new Email($this->index));
    }

    public function addFile($label, $name = null, $field_to_save = null)
    {
        $this->index++;

        $data = new FileModel();
        $data = $this->setDefaultModelValue($data);
        $data->setLabel($label);
        $data->setName($this->name($label,$name));
        $data->setField($field_to_save?:$this->name($label, $name));
        $data->setType("file");

        columnSingleton()->setColumn($this->index, $data);

        return (new File($this->index));
    }


    public function addHidden($label, $name = null, $field_to_save = null)
    {
        $this->index++;

        $data = new HiddenModel();
        $data = $this->setDefaultModelValue($data);
        $data->setLabel($label);
        $data->setName($this->name($label,$name));
        $data->setField($field_to_save?:$this->name($label, $name));
        $data->setType("hidden");

        columnSingleton()->setColumn($this->index, $data);

        return (new Hidden($this->index));
    }

    public function addNumber($label, $name = null, $field_to_save = null)
    {
        $this->index++;

        $data = new NumberModel();
        $data = $this->setDefaultModelValue($data);
        $data->setLabel($label);
        $data->setName($this->name($label,$name));
        $data->setField($field_to_save?:$this->name($label, $name));
        $data->setType("number");

        columnSingleton()->setColumn($this->index, $data);

        return (new Number($this->index));
    }

    public function addRadio($label, $name = null, $field_to_save = null)
    {
        $this->index++;

        $data = new RadioModel();
        $data = $this->setDefaultModelValue($data);
        $data->setLabel($label);
        $data->setName($this->name($label,$name));
        $data->setField($field_to_save?:$this->name($label, $name));
        $data->setType("radio");

        columnSingleton()->setColumn($this->index, $data);

        return (new Radio($this->index));
    }

    public function addWysiwyg($label, $name = null, $field_to_save = null)
    {
        $this->index++;

        $data = new WysiwygModel();
        $data = $this->setDefaultModelValue($data);
        $data->setLabel($label);
        $data->setName($this->name($label,$name));
        $data->setField($field_to_save?:$this->name($label, $name));
        $data->setType("wysiwyg");

        columnSingleton()->setColumn($this->index, $data);

        return (new Wysiwyg($this->index));
    }

}