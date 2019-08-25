<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 1/26/2019
 * Time: 6:00 PM
 */

namespace crocodicstudio\crudbooster\controllers\scaffolding\traits;

use crocodicstudio\crudbooster\controllers\scaffolding\singletons\ColumnSingleton;
use crocodicstudio\crudbooster\models\ColumnModel;

trait DefaultOption
{
    private $index;

    public function __construct($index = null)
    {
        $this->index = $index;
    }

    public function filterable($boolean = false, $help_info = null, $placeholder = null) {
        $data = ColumnSingleton()->getColumn($this->index);
        $data->setFilterable($boolean);
        $data->setFilterHelp($help_info);
        $data->setFilterPlaceholder($placeholder?:"Filter by ".$data->getLabel());
        // Save back
        columnSingleton()->setColumn($this->index, $data);

        return $this;
    }

    public function visible($boolean = true)
    {
        /** @var ColumnModel $data */
        $data = ColumnSingleton()->getColumn($this->index);
        $data->setVisible($boolean);

        // Save back
        columnSingleton()->setColumn($this->index, $data);

        return $this;
    }

    public function defaultValue($value)
    {
        /** @var ColumnModel $data */
        $data = ColumnSingleton()->getColumn($this->index);
        $data->setDefaultValue($value);

        // Save Back
        columnSingleton()->setColumn($this->index, $data);

        return $this;
    }

    /**
     * @param callable $transform
     */
    public function indexDisplayTransform(callable $transform) {
        $data = columnSingleton()->getColumn($this->index);
        /** @var ColumnModel $data */
        $data->setIndexDisplayTransform($transform);
        columnSingleton()->setColumn($this->index, $data);
        return $this;
    }

    /**
     * @param callable $transform
     */
    public function detailDisplayTransform(callable $transform) {
        $data = columnSingleton()->getColumn($this->index);
        /** @var ColumnModel $data */
        $data->setDetailDisplayTransform($transform);
        columnSingleton()->setColumn($this->index, $data);
        return $this;
    }

    public function inputWidth($width)
    {
        /** @var ColumnModel $data */
        $data = columnSingleton()->getColumn($this->index);
        $data->setInputWidth($width);

        // Save back
        columnSingleton()->setColumn($this->index, $data);

        return $this;
    }

    public function columnWidth($width)
    {
        /** @var ColumnModel $data */
        $data = ColumnSingleton()->getColumn($this->index);
        $data->setColumnWidth($width);

        // Save back
        columnSingleton()->setColumn($this->index, $data);

        return $this;
    }

    public function required($boolean = true)
    {
        /** @var ColumnModel $data */
        $data = ColumnSingleton()->getColumn($this->index);
        $data->setRequired($boolean);

        // Save back
        columnSingleton()->setColumn($this->index, $data);

        return $this;
    }

    public function help($helpText = null)
    {
        /** @var ColumnModel $data */
        $data = ColumnSingleton()->getColumn($this->index);
        $data->setHelp($helpText);

        // Save back
        columnSingleton()->setColumn($this->index, $data);

        return $this;
    }

    public function placeholder($placeholderText = null)
    {
        /** @var ColumnModel $data */
        $data = ColumnSingleton()->getColumn($this->index);
        $data->setPlaceholder($placeholderText);

        // Save back
        columnSingleton()->setColumn($this->index, $data);

        return $this;
    }

    public function readonly($boolean = true)
    {
        /** @var ColumnModel $data */
        $data = ColumnSingleton()->getColumn($this->index);
        $data->setReadonly($boolean);

        // Save back
        columnSingleton()->setColumn($this->index, $data);

        return $this;
    }

    public function disabled($boolean = true)
    {
        /** @var ColumnModel $data */
        $data = ColumnSingleton()->getColumn($this->index);
        $data->setDisabled($boolean);

        // Save back
        columnSingleton()->setColumn($this->index, $data);

        return $this;
    }

    public function onClick($js_function_name, callable $js_function = null)
    {
        /** @var ColumnModel $data */
        $data = ColumnSingleton()->getColumn($this->index);
        $data->setOnclickJsFunctionName($js_function_name);
        $data->setOnclickJsFunctionCallback($js_function);

        // Save back
        columnSingleton()->setColumn($this->index, $data);

        return $this;
    }

    public function onBlur($js_function_name, callable $js_function = null)
    {
        /** @var ColumnModel $data */
        $data = ColumnSingleton()->getColumn($this->index);
        $data->setOnblurJsFunctionName($js_function_name);
        $data->setOnblurJsFunctionCallback($js_function);

        // Save back
        columnSingleton()->setColumn($this->index, $data);

        return $this;
    }

    public function onChange($js_function_name, callable $js_function = null)
    {
        /** @var ColumnModel $data */
        $data = ColumnSingleton()->getColumn($this->index);
        $data->setOnchangeJsFunctionName($js_function_name);
        $data->setOnchangeJsFunctionCallback($js_function);

        // Save back
        columnSingleton()->setColumn($this->index, $data);

        return $this;
    }

    public function validation($rule = null, $custom_messages = [])
    {
        /** @var ColumnModel $data */
        $data = ColumnSingleton()->getColumn($this->index);
        $data->setValidation($rule);
        $data->setValidationMessages($custom_messages);

        // Save back
        columnSingleton()->setColumn($this->index, $data);

        return $this;
    }

    public function showIndex($show = true)
    {
        /** @var ColumnModel $data */
        $data = ColumnSingleton()->getColumn($this->index);
        $data->setShowIndex($show);

        // Save back
        columnSingleton()->setColumn($this->index, $data);

        return $this;
    }

    public function showEdit($show = true)
    {
        /** @var ColumnModel $data */
        $data = ColumnSingleton()->getColumn($this->index);
        $data->setShowEdit($show);

        // Save back
        columnSingleton()->setColumn($this->index, $data);

        return $this;
    }

    public function showAdd($show = true)
    {
        /** @var ColumnModel $data */
        $data = ColumnSingleton()->getColumn($this->index);
        $data->setShowAdd($show);

        // Save back
        columnSingleton()->setColumn($this->index, $data);

        return $this;
    }

    public function showDetail($show = true)
    {
        /** @var ColumnModel $data */
        $data = ColumnSingleton()->getColumn($this->index);
        $data->setShowDetail($show);

        // Save back
        columnSingleton()->setColumn($this->index, $data);

        return $this;
    }

}