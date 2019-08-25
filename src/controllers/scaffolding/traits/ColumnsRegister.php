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
use crocodicstudio\crudbooster\types\Money;
use crocodicstudio\crudbooster\types\money\MoneyModel;
use crocodicstudio\crudbooster\types\Number;
use crocodicstudio\crudbooster\types\number\NumberModel;
use crocodicstudio\crudbooster\types\Radio;
use crocodicstudio\crudbooster\types\radio\RadioModel;
use crocodicstudio\crudbooster\types\select_option\SelectOptionModel;
use crocodicstudio\crudbooster\types\SelectOption;
use crocodicstudio\crudbooster\types\SelectQuery;
use crocodicstudio\crudbooster\types\SelectTable;
use crocodicstudio\crudbooster\types\select_table\SelectTableModel;
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
use crocodicstudio\crudbooster\types\time\TimeModel;
use crocodicstudio\crudbooster\types\UploadImage;
use crocodicstudio\crudbooster\types\Wysiwyg;
use crocodicstudio\crudbooster\types\wysiwyg\WysiwygModel;
use Illuminate\Support\Str;

trait ColumnsRegister
{
    use ColumnsBasic;

    public function removeColumn($name)
    {
        columnSingleton()->removeColumn($name);
        return $this;
    }

    public function addText($label, $name = null, $field_to_save = null)
    {
        $this->index++;

        $data = new TextModel();
        $data = $this->setDefaultModelValue($data);
        $data->setLabel($label);
        $data->setName($this->name($label,$name));
        $data->setField($field_to_save?:$this->name($label, $name));
        $data->setOrderByColumn($this->table().".".$data->getField());
        $data->setFilterColumn($this->table().".".$data->getField());
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
        $data->setOrderByColumn($this->table().".".$data->getField());
        $data->setFilterColumn($this->table().".".$data->getField());
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
        $data->setOrderByColumn($this->table().".".$data->getField());
        $data->setFilterColumn($this->table().".".$data->getField());
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
        $data->setOrderByColumn($this->table().".".$data->getField());
        $data->setFilterColumn($this->table().".".$data->getField());
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
        $data->setOrderByColumn($this->table().".".$data->getField());
        $data->setFilterColumn($this->table().".".$data->getField());
        $data->setType("text_area");

        columnSingleton()->setColumn($this->index, $data);

        return (new TextArea($this->index));
    }

    /**
     * @param string $label
     * @param string|null $name
     * @param array $selectConfig (See bellow)
     *      $selectConfig = [
     *          'table'             => (string) the table name. Required.
     *          'value_option'      => (string) the column for value of option. Required.
     *          'display_option'    => (string) the column for display of option. Required.
     *          'sql_condition'     => (string) raw sql condition
     *      ]
     * @return SelectTable
     * @throws \Exception
     */
    public function addSelectTable($label, $name = null, $selectConfig)
    {
        if(isset($selectConfig['table']) &&
            isset($selectConfig['value_option']) &&
            isset($selectConfig['display_option'])) {
            $this->index++;

            $data = new SelectTableModel();
            $data = $this->setDefaultModelValue($data);
            $data->setLabel($label);
            $data->setName($this->name($label,$name));
            $data->setField($this->name($label, $name));
            $data->setType("select_table");
            $data->setOrderByColumn($selectConfig['table'].".".$selectConfig["display_option"]);
            $data->setFilterColumn($this->table().".".$data->getField());
            columnSingleton()->setColumn($this->index, $data);

            $selectTable = new SelectTable($this->index);
            $selectTable->optionsFromTable($selectConfig['table'],$selectConfig['value_option'],$selectConfig['display_option'],@$selectConfig['sql_condition']);

            return $selectTable;
        } else {
            throw new \Exception("addSelectTable `$label`: argument 3 needs table, value_option,display_option");
        }
    }

    public function addSelectOption($label, $name = null, $options = null)
    {
        $this->index++;

        $data = new SelectOptionModel();
        $data = $this->setDefaultModelValue($data);
        $data->setLabel($label);
        $data->setName($this->name($label,$name));
        $data->setField($this->name($label, $name));
        $data->setOrderByColumn($this->table().".".$data->getField());
        $data->setFilterColumn($this->table().".".$data->getField());
        $data->setType("select_option");

        columnSingleton()->setColumn($this->index, $data);

        $selectOption = new SelectOption($this->index);

        if($options) {
            $selectOption->options($options);
        }

        return $selectOption;
    }


    public function addCustom($label, $name = null, $field_to_save = null)
    {
        $this->index++;

        $data = new CustomModel();
        $data = $this->setDefaultModelValue($data);
        $data->setLabel($label);
        $data->setName($this->name($label,$name));
        $data->setField($field_to_save?:$this->name($label, $name));
        $data->setOrderByColumn($this->table().".".$data->getField());
        $data->setFilterColumn($this->table().".".$data->getField());
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
        $data->setOrderByColumn($this->table().".".$data->getField());
        $data->setFilterColumn($this->table().".".$data->getField());
        $data->setType("date");

        columnSingleton()->setColumn($this->index, $data);

        return (new Date($this->index));
    }

    public function addTime($label, $name = null, $field_to_save = null)
    {
        $this->index++;

        $data = new TimeModel();
        $data = $this->setDefaultModelValue($data);
        $data->setLabel($label);
        $data->setName($this->name($label,$name));
        $data->setField($field_to_save?:$this->name($label, $name));
        $data->setOrderByColumn($this->table().".".$data->getField());
        $data->setFilterColumn($this->table().".".$data->getField());
        $data->setType("time");

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
        $data->setOrderByColumn($this->table().".".$data->getField());
        $data->setFilterColumn($this->table().".".$data->getField());
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
        $data->setOrderByColumn($this->table().".".$data->getField());
        $data->setFilterColumn($this->table().".".$data->getField());
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
        $data->setOrderByColumn($this->table().".".$data->getField());
        $data->setFilterColumn($this->table().".".$data->getField());
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
        $data->setOrderByColumn($this->table().".".$data->getField());
        $data->setFilterColumn($this->table().".".$data->getField());
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
        $data->setOrderByColumn($this->table().".".$data->getField());
        $data->setFilterColumn($this->table().".".$data->getField());
        $data->setType("number");

        columnSingleton()->setColumn($this->index, $data);

        return (new Number($this->index));
    }

    public function addMoney($label, $name = null, $field_to_save = null)
    {
        $this->index++;

        $data = new MoneyModel();
        $data = $this->setDefaultModelValue($data);
        $data->setLabel($label);
        $data->setName($this->name($label,$name));
        $data->setField($field_to_save?:$this->name($label, $name));
        $data->setOrderByColumn($this->table().".".$data->getField());
        $data->setFilterColumn($this->table().".".$data->getField());
        $data->setType("money");

        columnSingleton()->setColumn($this->index, $data);

        return (new Money($this->index));
    }

    public function addRadio($label, $name = null, $field_to_save = null)
    {
        $this->index++;

        $data = new RadioModel();
        $data = $this->setDefaultModelValue($data);
        $data->setLabel($label);
        $data->setName($this->name($label,$name));
        $data->setField($field_to_save?:$this->name($label, $name));
        $data->setOrderByColumn($this->table().".".$data->getField());
        $data->setFilterColumn($this->table().".".$data->getField());
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
        $data->setOrderByColumn($this->table().".".$data->getField());
        $data->setFilterColumn($this->table().".".$data->getField());
        $data->setType("wysiwyg");

        columnSingleton()->setColumn($this->index, $data);

        return (new Wysiwyg($this->index));
    }

}