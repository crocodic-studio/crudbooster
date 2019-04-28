<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 4/21/2019
 * Time: 8:59 PM
 */

namespace crocodicstudio\crudbooster\models;


class ColumnModel
{

    private $label;
    private $name;
    private $field;
    private $type;
    private $show_detail;
    private $show_index;
    private $show_edit;
    private $show_add;
    private $visible;
    private $input_width;
    private $column_width;
    private $required;
    private $readonly;
    private $disabled;
    private $help;
    private $placeholder;
    private $validation;
    private $validation_messages;
    private $value;
    private $style;

    /**
     * @return mixed
     */
    public function getDisabled()
    {
        return $this->disabled;
    }

    /**
     * @param mixed $disabled
     */
    public function setDisabled($disabled)
    {
        $this->disabled = $disabled;
    }


    /**
     * @return mixed
     */
    public function getReadonly()
    {
        return $this->readonly;
    }

    /**
     * @param mixed $readonly
     */
    public function setReadonly($readonly)
    {
        $this->readonly = $readonly;
    }


    /**
     * @return mixed
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * @param mixed $style
     */
    public function setStyle($style)
    {
        $this->style = $style;
    }


    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     * @return ColumnModel
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }


    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param mixed $label
     * @return ColumnModel
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return ColumnModel
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param mixed $field
     * @return ColumnModel
     */
    public function setField($field)
    {
        $this->field = $field;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     * @return ColumnModel
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getShowDetail()
    {
        return $this->show_detail;
    }

    /**
     * @param mixed $show_detail
     * @return ColumnModel
     */
    public function setShowDetail($show_detail)
    {
        $this->show_detail = $show_detail;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getShowIndex()
    {
        return $this->show_index;
    }

    /**
     * @param mixed $show_index
     * @return ColumnModel
     */
    public function setShowIndex($show_index)
    {
        $this->show_index = $show_index;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getShowEdit()
    {
        return $this->show_edit;
    }

    /**
     * @param mixed $show_edit
     * @return ColumnModel
     */
    public function setShowEdit($show_edit)
    {
        $this->show_edit = $show_edit;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getShowAdd()
    {
        return $this->show_add;
    }

    /**
     * @param mixed $show_add
     * @return ColumnModel
     */
    public function setShowAdd($show_add)
    {
        $this->show_add = $show_add;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * @param mixed $visible
     * @return ColumnModel
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getInputWidth()
    {
        return $this->input_width;
    }

    /**
     * @param mixed $input_width
     * @return ColumnModel
     */
    public function setInputWidth($input_width)
    {
        $this->input_width = $input_width;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getColumnWidth()
    {
        return $this->column_width;
    }

    /**
     * @param mixed $column_width
     * @return ColumnModel
     */
    public function setColumnWidth($column_width)
    {
        $this->column_width = $column_width;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * @param mixed $required
     * @return ColumnModel
     */
    public function setRequired($required)
    {
        $this->required = $required;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getHelp()
    {
        return $this->help;
    }

    /**
     * @param mixed $help
     * @return ColumnModel
     */
    public function setHelp($help)
    {
        $this->help = $help;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPlaceholder()
    {
        return $this->placeholder;
    }

    /**
     * @param mixed $placeholder
     * @return ColumnModel
     */
    public function setPlaceholder($placeholder)
    {
        $this->placeholder = $placeholder;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValidation()
    {
        return $this->validation;
    }

    /**
     * @param mixed $validation
     * @return ColumnModel
     */
    public function setValidation($validation)
    {
        $this->validation = $validation;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValidationMessages()
    {
        return $this->validation_messages;
    }

    /**
     * @param mixed $validation_messages
     * @return ColumnModel
     */
    public function setValidationMessages($validation_messages)
    {
        $this->validation_messages = $validation_messages;
        return $this;
    }


}