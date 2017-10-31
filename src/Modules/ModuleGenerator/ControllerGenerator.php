<?php

namespace crocodicstudio\crudbooster\Modules\ModuleGenerator;

use Schema;
use CRUDBooster;
use CB;

class ControllerGenerator
{
    public static function generateController($table, $name = null){

        $controllerName = self::getControllerName($table, $name);

        $coloms = CRUDBooster::getTableColumns($table);
        $pk = CB::pk($table);
        $formArrayString = self::generateFormConfig($table, $coloms);
        list($cols, $joinList) = self::addCol($table, $coloms, $pk);
        $php = '<?php '.view('CbModulesGen::controller_stub', compact('controllerName', 'table', 'pk', 'coloms', 'cols', 'formArrayString', 'joinList'))->render();
        //create file controller
        file_put_contents(base_path(controllers_dir()).'Admin'.$controllerName.'.php', $php);

        return 'Admin'.$controllerName;
    }
    /**
     * @param $table
     * @param $name
     * @return string
     */
    private static function getControllerName($table, $name)
    {
        $controllername = ucwords(str_replace('_', ' ', $table));
        $controllername = str_replace(' ', '', $controllername).'Controller';
        if ($name) {
            $controllername = ucwords(str_replace(['_', '-'], ' ', $name));
            $controllername = str_replace(' ', '', $controllername).'Controller';
        }

        $countSameFile = count(glob(base_path(controllers_dir()).'Admin'.$controllername.'.php'));

        if ($countSameFile != 0) {
            $suffix = $countSameFile;
            $controllername = ucwords(str_replace(['_', '-'], ' ', $name)).$suffix;
            $controllername = str_replace(' ', '', $controllername).'Controller';
        }

        return $controllername;
    }

    /**
     * @param $table
     * @param $coloms
     * @return string
     */
    private static function generateFormConfig($table, $coloms)
    {
        $formArrayString = [];
        foreach ($coloms as $i => $c) {
            //$attribute = [];
            $validation = [];
            $validation[] = 'required';
            $placeholder = '';
            $help = '';
            $options = [];

            $label = str_replace("id_", "", $c);
            $label = ucwords(str_replace("_", " ", $label));
            $field = $c;

            if (self::isExceptional($field)) {
                continue;
            }

            $typedata = CRUDBooster::getFieldType($table, $field);

            switch ($typedata) {
                default:
                case 'varchar':
                case 'char':
                    $type = "text";
                    $validation[] = "min:1|max:255";
                    break;
                case 'text':
                case 'longtext':
                    $type = 'textarea';
                    $validation[] = "string|min:5";
                    break;
                case 'date':
                    $type = 'date';
                    $validation[] = "date";
                    $options = [
                        'php_format' => 'M, d Y',
                        'datepicker_format' => 'M, dd YYYY',
                    ];
                    break;
                case 'datetime':
                case 'timestamp':
                    $type = 'datetime';
                    $validation[] = "date_format:Y-m-d H:i:s";
                    $options = [
                        'php_format' => 'M, d Y H:i',
                    ];
                    break;
                case 'time':
                    $type = 'time';
                    $validation[] = 'date_format:H:i:s';
                    break;
                case 'double':
                    $type = 'money';
                    $validation[] = "integer|min:0";
                    break;
                case 'int':
                case 'integer':
                    $type = 'number';
                    $validation[] = 'integer|min:0';
                    break;
            }

            if (self::isForeignKey($field)) {
                $jointable = str_replace(['id_', '_id'], '', $field);
                if (Schema::hasTable($jointable)) {
                    $joincols = CRUDBooster::getTableColumns($jointable);
                    $joinname = CRUDBooster::getNameTable($joincols);
                    $jointablePK = CB::pk($jointable);
                    $type = 'select2_datatable';
                    $options = [
                        "table" => $jointable,
                        "field_label" => $joinname,
                        "field_value" => $jointablePK,
                    ];
                }
            }

            if (substr($field, 0, 3) == 'is_') {
                $type = 'radio_dataenum';
                $label_field = ucwords(substr($field, 3));
                $validation = ['required|integer'];
                $options = [
                    "enum" => ["In ".$label_field, $label_field],
                    "value" => [0, 1],
                ];
            }

            if (self::isPassword($field)) {
                $type = 'password';
                $validation = ['min:3', 'max:32'];
                $help = trans("crudbooster.text_default_help_password");
            }

            if (self::isImage($field)) {
                $type = 'upload';
                $help = trans('crudbooster.text_default_help_upload');
                $validation = ['required|image'];
            }

            if (self::isGeographical($field)) {
                $type = 'hidden';
            }

            if (self::isPhone($field)) {
                $type = 'number';
                $validation = ['required', 'numeric'];
                $placeholder = trans('crudbooster.text_default_help_number');
            }

            if (self::isEmail($field)) {
                $type = 'email';
                $validation[] = 'email|unique:'.$table;
                $placeholder = trans('crudbooster.text_default_help_email');
            }

            if ($type == 'text' && self::isNameField($field)) {
                $placeholder = trans('crudbooster.text_default_help_text');
                $validation = ['required', 'string', 'min:3', 'max:70'];
            }

            if ($type == 'text' && self::isUrlField($field)) {
                $validation = ['required', 'url'];
                $placeholder = trans('crudbooster.text_default_help_url');
            }

            $validation = implode('|', $validation);

            $formArray = [];
            $formArray['label'] = $label;
            $formArray['name'] = $field;
            $formArray['type'] = $type;
            $formArray['options'] = $options;
            $formArray['required'] = true;
            $formArray['validation'] = $validation;
            $formArray['help'] = $help;
            $formArray['placeholder'] = $placeholder;
            $formArrayString[] = min_var_export($formArray, "            ");
        }

        return $formArrayString;
    }

    /**
     * @param $table
     * @param $coloms
     * @param $php
     * @param $pk
     * @return array
     */
    private static function addCol($table, $coloms, $pk)
    {
        $coloms_col = array_slice($coloms, 0, 8);
        $joinList = [];
        $cols = [];

        foreach ($coloms_col as $c) {
            $label = str_replace("id_", "", $c);
            $label = ucwords(str_replace("_", " ", $label));
            $label = str_replace('Cms ', '', $label);
            $field = $c;

            if (self::isExceptional($field) || self::isPassword($field)) {
                continue;
            }

            if (self::isForeignKey($field)) {
                $jointable = str_replace(['id_', '_id'], '', $field);

                if (Schema::hasTable($jointable)) {
                    $joincols = CRUDBooster::getTableColumns($jointable);
                    $joinname = CRUDBooster::getNameTable($joincols);
                    $cols[] = ['label' => $label, 'name' =>  $jointable.$joinname];
                    $jointablePK = CB::pk($jointable);
                    $joinList[] = [
                        'table' => $jointable,
                        'field1' => $jointable.'.'.$jointablePK,
                        'field2' => $table.'.'.$pk,
                    ];
                }
            } else {
                $image = '';
                if (self::isImage($field)) {
                    $image = '"image" => true';
                }
                $cols[] = ['label' => $label, 'name' =>  "'$field', $image"];
            }
        }

        return [$cols, $joinList];
    }

    /**
     * @param $field
     * @return bool
     */
    private static function isPassword($field)
    {
        return in_array($field, explode(',', cbConfig('PASSWORD_FIELDS_CANDIDATE')));
    }

    /**
     * @param $field
     * @return bool
     */
    private static function isEmail($field)
    {
        return in_array($field, explode(',', cbConfig('EMAIL_FIELDS_CANDIDATE')));
    }

    /**
     * @param $field
     * @return bool
     */
    private static function isPhone($field)
    {
        return in_array($field, explode(',', cbConfig('PHONE_FIELDS_CANDIDATE')));
    }

    /**
     * @param $field
     * @return bool
     */
    private static function isImage($field)
    {
        return in_array($field, explode(',', cbConfig('IMAGE_FIELDS_CANDIDATE')));
    }

    /**
     * @param $field
     * @return bool
     */
    private static function isExceptional($field)
    {
        return in_array($field, ['id', 'created_at', 'updated_at', 'deleted_at']);
    }

    /**
     * @param $field
     * @return bool
     */
    private static function isForeignKey($field)
    {
        return substr($field, 0, 3) == 'id_' || substr($field, -3) == '_id';
    }

    /**
     * @param $field
     * @return bool
     */
    private static function isGeographical($field)
    {
        return $field == 'latitude' || $field == 'longitude';
    }

    /**
     * @param $field
     * @return bool
     */
    private static function isNameField($field)
    {
        return in_array($field, explode(',', cbConfig('NAME_FIELDS_CANDIDATE')));
    }

    /**
     * @param $field
     * @return bool
     */
    private static function isUrlField($field)
    {
        return in_array($field, explode(',', cbConfig("URL_FIELDS_CANDIDATE")));
    }
}